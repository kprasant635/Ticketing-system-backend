<?php

namespace App\Requests\API\V1\Master\Status;

use Illuminate\Foundation\Http\FormRequest;

class CreateStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status_name' => ['required', 'string', 'max:255', 'unique:statuses,status_name'],
        ];
    }
}
