<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostReaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostReactionController extends Controller
{
    /**
     * Toggle a reaction on a post
     */
    public function toggle(Request $request, int $postId): JsonResponse
    {
        $request->validate([
            'reaction_type' => 'required|string|in:like,love,celebrate,insightful,curious',
        ]);

        $post = Post::findOrFail($postId);
        $reactionType = $request->input('reaction_type');
        $userId = auth()->check() ? auth()->user()->id : null;
        $ipAddress = $request->ip();

        // Find existing reaction
        $existingReaction = PostReaction::where('post_id', $postId)
            ->where(function ($query) use ($userId, $ipAddress) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('ip_address', $ipAddress);
                }
            })
            ->first();

        $action = 'added';

        if ($existingReaction) {
            if ($existingReaction->reaction_type === $reactionType) {
                // Same reaction type - remove it (toggle off)
                $existingReaction->delete();
                $action = 'removed';
            } else {
                // Different reaction type - update it
                $existingReaction->update(['reaction_type' => $reactionType]);
                $action = 'changed';
            }
        } else {
            // Create new reaction
            PostReaction::create([
                'post_id' => $postId,
                'user_id' => $userId,
                'ip_address' => $userId ? null : $ipAddress,
                'reaction_type' => $reactionType,
            ]);
        }

        // Get updated reaction counts
        $reactionCounts = $this->getReactionCounts($postId);
        $totalReactions = array_sum($reactionCounts);
        $userReaction = $action !== 'removed' ? $reactionType : null;

        return response()->json([
            'success' => true,
            'action' => $action,
            'reaction_counts' => $reactionCounts,
            'total_reactions' => $totalReactions,
            'user_reaction' => $userReaction,
        ]);
    }

    /**
     * Get reactions for a post
     */
    public function show(int $postId): JsonResponse
    {
        $post = Post::findOrFail($postId);
        $userId = auth()->check() ? auth()->user()->id : null;
        $ipAddress = request()->ip();

        $reactionCounts = $this->getReactionCounts($postId);
        $totalReactions = array_sum($reactionCounts);

        // Get user's current reaction
        $userReaction = PostReaction::where('post_id', $postId)
            ->where(function ($query) use ($userId, $ipAddress) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('ip_address', $ipAddress);
                }
            })
            ->first();

        return response()->json([
            'success' => true,
            'reaction_counts' => $reactionCounts,
            'total_reactions' => $totalReactions,
            'user_reaction' => $userReaction ? $userReaction->reaction_type : null,
            'views' => $post->visits ?? 0,
        ]);
    }

    /**
     * Get reaction counts for a post grouped by type
     */
    private function getReactionCounts(int $postId): array
    {
        $counts = PostReaction::where('post_id', $postId)
            ->selectRaw('reaction_type, count(*) as count')
            ->groupBy('reaction_type')
            ->pluck('count', 'reaction_type')
            ->toArray();

        // Ensure all reaction types have a count (even if 0)
        $allTypes = array_keys(PostReaction::REACTION_TYPES);
        foreach ($allTypes as $type) {
            if (!isset($counts[$type])) {
                $counts[$type] = 0;
            }
        }

        return $counts;
    }

    /**
     * Get reactions for multiple posts (batch request)
     */
    public function batch(Request $request): JsonResponse
    {
        $request->validate([
            'post_ids' => 'required|array',
            'post_ids.*' => 'integer',
        ]);

        $postIds = $request->input('post_ids');
        $userId = auth()->check() ? auth()->user()->id : null;
        $ipAddress = $request->ip();

        $result = [];

        foreach ($postIds as $postId) {
            $reactionCounts = $this->getReactionCounts($postId);
            $totalReactions = array_sum($reactionCounts);

            // Get user's current reaction for this post
            $userReaction = PostReaction::where('post_id', $postId)
                ->where(function ($query) use ($userId, $ipAddress) {
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } else {
                        $query->where('ip_address', $ipAddress);
                    }
                })
                ->first();

            // Get views count
            $views = Post::where('id', $postId)->value('visits') ?? 0;

            $result[$postId] = [
                'reaction_counts' => $reactionCounts,
                'total_reactions' => $totalReactions,
                'user_reaction' => $userReaction ? $userReaction->reaction_type : null,
                'views' => $views,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
