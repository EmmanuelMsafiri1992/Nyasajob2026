<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseLesson;
use App\Http\Controllers\Admin\Panel\PanelController;
use App\Http\Requests\Admin\CourseLessonRequest as StoreRequest;
use App\Http\Requests\Admin\CourseLessonRequest as UpdateRequest;

class CourseLessonController extends PanelController
{
    public $moduleId = null;
    public $module = null;
    public $course = null;

    public function setup()
    {
        // Get the Module ID from the URL
        $this->moduleId = request()->segment(3);
        $this->module = CourseModule::with('course')->find($this->moduleId);

        if (!$this->module) {
            abort(404, 'Module not found');
        }

        $this->course = $this->module->course;

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->xPanel->setModel('App\Models\CourseLesson');
        $this->xPanel->with(['exercises', 'interactiveSteps']);
        $this->xPanel->setRoute(admin_uri('modules/' . $this->moduleId . '/lessons'));
        $this->xPanel->setEntityNameStrings(
            'Lesson &rarr; <strong>' . $this->module->title . '</strong>',
            'Lessons &rarr; <strong>' . $this->module->title . '</strong>'
        );

        // Enable parent navigation
        $this->xPanel->enableParentEntity();
        $this->xPanel->setParentKeyField('module_id');
        $this->xPanel->addClause('where', 'module_id', '=', $this->moduleId);
        $this->xPanel->setParentRoute(admin_uri('courses/' . $this->course->id . '/modules'));
        $this->xPanel->setParentEntityNameStrings('Module', 'Modules');

        $this->xPanel->allowAccess(['reorder', 'parent', 'create', 'update', 'delete', 'list']);
        $this->xPanel->enableReorder('title', 1);

        if (!request()->input('order')) {
            $this->xPanel->orderBy('order', 'ASC');
        }

        // Buttons
        $this->xPanel->addButtonFromModelFunction('top', 'bulk_deletion_btn', 'bulkDeletionBtn', 'end');
        $this->xPanel->addButtonFromModelFunction('line', 'interactive_steps', 'interactiveStepsBtn', 'beginning');

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
            'name'  => 'type',
            'type'  => 'dropdown',
            'label' => 'Type',
        ], CourseLesson::TYPES, function ($value) {
            $this->xPanel->addClause('where', 'type', '=', $value);
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
            'name'          => 'type',
            'label'         => 'Type',
            'type'          => 'model_function',
            'function_name' => 'getTypeBadge',
        ]);

        $this->xPanel->addColumn([
            'name'          => 'is_free_preview',
            'label'         => 'Free Preview',
            'type'          => 'model_function',
            'function_name' => 'getFreePreviewBadge',
        ]);

        $this->xPanel->addColumn([
            'name'  => 'duration_minutes',
            'label' => 'Duration',
            'type'  => 'number',
            'suffix' => ' min',
        ]);

        /*
        |--------------------------------------------------------------------------
        | FIELDS
        |--------------------------------------------------------------------------
        */
        $this->xPanel->addField([
            'name'  => 'module_id',
            'type'  => 'hidden',
            'value' => $this->moduleId,
        ]);

        $this->xPanel->addField([
            'name'              => 'title',
            'label'             => 'Lesson Title',
            'type'              => 'text',
            'attributes'        => [
                'placeholder' => 'e.g., Introduction to the Desktop',
            ],
            'wrapperAttributes' => [
                'class' => 'col-md-8',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'type',
            'label'             => 'Lesson Type',
            'type'              => 'select2_from_array',
            'options'           => CourseLesson::TYPES,
            'allows_null'       => false,
            'default'           => 'text',
            'hint'              => 'Interactive = Virtual desktop simulation',
            'wrapperAttributes' => [
                'class' => 'col-md-4',
            ],
        ]);

        $wysiwygEditor = config('settings.other.wysiwyg_editor');
        $wysiwygEditorViewPath = '/views/admin/panel/fields/' . $wysiwygEditor . '.blade.php';
        $this->xPanel->addField([
            'name'       => 'content',
            'label'      => 'Lesson Content',
            'type'       => ($wysiwygEditor != 'none' && file_exists(resource_path() . $wysiwygEditorViewPath))
                ? $wysiwygEditor
                : 'textarea',
            'attributes' => [
                'id'   => 'content',
                'rows' => 10,
            ],
            'hint'       => 'For interactive lessons, this is the introduction text shown before the simulation.',
        ]);

        $this->xPanel->addField([
            'name'              => 'video_url',
            'label'             => 'Video URL',
            'type'              => 'text',
            'attributes'        => [
                'placeholder' => 'https://youtube.com/... or https://vimeo.com/...',
            ],
            'hint'              => 'Only for video lesson types',
            'wrapperAttributes' => [
                'class' => 'col-md-8',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'duration_minutes',
            'label'             => 'Duration (minutes)',
            'type'              => 'number',
            'attributes'        => [
                'min' => 0,
                'placeholder' => 'Estimated time',
            ],
            'wrapperAttributes' => [
                'class' => 'col-md-4',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'order',
            'label'             => 'Order',
            'type'              => 'number',
            'default'           => CourseLesson::where('module_id', $this->moduleId)->count() + 1,
            'attributes'        => [
                'min' => 0,
            ],
            'wrapperAttributes' => [
                'class' => 'col-md-4',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'is_free_preview',
            'label'             => 'Free Preview',
            'type'              => 'checkbox_switch',
            'default'           => 0,
            'hint'              => 'Allow non-enrolled users to preview this lesson',
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
