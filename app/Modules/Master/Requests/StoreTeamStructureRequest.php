<?php

namespace App\Modules\Master\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamStructureRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'service_id' => 'required',
            'categoryId' => 'required',
            'teamLeadId' => 'required',
            'developers' => 'required|array|min:1',
            'developers.*' => 'required'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            // 'service_id.required' => 'Please select a service.',
            'categoryId.required' => 'Please select a category.',
            'teamLeadId.required' => 'Please select a team lead.',
            'developers.required' => 'Please select at least one developer.',
            'developers.array' => 'Developers must be provided as an array.',
            'developers.min' => 'Please select at least one developer.',
            'developers.*.required' => 'Each developer selection is invalid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            // 'service_id' => 'Service',
            'category_id' => 'Category',
            'team_lead_id' => 'Team Lead',
            'developers' => 'Developers',
        ];
    }
}
