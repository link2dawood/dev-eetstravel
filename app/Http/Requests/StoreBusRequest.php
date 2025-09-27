<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\ValidationService;

class StoreBusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ValidationService::businessNameRules(true, 191, 2),
            'bus_number' => 'required|string|max:50|unique:buses,bus_number',
            'transfer_id' => 'nullable|exists:transfers,id',
            'capacity' => 'nullable|integer|min:1|max:500',
            'comments' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return array_merge(ValidationService::getCommonMessages(), [
            'bus_number.unique' => 'This bus number has already been taken.',
            'transfer_id.exists' => 'The selected transfer company is invalid.',
            'capacity.integer' => 'The capacity must be a number.',
            'capacity.min' => 'The capacity must be at least 1.',
            'capacity.max' => 'The capacity may not be greater than 500.',
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return array_merge(ValidationService::getCommonAttributes(), [
            'bus_number' => 'bus number',
            'transfer_id' => 'transfer company',
        ]);
    }
}