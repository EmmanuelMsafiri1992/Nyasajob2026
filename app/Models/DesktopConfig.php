<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DesktopConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'desktop_icons',
        'taskbar_apps',
        'start_menu_apps',
        'initial_windows',
        'filesystem',
        'wallpaper',
        'mode',
        'show_taskbar',
        'show_start_menu',
        'show_desktop_icons',
        'allow_window_resize',
        'allow_window_move',
        'disabled_apps',
        'hidden_elements',
    ];

    protected $casts = [
        'desktop_icons' => 'array',
        'taskbar_apps' => 'array',
        'start_menu_apps' => 'array',
        'initial_windows' => 'array',
        'filesystem' => 'array',
        'show_taskbar' => 'boolean',
        'show_start_menu' => 'boolean',
        'show_desktop_icons' => 'boolean',
        'allow_window_resize' => 'boolean',
        'allow_window_move' => 'boolean',
        'disabled_apps' => 'array',
        'hidden_elements' => 'array',
    ];

    /**
     * Available modes
     */
    public const MODES = [
        'guided' => 'Guided Mode',
        'free' => 'Free Exploration',
        'both' => 'Both Modes',
    ];

    /**
     * Default desktop icons
     */
    public const DEFAULT_DESKTOP_ICONS = [
        ['id' => 'this-pc', 'name' => 'This PC', 'icon' => 'computer', 'app' => 'file-explorer', 'path' => 'C:'],
        ['id' => 'recycle-bin', 'name' => 'Recycle Bin', 'icon' => 'trash', 'app' => 'recycle-bin'],
        ['id' => 'documents', 'name' => 'Documents', 'icon' => 'folder', 'app' => 'file-explorer', 'path' => 'C:/Users/Learner/Documents'],
    ];

    /**
     * Default taskbar apps
     */
    public const DEFAULT_TASKBAR_APPS = [
        ['id' => 'file-explorer', 'name' => 'File Explorer', 'icon' => 'folder'],
        ['id' => 'edge', 'name' => 'Microsoft Edge', 'icon' => 'globe'],
        ['id' => 'notepad', 'name' => 'Notepad', 'icon' => 'file-text'],
    ];

    /**
     * All available apps
     */
    public const AVAILABLE_APPS = [
        'file-explorer' => ['name' => 'File Explorer', 'icon' => 'folder', 'type' => 'system'],
        'notepad' => ['name' => 'Notepad', 'icon' => 'file-text', 'type' => 'accessory'],
        'calculator' => ['name' => 'Calculator', 'icon' => 'calculator', 'type' => 'accessory'],
        'edge' => ['name' => 'Microsoft Edge', 'icon' => 'globe', 'type' => 'browser'],
        'cmd' => ['name' => 'Command Prompt', 'icon' => 'terminal', 'type' => 'system'],
        'settings' => ['name' => 'Settings', 'icon' => 'gear', 'type' => 'system'],
        'recycle-bin' => ['name' => 'Recycle Bin', 'icon' => 'trash', 'type' => 'system'],
        'photos' => ['name' => 'Photos', 'icon' => 'image', 'type' => 'app'],
        'paint' => ['name' => 'Paint', 'icon' => 'palette', 'type' => 'accessory'],
    ];

    /**
     * Default filesystem structure
     */
    public const DEFAULT_FILESYSTEM = [
        'C:' => [
            'type' => 'drive',
            'name' => 'Local Disk (C:)',
            'children' => [
                'Users' => [
                    'type' => 'folder',
                    'children' => [
                        'Learner' => [
                            'type' => 'folder',
                            'children' => [
                                'Desktop' => ['type' => 'folder', 'children' => []],
                                'Documents' => ['type' => 'folder', 'children' => []],
                                'Downloads' => ['type' => 'folder', 'children' => []],
                                'Pictures' => ['type' => 'folder', 'children' => []],
                                'Music' => ['type' => 'folder', 'children' => []],
                                'Videos' => ['type' => 'folder', 'children' => []],
                            ],
                        ],
                    ],
                ],
                'Program Files' => ['type' => 'folder', 'children' => []],
                'Windows' => ['type' => 'folder', 'children' => []],
            ],
        ],
    ];

    /**
     * Get the lesson this config belongs to
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseLesson::class, 'lesson_id');
    }

    /**
     * Get desktop icons with defaults
     */
    public function getDesktopIconsWithDefaults(): array
    {
        return $this->desktop_icons ?? self::DEFAULT_DESKTOP_ICONS;
    }

    /**
     * Get taskbar apps with defaults
     */
    public function getTaskbarAppsWithDefaults(): array
    {
        return $this->taskbar_apps ?? self::DEFAULT_TASKBAR_APPS;
    }

    /**
     * Get filesystem with defaults
     */
    public function getFilesystemWithDefaults(): array
    {
        return $this->filesystem ?? self::DEFAULT_FILESYSTEM;
    }

    /**
     * Check if an app is enabled
     */
    public function isAppEnabled(string $appId): bool
    {
        $disabled = $this->disabled_apps ?? [];
        return !in_array($appId, $disabled);
    }

    /**
     * Get mode label
     */
    public function getModeLabelAttribute(): string
    {
        return self::MODES[$this->mode] ?? $this->mode;
    }

    /**
     * Create default config for a lesson
     */
    public static function createDefault(int $lessonId): self
    {
        return static::create([
            'lesson_id' => $lessonId,
            'desktop_icons' => self::DEFAULT_DESKTOP_ICONS,
            'taskbar_apps' => self::DEFAULT_TASKBAR_APPS,
            'start_menu_apps' => array_keys(self::AVAILABLE_APPS),
            'filesystem' => self::DEFAULT_FILESYSTEM,
            'mode' => 'guided',
            'show_taskbar' => true,
            'show_start_menu' => true,
            'show_desktop_icons' => true,
            'allow_window_resize' => true,
            'allow_window_move' => true,
        ]);
    }

    /**
     * Get configuration as JSON for frontend
     */
    public function toFrontendConfig(): array
    {
        return [
            'desktopIcons' => $this->getDesktopIconsWithDefaults(),
            'taskbarApps' => $this->getTaskbarAppsWithDefaults(),
            'startMenuApps' => $this->start_menu_apps ?? array_keys(self::AVAILABLE_APPS),
            'initialWindows' => $this->initial_windows ?? [],
            'filesystem' => $this->getFilesystemWithDefaults(),
            'wallpaper' => $this->wallpaper,
            'mode' => $this->mode,
            'showTaskbar' => $this->show_taskbar,
            'showStartMenu' => $this->show_start_menu,
            'showDesktopIcons' => $this->show_desktop_icons,
            'allowWindowResize' => $this->allow_window_resize,
            'allowWindowMove' => $this->allow_window_move,
            'disabledApps' => $this->disabled_apps ?? [],
            'hiddenElements' => $this->hidden_elements ?? [],
            'availableApps' => self::AVAILABLE_APPS,
        ];
    }
}
