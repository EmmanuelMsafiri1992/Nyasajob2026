<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkerProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        if ($this->resource instanceof \Illuminate\Http\Resources\MissingValue) {
            return [];
        }

        $authUser = auth('sanctum')->user();
        $embed = explode(',', request()->input('embed', ''));

        // Base public info (always shown)
        $entity = [
            'id' => $this->id,
            'title' => $this->title,
            'bio' => $this->bio,
            'district' => $this->district,
            'availability_status' => $this->availability_status,
            'availability_status_formatted' => $this->availability_status_formatted,
            'availability_badge_class' => $this->availability_badge_class,
            'experience_years' => $this->experience_years,
            'hourly_rate' => $this->hourly_rate,
            'currency_code' => $this->currency_code,
            'gender' => $this->gender,
            'photo_url' => $this->photo_url,
            'skills_list' => $this->skills_list,
            'views' => $this->views,
            'is_featured' => !empty($this->featured_at),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Include related data if embedded
        if (in_array('skills', $embed)) {
            $entity['skills'] = WorkerSkillResource::collection($this->whenLoaded('skills'));
        }

        if (in_array('city', $embed)) {
            $entity['city'] = new CityResource($this->whenLoaded('city'));
        }

        if (in_array('country', $embed)) {
            $entity['country'] = new CountryResource($this->whenLoaded('country'));
        }

        if (in_array('user', $embed)) {
            $entity['user'] = [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'photo_url' => $this->user->photo_url ?? null,
            ];
        }

        // Contact details - only for authorized viewers
        $contactDetails = $this->resource->getContactDetailsFor($authUser);
        $entity['can_view_contact'] = $contactDetails['can_view'];

        if ($contactDetails['can_view']) {
            $entity['phone'] = $contactDetails['phone'];
            $entity['email'] = $contactDetails['email'];
            $entity['whatsapp'] = $contactDetails['whatsapp'];
        }

        // For profile owner, include additional fields
        if (!empty($authUser) && $authUser->id === $this->user_id) {
            $entity['is_public'] = $this->is_public;
            $entity['custom_skills'] = $this->custom_skills;
            $entity['date_of_birth'] = $this->date_of_birth;
            $entity['country_code'] = $this->country_code;
            $entity['city_id'] = $this->city_id;
        }

        return $entity;
    }
}
