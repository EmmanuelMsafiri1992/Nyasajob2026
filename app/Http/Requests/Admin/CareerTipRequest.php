<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request;

class CareerTipRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:career_tips,slug',
            'category' => 'required|string|in:' . implode(',', array_keys(\App\Models\CareerTip::CATEGORIES)),
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|string|max:500',
            'reading_time' => 'required|integer|min:1|max:60',
            'is_featured' => 'boolean',
            'active' => 'boolean',
        ];

        // On update, exclude current record from unique check
        if ($this->method() === 'PUT') {
            $id = $this->route('id');
            $rules['slug'] = "nullable|string|max:255|unique:career_tips,slug,{$id}";
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The article title is required.',
            'category.required' => 'Please select a category.',
            'content.required' => 'The article content is required.',
        ];
    }
}
