<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'title'          => ['required', 'string', 'min:3', 'max:255'],
            'slug'           => ['nullable', 'string', 'max:255'],
            'description'    => ['required', 'string', 'min:10'],
            'objectives'     => ['nullable', 'string'],
            'thumbnail'      => ['nullable'],
            'price'          => ['nullable', 'numeric', 'min:0'],
            'is_free'        => ['nullable'],
            'level'          => ['required', 'in:beginner,intermediate,advanced'],
            'duration_hours' => ['nullable', 'integer', 'min:0'],
            'is_published'   => ['nullable'],
            'instructor_id'  => ['nullable', 'exists:users,id'],
        ];

        // On update, make slug unique except for current course
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $courseId = $this->route('id');
            $rules['slug'][] = 'unique:courses,slug,' . $courseId;
        } else {
            $rules['slug'][] = 'unique:courses,slug';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required'       => 'The course title is required.',
            'title.min'            => 'The course title must be at least 3 characters.',
            'description.required' => 'Please provide a course description.',
            'description.min'      => 'The description must be at least 10 characters.',
            'level.required'       => 'Please select a difficulty level.',
            'level.in'             => 'Invalid difficulty level selected.',
            'slug.unique'          => 'This slug is already in use by another course.',
        ];
    }
}
