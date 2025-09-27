<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\ValidationService;

class UpdateHotelRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
            'address_first' => trim($this->address_first ?? ''),
            'city' => trim($this->city ?? ''),
            'country' => trim($this->country ?? ''),
            'work_phone' => trim($this->work_phone ?? ''),
            'work_email' => strtolower(trim($this->work_email ?? '')),
        ]);
    }

    public function rules()
    {
        $hotelId = $this->route('id') ?? $this->route('hotel');
        return ValidationService::hotelBusinessRules(true, $hotelId) + [
            'star_rating' => 'nullable|integer|min:1|max:5',
            'files' => 'nullable|array',
            'files.*' => ValidationService::fileRules(false, 10240)
        ];
    }

    public function messages()
    {
        return ValidationService::getCommonMessages();
    }

    public function attributes()
    {
        return ValidationService::getCommonAttributes();
    }
}