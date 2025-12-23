<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CourseLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'module_id'        => ['required', 'exists:course_modules,id'],
            'title'            => ['required', 'string', 'min:2', 'max:255'],
            'content'          => ['nullable', 'string'],
            'type'             => ['required', 'in:text,video,quiz,exercise,interactive'],
            'video_url'        => ['nullable', 'url'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'order'            => ['nullable', 'integer', 'min:0'],
            'is_free_preview'  => ['nullable'],
        ];
    }
}
