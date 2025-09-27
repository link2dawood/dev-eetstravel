<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\ValidationService;

class StoreEventRequest extends FormRequest
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
            'work_email' => ValidationService::emailRules(false, 'events,work_email'),
            'contact_email' => ValidationService::emailRules(false),
            'work_phone' => ValidationService::phoneRules(false),
            'contact_phone' => ValidationService::phoneRules(false),
            'contact_name' => ValidationService::nameRules(false, 191, 2),
            'address_first' => ValidationService::addressRules(false, 500, 5),
            'address_second' => ValidationService::addressRules(false, 500, 5),
            'city' => 'nullable|string|max:191',
            'country' => 'nullable|string|max:191',
            'website' => 'nullable|url|max:191',
            'comments' => 'nullable|string|max:1000',
            'int_comments' => 'nullable|string|max:1000',
            'rate' => 'nullable|exists:rates,id',
            'criterias' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return ValidationService::getCommonMessages();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return array_merge(ValidationService::getCommonAttributes(), [
            'work_email' => 'work email',
            'contact_email' => 'contact email',
            'work_phone' => 'work phone',
            'contact_phone' => 'contact phone',
            'contact_name' => 'contact name',
            'address_first' => 'primary address',
            'address_second' => 'secondary address',
            'int_comments' => 'internal comments',
        ]);
    }
}
