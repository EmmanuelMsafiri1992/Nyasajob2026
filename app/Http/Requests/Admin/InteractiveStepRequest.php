<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class InteractiveStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lesson_id'        => ['required', 'exists:course_lessons,id'],
            'step_number'      => ['required', 'integer', 'min:1'],
            'title'            => ['required', 'string', 'min:2', 'max:255'],
            'instruction'      => ['required', 'string'],
            'action_type'      => ['required', 'string'],
            'target_element'   => ['nullable', 'string'],
            'action_data'      => ['nullable'],
            'validation_rules' => ['nullable'],
            'hint'             => ['nullable', 'string', 'max:500'],
            'points'           => ['nullable', 'integer', 'min:0'],
            'is_required'      => ['nullable'],
            'timeout_seconds'  => ['nullable', 'integer', 'min:0'],
        ];
    }
}
