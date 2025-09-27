<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
        // Clean and format input data
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
        return [
            // Basic Information
            'first_name' => [
                'required',
                'string',
                'max:191',
                'min:2',
                'regex:/^[a-zA-Z\s\-\.\']+$/' // Allow letters, spaces, hyphens, dots, apostrophes
            ],

            // Location Information
            'country' => [
                'required',
                'string',
                'max:191',
                'min:2',
                'regex:/^[a-zA-Z\s\-]+$/' // Allow letters, spaces, hyphens
            ],
            'city' => [
                'required',
                'string',
                'max:191',
                'min:2',
                'regex:/^[a-zA-Z\s\-\.]+$/' // Allow letters, spaces, hyphens, dots
            ],
            'address' => [
                'required',
                'string',
                'max:500',
                'min:5'
            ],

            // Contact Information
            'work_phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/' // Phone format
            ],
            'contact_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/' // Phone format
            ],
            'work_email' => [
                'required',
                'email',
                'max:191',
                'unique:clients,work_email'
            ],
            'contact_email' => [
                'nullable',
                'email',
                'max:191',
                'unique:clients,contact_email'
            ],
            'work_fax' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/' // Fax format similar to phone
            ],

            // File Uploads
            'files' => 'nullable|array',
            'files.*' => [
                'file',
                'max:5120', // 5MB max
                'mimes:jpeg,jpg,png,gif,pdf,doc,docx'
            ]
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'first_name.required' => 'Client name is required.',
            'first_name.regex' => 'Client name can only contain letters, spaces, hyphens, dots, and apostrophes.',
            'first_name.min' => 'Client name must be at least 2 characters.',
            'country.required' => 'Country is required.',
            'country.regex' => 'Country name can only contain letters, spaces, and hyphens.',
            'city.required' => 'City is required.',
            'city.regex' => 'City name can only contain letters, spaces, hyphens, and dots.',
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
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'first_name' => 'client name',
            'work_phone' => 'work phone',
            'contact_phone' => 'contact phone',
            'work_email' => 'work email',
            'contact_email' => 'contact email',
            'work_fax' => 'fax number'
        ];
    }
}
