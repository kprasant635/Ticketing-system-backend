<?php

namespace App\Requests\API\V1\Master\Priority;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePriorityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'priority_name' => ['required', 'string', 'max:255', Rule::unique('priorities', 'priority_name')->ignore($this->route('id'))],
            'sla_hours'     => ['nullable', 'integer', 'min:1'],
        ];
    }
}
