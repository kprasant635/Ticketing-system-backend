<?php

namespace App\Requests\API\V1\Ticket;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'service_id' => decrypt_id($this->service_id),
            'category_id' => decrypt_id($this->category_id),
            'sub_category_id' => decrypt_id($this->sub_category_id),
        ]);
    }

    public function rules(): array
    {
        return [
            'service_id' => 'required|integer|exists:services,id',
            'category_id' => 'required|integer|exists:categories,id',
            'sub_category_id' => [
                'required',
                'integer',
                Rule::exists('sub_categories', 'id')->where(function ($query) {
                    $query->where('category_id', $this->category_id);
                }),
            ],
            // 'priority_id' => 'required|integer|exists:priorities,id',
            'subject' => 'required|string|max:255|min:5',
            'description' => 'required|string|min:10'
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Selected category does not exist',
            'sub_category_id.required' => 'Sub category is required',
            'sub_category_id.exists' => 'The selected subcategory does not belong to the selected category.',
            // 'priority_id.required' => 'Priority is required',
            // 'priority_id.exists' => 'Selected priority does not exist',
            'subject.required' => 'Subject is required',
            'subject.min' => 'Subject must be at least 5 characters',
            'subject.max' => 'Subject cannot exceed 255 characters',
            'description.required' => 'Description is required',
            'description.min' => 'Description must be at least 10 characters'
        ];
    }
}
