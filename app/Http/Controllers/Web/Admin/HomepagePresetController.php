<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\HomepagePreset;
use App\Models\HomeSection;
use App\Http\Controllers\Web\Admin\Panel\PanelController;
use Illuminate\Http\RedirectResponse;

class HomepagePresetController extends PanelController
{
    public function setup(): void
    {
        $this->xPanel->setModel(HomepagePreset::class);
        $this->xPanel->setRoute(admin_uri('homepage-presets'));
        $this->xPanel->setEntityNameStrings(
            trans('admin.homepage_preset'),
            trans('admin.homepage_presets')
        );
        $this->xPanel->enableReorder('name', 1);
        $this->xPanel->allowAccess(['reorder']);

        // Columns
        $this->xPanel->addColumn([
            'name'  => 'id',
            'label' => '',
            'type'  => 'checkbox',
            'orderable' => false,
        ]);
        $this->xPanel->addColumn([
            'name'  => 'name',
            'label' => trans('admin.Name'),
        ]);
        $this->xPanel->addColumn([
            'name'  => 'description',
            'label' => trans('admin.Description'),
        ]);
        $this->xPanel->addColumn([
            'name'          => 'is_default',
            'label'         => trans('admin.Default'),
            'type'          => 'model_function',
            'function_name' => 'getDefaultHtml',
        ]);
        $this->xPanel->addColumn([
            'name'          => 'active',
            'label'         => trans('admin.Active'),
            'type'          => 'model_function',
            'function_name' => 'getActiveHtml',
        ]);

        // Buttons
        $this->xPanel->addButtonFromModelFunction('line', 'apply_preset', 'applyPresetButton', 'beginning');

        // Fields
        $this->xPanel->addField([
            'name'       => 'name',
            'label'      => trans('admin.Name'),
            'type'       => 'text',
            'attributes' => ['placeholder' => trans('admin.Name')],
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->xPanel->addField([
            'name'       => 'slug',
            'label'      => trans('admin.Slug'),
            'type'       => 'text',
            'attributes' => ['placeholder' => trans('admin.Slug')],
            'wrapperAttributes' => ['class' => 'col-md-6'],
            'hint'       => trans('admin.slug_hint'),
        ]);
        $this->xPanel->addField([
            'name'       => 'description',
            'label'      => trans('admin.Description'),
            'type'       => 'textarea',
            'attributes' => ['rows' => 3],
        ]);
        $this->xPanel->addField([
            'name'  => 'separator_sections',
            'type'  => 'custom_html',
            'value' => '<h4 class="mt-4 mb-3">' . trans('admin.sections_configuration') . '</h4>',
        ]);
        $this->xPanel->addField([
            'name'    => 'sections_config',
            'label'   => trans('admin.sections_config'),
            'type'    => 'textarea',
            'attributes' => ['rows' => 10],
            'hint'    => trans('admin.sections_config_hint'),
        ]);
        $this->xPanel->addField([
            'name'  => 'separator_navbar',
            'type'  => 'custom_html',
            'value' => '<h4 class="mt-4 mb-3">' . trans('admin.navbar_configuration') . '</h4>',
        ]);
        $this->xPanel->addField([
            'name'    => 'navbar_config',
            'label'   => trans('admin.navbar_config'),
            'type'    => 'textarea',
            'attributes' => ['rows' => 6],
            'hint'    => trans('admin.navbar_config_hint'),
        ]);
        $this->xPanel->addField([
            'name'  => 'is_default',
            'label' => trans('admin.is_default'),
            'type'  => 'checkbox_switch',
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
        $this->xPanel->addField([
            'name'  => 'active',
            'label' => trans('admin.Active'),
            'type'  => 'checkbox_switch',
            'wrapperAttributes' => ['class' => 'col-md-6'],
        ]);
    }

    /**
     * Apply a preset to the homepage
     */
    public function apply(int $id): RedirectResponse
    {
        $preset = HomepagePreset::findOrFail($id);

        if ($preset->applyPreset()) {
            notification(trans('admin.preset_applied_successfully'), 'success');
        } else {
            notification(trans('admin.preset_apply_failed'), 'error');
        }

        return redirect()->back();
    }

    /**
     * Seed default presets
     */
    public function seedDefaults(): RedirectResponse
    {
        $presets = $this->getDefaultPresets();

        foreach ($presets as $preset) {
            HomepagePreset::updateOrCreate(
                ['slug' => $preset['slug']],
                $preset
            );
        }

        notification(trans('admin.default_presets_created'), 'success');

        return redirect()->back();
    }

    /**
     * Get default presets configuration
     */
    private function getDefaultPresets(): array
    {
        return [
            [
                'name' => 'Classic',
                'slug' => 'classic',
                'description' => 'Traditional job board layout with all sections',
                'sections_config' => [
                    'sections' => [
                        ['method' => 'getSearchForm', 'active' => true, 'lft' => 0],
                        ['method' => 'getLocations', 'active' => true, 'lft' => 2],
                        ['method' => 'getPremiumListings', 'active' => true, 'lft' => 4],
                        ['method' => 'getCategories', 'active' => true, 'lft' => 6],
                        ['method' => 'getLatestListings', 'active' => true, 'lft' => 8],
                        ['method' => 'getCompanies', 'active' => true, 'lft' => 10],
                        ['method' => 'getStats', 'active' => true, 'lft' => 12],
                    ],
                ],
                'navbar_config' => ['style' => 'default', 'sticky' => true],
                'is_default' => true,
                'active' => true,
            ],
            [
                'name' => 'Modern',
                'slug' => 'modern',
                'description' => 'Clean minimal design focused on search',
                'sections_config' => [
                    'sections' => [
                        ['method' => 'getSearchForm', 'active' => true, 'lft' => 0],
                        ['method' => 'getPremiumListings', 'active' => true, 'lft' => 2],
                        ['method' => 'getLatestListings', 'active' => true, 'lft' => 4],
                    ],
                ],
                'navbar_config' => ['style' => 'transparent', 'sticky' => true],
                'is_default' => false,
                'active' => true,
            ],
            [
                'name' => 'Corporate',
                'slug' => 'corporate',
                'description' => 'Business-focused layout with companies highlight',
                'sections_config' => [
                    'sections' => [
                        ['method' => 'getSearchForm', 'active' => true, 'lft' => 0],
                        ['method' => 'getCompanies', 'active' => true, 'lft' => 2],
                        ['method' => 'getPremiumListings', 'active' => true, 'lft' => 4],
                        ['method' => 'getCategories', 'active' => true, 'lft' => 6],
                        ['method' => 'getStats', 'active' => true, 'lft' => 8],
                    ],
                ],
                'navbar_config' => ['style' => 'dark', 'sticky' => true],
                'is_default' => false,
                'active' => true,
            ],
            [
                'name' => 'Startup',
                'slug' => 'startup',
                'description' => 'Tech/startup aesthetic with gradient navbar',
                'sections_config' => [
                    'sections' => [
                        ['method' => 'getSearchForm', 'active' => true, 'lft' => 0],
                        ['method' => 'getLatestListings', 'active' => true, 'lft' => 2],
                        ['method' => 'getCompanies', 'active' => true, 'lft' => 4],
                    ],
                ],
                'navbar_config' => ['style' => 'gradient', 'sticky' => true],
                'is_default' => false,
                'active' => true,
            ],
            [
                'name' => 'Minimal',
                'slug' => 'minimal',
                'description' => 'Essential sections only for fast loading',
                'sections_config' => [
                    'sections' => [
                        ['method' => 'getSearchForm', 'active' => true, 'lft' => 0],
                        ['method' => 'getLatestListings', 'active' => true, 'lft' => 2],
                    ],
                ],
                'navbar_config' => ['style' => 'minimal', 'sticky' => false],
                'is_default' => false,
                'active' => true,
            ],
            [
                'name' => 'Full Featured',
                'slug' => 'full-featured',
                'description' => 'All sections enabled for maximum content',
                'sections_config' => [
                    'sections' => [
                        ['method' => 'getSearchForm', 'active' => true, 'lft' => 0],
                        ['method' => 'getLocations', 'active' => true, 'lft' => 2],
                        ['method' => 'getPremiumListings', 'active' => true, 'lft' => 4],
                        ['method' => 'getCategories', 'active' => true, 'lft' => 6],
                        ['method' => 'getLatestListings', 'active' => true, 'lft' => 8],
                        ['method' => 'getCompanies', 'active' => true, 'lft' => 10],
                        ['method' => 'getStats', 'active' => true, 'lft' => 12],
                        ['method' => 'getTextArea', 'active' => true, 'lft' => 14],
                    ],
                ],
                'navbar_config' => ['style' => 'default', 'sticky' => true],
                'is_default' => false,
                'active' => true,
            ],
        ];
    }
}
