<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CourseModuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id'   => ['required', 'exists:courses,id'],
            'title'       => ['required', 'string', 'min:2', 'max:255'],
            'description' => ['nullable', 'string'],
            'order'       => ['nullable', 'integer', 'min:0'],
        ];
    }
}
