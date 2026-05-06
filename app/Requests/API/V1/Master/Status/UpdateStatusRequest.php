<?php

namespace App\Requests\API\V1\Master\Status;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status_name' => ['required', 'string', 'max:255', Rule::unique('statuses', 'status_name')->ignore($this->route('id'))],
        ];
    }
}
