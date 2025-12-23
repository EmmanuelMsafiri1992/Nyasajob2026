<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\CourseModule;
use App\Http\Controllers\Admin\Panel\PanelController;
use App\Http\Requests\Admin\CourseModuleRequest as StoreRequest;
use App\Http\Requests\Admin\CourseModuleRequest as UpdateRequest;

class CourseModuleController extends PanelController
{
    public $courseId = null;
    public $course = null;

    public function setup()
    {
        // Get the Course ID from the URL
        $this->courseId = request()->segment(3);
        $this->course = Course::find($this->courseId);

        if (!$this->course) {
            abort(404, 'Course not found');
        }

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->xPanel->setModel('App\Models\CourseModule');
        $this->xPanel->with(['lessons']);
        $this->xPanel->setRoute(admin_uri('courses/' . $this->courseId . '/modules'));
        $this->xPanel->setEntityNameStrings(
            'Module &rarr; <strong>' . $this->course->title . '</strong>',
            'Modules &rarr; <strong>' . $this->course->title . '</strong>'
        );

        // Enable parent navigation
        $this->xPanel->enableParentEntity();
        $this->xPanel->setParentKeyField('course_id');
        $this->xPanel->addClause('where', 'course_id', '=', $this->courseId);
        $this->xPanel->setParentRoute(admin_uri('courses'));
        $this->xPanel->setParentEntityNameStrings('Course', 'Courses');

        $this->xPanel->allowAccess(['reorder', 'parent', 'create', 'update', 'delete', 'list']);
        $this->xPanel->enableReorder('title', 1);

        if (!request()->input('order')) {
            $this->xPanel->orderBy('order', 'ASC');
        }

        // Buttons
        $this->xPanel->addButtonFromModelFunction('top', 'bulk_deletion_btn', 'bulkDeletionBtn', 'end');
        $this->xPanel->addButtonFromModelFunction('line', 'lessons', 'lessonsBtn', 'beginning');

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
            'name'  => 'order',
            'label' => 'Order',
            'type'  => 'number',
        ]);

        $this->xPanel->addColumn([
            'name'  => 'title',
            'label' => trans('admin.Title'),
        ]);

        $this->xPanel->addColumn([
            'name'          => 'lessons_count',
            'label'         => 'Lessons',
            'type'          => 'model_function',
            'function_name' => 'getLessonsCountBadge',
        ]);

        /*
        |--------------------------------------------------------------------------
        | FIELDS
        |--------------------------------------------------------------------------
        */
        $this->xPanel->addField([
            'name'  => 'course_id',
            'type'  => 'hidden',
            'value' => $this->courseId,
        ]);

        $this->xPanel->addField([
            'name'       => 'title',
            'label'      => 'Module Title',
            'type'       => 'text',
            'attributes' => [
                'placeholder' => 'e.g., Getting Started with Windows',
            ],
        ]);

        $this->xPanel->addField([
            'name'       => 'description',
            'label'      => trans('admin.Description'),
            'type'       => 'textarea',
            'attributes' => [
                'rows' => 3,
                'placeholder' => 'Brief description of what this module covers',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'order',
            'label'             => 'Order',
            'type'              => 'number',
            'default'           => CourseModule::where('course_id', $this->courseId)->count() + 1,
            'attributes'        => [
                'min' => 0,
            ],
            'wrapperAttributes' => [
                'class' => 'col-md-4',
            ],
        ]);
    }

    public function store(StoreRequest $request)
    {
        return parent::storeCrud($request);
    }

    public function update(UpdateRequest $request)
    {
        return parent::updateCrud($request);
    }
}
