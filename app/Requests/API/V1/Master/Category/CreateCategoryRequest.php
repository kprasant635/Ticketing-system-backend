<?php

namespace App\Requests\API\V1\Master\Category;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'serviceId' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'serviceId.required' => 'Service ID is required',
            'serviceId.string' => 'Service ID must be a string',
            'name.required' => 'Category name is required',
            'name.string' => 'Category name must be a string',
            'name.max' => 'Category name cannot exceed 255 characters',
            'name.unique' => 'Category name already exists',
            'description.string' => 'Description must be a string',
        ];
    }
}
