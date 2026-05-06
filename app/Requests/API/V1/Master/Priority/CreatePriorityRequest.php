<?php

namespace App\Requests\API\V1\Master\Priority;

use Illuminate\Foundation\Http\FormRequest;

class CreatePriorityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'priority_name' => ['required', 'string', 'max:255', 'unique:priorities,priority_name'],
            'sla_hours'     => ['nullable', 'integer', 'min:1'],
        ];
    }
}
