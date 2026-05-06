<?php

namespace App\Modules\Master\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'service_name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'service_name.required' => 'Please enter service name',
            'service_name.string' => 'Please enter valid service name',
            'service_name.max' => 'Please enter valid service name',
            'description.string' => 'Please enter valid description',
            'status.boolean' => 'Please enter valid status',
        ];
    }

    public function attributes()
    {
        return [
            'service_name' => 'Service Name',
            'description' => 'Description',
            'status' => 'Status',
        ];
    }
}
