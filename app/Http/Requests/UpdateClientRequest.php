<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\ValidationService;

class UpdateClientRequest extends FormRequest
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'first_name' => trim($this->first_name ?? ''),
            'country' => trim($this->country ?? ''),
            'city' => trim($this->city ?? ''),
            'address' => trim($this->address ?? ''),
            'work_phone' => trim($this->work_phone ?? ''),
            'contact_phone' => trim($this->contact_phone ?? ''),
            'work_email' => strtolower(trim($this->work_email ?? '')),
            'contact_email' => strtolower(trim($this->contact_email ?? '')),
            'work_fax' => trim($this->work_fax ?? ''),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $clientId = $this->route('id') ?? $this->route('client');

        return ValidationService::clientBusinessRules(true, $clientId) + [
            // File Uploads
            'files' => 'nullable|array',
            'files.*' => ValidationService::fileRules(false, 5120, 'jpeg,jpg,png,gif,pdf,doc,docx'),

            // Additional fields specific to updates
            'password' => 'nullable|string|min:6'
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return array_merge(ValidationService::getCommonMessages(), [
            'first_name.required' => 'Client name is required.',
            'first_name.regex' => 'Client name can only contain letters, spaces, hyphens, dots, and apostrophes.',
            'first_name.min' => 'Client name must be at least 2 characters.',
            'country.required' => 'Country is required.',
            'city.required' => 'City is required.',
            'address.required' => 'Address is required.',
            'address.min' => 'Address must be at least 5 characters.',
            'work_phone.required' => 'Work phone number is required.',
            'work_phone.regex' => 'Please enter a valid work phone number.',
            'contact_phone.regex' => 'Please enter a valid contact phone number.',
            'work_email.required' => 'Work email address is required.',
            'work_email.email' => 'Please enter a valid work email address.',
            'work_email.unique' => 'This work email address is already registered.',
            'contact_email.email' => 'Please enter a valid contact email address.',
            'contact_email.unique' => 'This contact email address is already registered.',
            'work_fax.regex' => 'Please enter a valid fax number.',
            'files.*.max' => 'Each file must not exceed 5MB.',
            'files.*.mimes' => 'Files must be of type: JPEG, JPG, PNG, GIF, PDF, DOC, DOCX.'
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return ValidationService::getCommonAttributes() + [
            'first_name' => 'client name',
            'work_fax' => 'fax number'
        ];
    }
}