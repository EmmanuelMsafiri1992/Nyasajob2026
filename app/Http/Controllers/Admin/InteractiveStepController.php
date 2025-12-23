<?php

namespace App\Http\Controllers\Admin;

use App\Models\CourseLesson;
use App\Models\InteractiveStep;
use App\Http\Controllers\Admin\Panel\PanelController;
use App\Http\Requests\Admin\InteractiveStepRequest as StoreRequest;
use App\Http\Requests\Admin\InteractiveStepRequest as UpdateRequest;

class InteractiveStepController extends PanelController
{
    public $lessonId = null;
    public $lesson = null;

    public function setup()
    {
        // Get the Lesson ID from the URL
        $this->lessonId = request()->segment(3);
        $this->lesson = CourseLesson::with('module.course')->find($this->lessonId);

        if (!$this->lesson) {
            abort(404, 'Lesson not found');
        }

        $module = $this->lesson->module;
        $course = $module->course;

        /*
        |--------------------------------------------------------------------------
        | BASIC CRUD INFORMATION
        |--------------------------------------------------------------------------
        */
        $this->xPanel->setModel('App\Models\InteractiveStep');
        $this->xPanel->setRoute(admin_uri('lessons/' . $this->lessonId . '/steps'));
        $this->xPanel->setEntityNameStrings(
            'Step &rarr; <strong>' . $this->lesson->title . '</strong>',
            'Steps &rarr; <strong>' . $this->lesson->title . '</strong>'
        );

        // Enable parent navigation
        $this->xPanel->enableParentEntity();
        $this->xPanel->setParentKeyField('lesson_id');
        $this->xPanel->addClause('where', 'lesson_id', '=', $this->lessonId);
        $this->xPanel->setParentRoute(admin_uri('modules/' . $module->id . '/lessons'));
        $this->xPanel->setParentEntityNameStrings('Lesson', 'Lessons');

        $this->xPanel->allowAccess(['reorder', 'parent', 'create', 'update', 'delete', 'list']);
        $this->xPanel->enableReorder('title', 1);

        if (!request()->input('order')) {
            $this->xPanel->orderBy('step_number', 'ASC');
        }

        // Buttons
        $this->xPanel->addButtonFromModelFunction('top', 'bulk_deletion_btn', 'bulkDeletionBtn', 'end');
        $this->xPanel->addButtonFromModelFunction('top', 'preview_lesson_btn', 'previewLessonBtn', 'beginning');

        /*
        |--------------------------------------------------------------------------
        | FILTERS
        |--------------------------------------------------------------------------
        */
        $this->xPanel->disableSearchBar();

        $this->xPanel->addFilter([
            'name'  => 'action_type',
            'type'  => 'dropdown',
            'label' => 'Action Type',
        ], InteractiveStep::ACTION_TYPES, function ($value) {
            $this->xPanel->addClause('where', 'action_type', '=', $value);
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
            'name'  => 'step_number',
            'label' => 'Step #',
            'type'  => 'number',
        ]);

        $this->xPanel->addColumn([
            'name'  => 'title',
            'label' => trans('admin.Title'),
        ]);

        $this->xPanel->addColumn([
            'name'          => 'action_type',
            'label'         => 'Action',
            'type'          => 'model_function',
            'function_name' => 'getActionTypeBadge',
        ]);

        $this->xPanel->addColumn([
            'name'  => 'target_element',
            'label' => 'Target',
        ]);

        $this->xPanel->addColumn([
            'name'  => 'points',
            'label' => 'Points',
            'type'  => 'number',
        ]);

        $this->xPanel->addColumn([
            'name'          => 'is_required',
            'label'         => 'Required',
            'type'          => 'model_function',
            'function_name' => 'getRequiredBadge',
        ]);

        /*
        |--------------------------------------------------------------------------
        | FIELDS
        |--------------------------------------------------------------------------
        */
        $this->xPanel->addField([
            'name'  => 'lesson_id',
            'type'  => 'hidden',
            'value' => $this->lessonId,
        ]);

        $this->xPanel->addField([
            'name'              => 'step_number',
            'label'             => 'Step Number',
            'type'              => 'number',
            'default'           => InteractiveStep::where('lesson_id', $this->lessonId)->count() + 1,
            'attributes'        => [
                'min' => 1,
            ],
            'wrapperAttributes' => [
                'class' => 'col-md-2',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'title',
            'label'             => 'Step Title',
            'type'              => 'text',
            'attributes'        => [
                'placeholder' => 'e.g., Click the Start button',
            ],
            'wrapperAttributes' => [
                'class' => 'col-md-10',
            ],
        ]);

        $this->xPanel->addField([
            'name'       => 'instruction',
            'label'      => 'Instruction',
            'type'       => 'textarea',
            'attributes' => [
                'rows' => 3,
                'placeholder' => 'Detailed instruction shown to the learner',
            ],
            'hint'       => 'Explain what the learner needs to do in this step',
        ]);

        $this->xPanel->addField([
            'name'              => 'action_type',
            'label'             => 'Action Type',
            'type'              => 'select2_from_array',
            'options'           => InteractiveStep::ACTION_TYPES,
            'allows_null'       => false,
            'default'           => 'click',
            'wrapperAttributes' => [
                'class' => 'col-md-4',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'target_element',
            'label'             => 'Target Element',
            'type'              => 'text',
            'attributes'        => [
                'placeholder' => 'e.g., #start-button, .desktop-icon[data-app="notepad"]',
            ],
            'hint'              => 'CSS selector for the element the learner must interact with',
            'wrapperAttributes' => [
                'class' => 'col-md-8',
            ],
        ]);

        $this->xPanel->addField([
            'name'       => 'action_data',
            'label'      => 'Action Data (JSON)',
            'type'       => 'textarea',
            'attributes' => [
                'rows' => 3,
                'placeholder' => '{"text": "Hello World", "app": "notepad"}',
            ],
            'hint'       => 'Additional data for the action (e.g., text to type, app to open)',
        ]);

        $this->xPanel->addField([
            'name'       => 'validation_rules',
            'label'      => 'Validation Rules (JSON)',
            'type'       => 'textarea',
            'attributes' => [
                'rows' => 3,
                'placeholder' => '{"type": "element_clicked", "target": "#start-button"}',
            ],
            'hint'       => 'Rules for validating step completion',
        ]);

        $this->xPanel->addField([
            'name'              => 'hint',
            'label'             => 'Hint',
            'type'              => 'text',
            'attributes'        => [
                'placeholder' => 'Hint shown after failed attempts',
            ],
            'wrapperAttributes' => [
                'class' => 'col-md-6',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'points',
            'label'             => 'Points',
            'type'              => 'number',
            'default'           => 10,
            'attributes'        => [
                'min' => 0,
            ],
            'wrapperAttributes' => [
                'class' => 'col-md-3',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'is_required',
            'label'             => 'Required',
            'type'              => 'checkbox_switch',
            'default'           => 1,
            'hint'              => 'Must complete to finish lesson',
            'wrapperAttributes' => [
                'class' => 'col-md-3',
            ],
        ]);

        $this->xPanel->addField([
            'name'              => 'timeout_seconds',
            'label'             => 'Timeout (seconds)',
            'type'              => 'number',
            'attributes'        => [
                'min' => 0,
                'placeholder' => 'Optional time limit',
            ],
            'wrapperAttributes' => [
                'class' => 'col-md-4',
            ],
        ]);
    }

    public function store(StoreRequest $request)
    {
        // Parse JSON fields
        $request = $this->parseJsonFields($request);

        return parent::storeCrud($request);
    }

    public function update(UpdateRequest $request)
    {
        // Parse JSON fields
        $request = $this->parseJsonFields($request);

        return parent::updateCrud($request);
    }

    private function parseJsonFields($request)
    {
        // Parse action_data JSON
        $actionData = $request->input('action_data');
        if (!empty($actionData) && is_string($actionData)) {
            $decoded = json_decode($actionData, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->request->set('action_data', $decoded);
            }
        }

        // Parse validation_rules JSON
        $validationRules = $request->input('validation_rules');
        if (!empty($validationRules) && is_string($validationRules)) {
            $decoded = json_decode($validationRules, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->request->set('validation_rules', $decoded);
            }
        }

        return $request;
    }
}
