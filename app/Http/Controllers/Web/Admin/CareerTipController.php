<?php

namespace App\Http\Controllers\Web\Admin;

use App\Models\CareerTip;
use App\Http\Controllers\Web\Admin\Panel\PanelController;
use App\Http\Requests\Admin\CareerTipRequest;

class CareerTipController extends PanelController
{
    public function setup()
    {
        $this->xPanel->setModel(CareerTip::class);
        $this->xPanel->setRoute(admin_uri('career-tips'));
        $this->xPanel->setEntityNameStrings('career tip', 'career tips');
        $this->xPanel->orderBy('created_at', 'desc');
        $this->xPanel->addButtonFromModelFunction('top', 'seed_tips', 'seedTipsButton', 'beginning');

        // Filters
        $this->xPanel->addFilter(
            [
                'name' => 'category',
                'type' => 'dropdown',
                'label' => 'Category',
            ],
            CareerTip::CATEGORIES,
            function ($value) {
                $this->xPanel->addClause('where', 'category', $value);
            }
        );

        $this->xPanel->addFilter(
            [
                'name' => 'is_featured',
                'type' => 'dropdown',
                'label' => 'Featured',
            ],
            [
                1 => 'Featured',
                0 => 'Not Featured',
            ],
            function ($value) {
                $this->xPanel->addClause('where', 'is_featured', $value);
            }
        );

        // Columns
        $this->xPanel->addColumn([
            'name' => 'id',
            'label' => 'ID',
            'type' => 'number',
        ]);

        $this->xPanel->addColumn([
            'name' => 'title',
            'label' => 'Title',
            'type' => 'text',
        ]);

        $this->xPanel->addColumn([
            'name' => 'category',
            'label' => 'Category',
            'type' => 'text',
            'value' => function ($entry) {
                return $entry->category_label;
            },
        ]);

        $this->xPanel->addColumn([
            'name' => 'is_featured',
            'label' => 'Featured',
            'type' => 'boolean',
        ]);

        $this->xPanel->addColumn([
            'name' => 'views',
            'label' => 'Views',
            'type' => 'number',
        ]);

        $this->xPanel->addColumn([
            'name' => 'active',
            'label' => 'Active',
            'type' => 'boolean',
        ]);

        $this->xPanel->addColumn([
            'name' => 'created_at',
            'label' => 'Created',
            'type' => 'datetime',
        ]);

        // Fields
        $this->xPanel->addField([
            'name' => 'title',
            'label' => 'Title',
            'type' => 'text',
            'attributes' => [
                'placeholder' => 'Enter the article title',
            ],
        ]);

        $this->xPanel->addField([
            'name' => 'slug',
            'label' => 'Slug (URL)',
            'type' => 'text',
            'hint' => 'Leave empty to auto-generate from title',
            'attributes' => [
                'placeholder' => 'custom-url-slug',
            ],
        ]);

        $this->xPanel->addField([
            'name' => 'category',
            'label' => 'Category',
            'type' => 'select_from_array',
            'options' => CareerTip::CATEGORIES,
            'allows_null' => false,
        ]);

        $this->xPanel->addField([
            'name' => 'excerpt',
            'label' => 'Excerpt',
            'type' => 'textarea',
            'hint' => 'A short summary of the article (displayed in listings)',
            'attributes' => [
                'rows' => 3,
            ],
        ]);

        $this->xPanel->addField([
            'name' => 'content',
            'label' => 'Content',
            'type' => 'wysiwyg',
            'hint' => 'The full article content',
        ]);

        $this->xPanel->addField([
            'name' => 'featured_image',
            'label' => 'Featured Image',
            'type' => 'text',
            'hint' => 'Path to image in storage (e.g., career-tips/image.jpg)',
        ]);

        $this->xPanel->addField([
            'name' => 'reading_time',
            'label' => 'Reading Time (minutes)',
            'type' => 'number',
            'default' => 5,
        ]);

        $this->xPanel->addField([
            'name' => 'is_featured',
            'label' => 'Featured Article',
            'type' => 'checkbox',
            'hint' => 'Featured articles appear prominently on the career resources page',
        ]);

        $this->xPanel->addField([
            'name' => 'active',
            'label' => 'Active',
            'type' => 'checkbox',
            'default' => true,
        ]);
    }

    public function store(CareerTipRequest $request)
    {
        return parent::storeCrud($request);
    }

    public function update(CareerTipRequest $request)
    {
        return parent::updateCrud($request);
    }
}
