<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Files\Upload;
use App\Models\Course;
use App\Models\User;
use App\Http\Controllers\Admin\Panel\PanelController;
use App\Http\Requests\Admin\CourseRequest as StoreRequest;
use App\Http\Requests\Admin\CourseRequest as UpdateRequest;

class CourseController extends PanelController
{
    public function setup()
    {
        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->xPanel->setModel('App\Models\Course');
        $this->xPanel->with(['instructor']);
        $this->xPanel->setRoute(admin_uri('courses'));
        $this->xPanel->setEntityNameStrings(trans('admin.course'), trans('admin.courses'));

        if (!request()->input('order')) {
            $this->xPanel->orderBy('created_at', 'DESC');
        }

        $this->xPanel->allowAccess(['create', 'update', 'delete', 'list']);

        // Buttons
        $this->xPanel->addButtonFromModelFunction('top', 'bulk_activation_btn', 'bulkActivationBtn', 'end');
        $this->xPanel->addButtonFromModelFunction('top', 'bulk_deactivation_btn', 'bulkDeactivationBtn', 'end');
        $this->xPanel->addButtonFromModelFunction('top', 'bulk_deletion_btn', 'bulkDeletionBtn', 'end');

        // Custom button for modules
        $this->xPanel->addButtonFromModelFunction('line', 'modules', 'modulesBtn', 'beginning');

        /*
        |--------------------------------------------------------------------------
        | FILTERS
        |--------------------------------------------------------------------------
        */
        $this->xPanel->disableSearchBar();

        $this->xPanel->addFilter([
            'name'  => 'title',
            'type'  => 'text',
            'label' => trans('admin.Title'),
        ],
            false,
            function ($value) {
                $this->xPanel->addClause('where', 'title', 'LIKE', "%$value%");
            });

        $this->xPanel->addFilter([
            'name'  => 'level',
            'type'  => 'dropdown',
            'label' => trans('admin.Level'),
        ], [
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
        ], function ($value) {
            $this->xPanel->addClause('where', 'level', '=', $value);
        });

        $this->xPanel->addFilter([
            'name'  => 'is_free',
            'type'  => 'dropdown',
            'label' => 'Pricing',
        ], [
            1 => 'Free',
            0 => 'Paid',
        ], function ($value) {
            $this->xPanel->addClause('where', 'is_free', '=', $value);
        });

        $this->xPanel->addFilter([
            'name'  => 'is_published',
            'type'  => 'dropdown',
            'label' => trans('admin.Status'),
        ], [
            1 => 'Published',
            0 => 'Draft',
        ], function ($value) {
            $this->xPanel->addClause('where', 'is_published', '=', $value);
        });

        /*
        |--------------------------------------------------------------------------
        | COLUMNS
        |--------------------------------------------------------------------------
        */
        $this->xPanel->addColumn([
            'name'      => 'id',
            'label'     => '',
            'type'      => 'checkbox',
            'orderable' => false,
        ]);

        $this->xPanel->addColumn([
            'name'  => 'title',
            'label' => trans('admin.Title'),
        ]);

        $this->xPanel->addColumn([
            'name'          => 'level',
            'label'         => trans('admin.Level'),
            'type'          => 'model_function',
            'function_name' => 'getLevelBadge',
        ]);

        $this->xPanel->addColumn([
            'name'          => 'is_free',
            'label'         => 'Pricing',
            'type'          => 'model_function',
            'function_name' => 'getPricingBadge',
        ]);

        $this->xPanel->addColumn([
            'name'  => 'enrollment_count',
            'label' => 'Enrollments',
            'type'  => 'number',
        ]);

        $this->xPanel->addColumn([
            'name'          => 'is_published',
            'label'         => trans('admin.Status'),
            'type'          => 'model_function',
            'function_name' => 'getPublishedBadge',
        ]);

        /*
        |--------------------------------------------------------------------------
        | FIELDS
        |--------------------------------------------------------------------------
        */
        $this->xPanel->addField([
            'name'              => 'title',
            'label'             => trans('admin.Title'),
            'type'              => 'text',
            'attributes'        => [
                'placeholder' => 'Enter course title',
            ],
            'wrapperAttributes' => [
                'class' => 'col-md-8',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'slug',
            'label'             => trans('admin.Slug'),
            'type'              => 'text',
            'attributes'        => [
                'placeholder' => 'Will be auto-generated if left empty',
            ],
            'hint'              => 'URL-friendly identifier. Auto-generated from title if empty.',
            'wrapperAttributes' => [
                'class' => 'col-md-4',
            ],
        ]);

        $this->xPanel->addField([
            'name'       => 'description',
            'label'      => trans('admin.Description'),
            'type'       => 'textarea',
            'attributes' => [
                'rows' => 4,
                'placeholder' => 'Brief description of the course',
            ],
        ]);

        $this->xPanel->addField([
            'name'       => 'objectives',
            'label'      => 'Learning Objectives',
            'type'       => 'textarea',
            'attributes' => [
                'rows' => 3,
                'placeholder' => 'What will students learn? (one objective per line)',
            ],
            'hint'       => 'Enter learning objectives, one per line',
        ]);

        $this->xPanel->addField([
            'name'   => 'thumbnail',
            'label'  => 'Course Thumbnail',
            'type'   => 'image',
            'upload' => true,
            'disk'   => 'public',
            'hint'   => 'Recommended size: 800x450 pixels',
        ]);

        $this->xPanel->addField([
            'name'  => 'separator1',
            'type'  => 'custom_html',
            'value' => '<hr><h4>Pricing & Settings</h4>',
        ]);

        $this->xPanel->addField([
            'name'              => 'is_free',
            'label'             => 'Free Course',
            'type'              => 'checkbox_switch',
            'default'           => 1,
            'hint'              => 'Toggle off to set a price for this course',
            'wrapperAttributes' => [
                'class' => 'col-md-4',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'price',
            'label'             => 'Price',
            'type'              => 'number',
            'attributes'        => [
                'step' => '0.01',
                'min' => '0',
                'placeholder' => '0.00',
            ],
            'prefix'            => config('settings.app.currency_symbol', '$'),
            'hint'              => 'Set to 0 for free courses',
            'wrapperAttributes' => [
                'class' => 'col-md-4',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'level',
            'label'             => trans('admin.Level'),
            'type'              => 'select2_from_array',
            'options'           => [
                'beginner' => 'Beginner',
                'intermediate' => 'Intermediate',
                'advanced' => 'Advanced',
            ],
            'allows_null'       => false,
            'default'           => 'beginner',
            'wrapperAttributes' => [
                'class' => 'col-md-4',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'duration_hours',
            'label'             => 'Duration (hours)',
            'type'              => 'number',
            'attributes'        => [
                'min' => '0',
                'placeholder' => 'Estimated hours to complete',
            ],
            'wrapperAttributes' => [
                'class' => 'col-md-4',
            ],
        ]);

        // Get instructors (users who can teach)
        $instructors = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['super-admin', 'admin', 'instructor']);
        })->orWhere('id', 1)->pluck('name', 'id')->toArray();

        $this->xPanel->addField([
            'name'              => 'instructor_id',
            'label'             => 'Instructor',
            'type'              => 'select2_from_array',
            'options'           => ['' => '-- Select Instructor --'] + $instructors,
            'allows_null'       => true,
            'wrapperAttributes' => [
                'class' => 'col-md-4',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'is_published',
            'label'             => 'Published',
            'type'              => 'checkbox_switch',
            'default'           => 0,
            'hint'              => 'Publish this course to make it visible to students',
            'wrapperAttributes' => [
                'class' => 'col-md-4',
            ],
        ]);
    }

    public function store(StoreRequest $request)
    {
        $request = $this->uploadThumbnail($request);
        $request = $this->generateSlug($request);

        return parent::storeCrud($request);
    }

    public function update(UpdateRequest $request)
    {
        $request = $this->uploadThumbnail($request);
        $request = $this->generateSlug($request);

        return parent::updateCrud($request);
    }

    private function uploadThumbnail($request)
    {
        $attribute = 'thumbnail';
        $destPath = 'app/courses/thumbnails';
        $file = $request->hasFile($attribute) ? $request->file($attribute) : $request->input($attribute);

        if ($file) {
            $request->request->set($attribute, Upload::image($destPath, $file, null, true));
        } else {
            $request->request->remove($attribute);
        }

        return $request;
    }

    private function generateSlug($request)
    {
        if (empty($request->input('slug'))) {
            $slug = \Str::slug($request->input('title'));

            // Check for uniqueness
            $count = Course::where('slug', 'LIKE', $slug . '%')
                ->when($this->xPanel->getCurrentEntryId(), function ($query, $id) {
                    return $query->where('id', '!=', $id);
                })
                ->count();

            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }

            $request->request->set('slug', $slug);
        }

        return $request;
    }
}
