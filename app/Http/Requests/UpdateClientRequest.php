<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'name'          => trim($this->name ?? ''),
            'country'       => trim($this->country ?? ''),
            'city'          => trim($this->city ?? ''),
            'address'       => trim($this->address ?? ''),
            'work_phone'    => trim($this->work_phone ?? ''),
            'contact_phone' => trim($this->contact_phone ?? ''),
            'work_email'    => strtolower(trim($this->work_email ?? '')),
            'contact_email' => strtolower(trim($this->contact_email ?? '')),
            'work_fax'      => trim($this->work_fax ?? ''),
        ]);
    }

    public function rules()
    {
        // Allow the client to keep its own email on update.
        $clientId           = $this->route('id') ?? $this->route('client');
        $workEmailUnique    = 'unique:clients,work_email'    . ($clientId ? ',' . $clientId : '');
        $contactEmailUnique = 'unique:clients,contact_email' . ($clientId ? ',' . $clientId : '');

        return [
            // Identity
            'name'    => 'required|string|max:191|min:2',
            'country' => 'required|string|max:191',

            // Optional
            'city'             => 'nullable|string|max:191',
            'address'          => 'nullable|string|max:500',
            'account_no'       => 'nullable|string|max:250',
            'company_address'  => 'nullable|string|max:250',
            'invoice_address'  => 'nullable|string|max:250',
            'work_phone'       => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'contact_phone'    => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'work_email'       => 'nullable|email|max:191|' . $workEmailUnique,
            'contact_email'    => 'nullable|email|max:191|' . $contactEmailUnique,
            'work_fax'         => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'password'         => 'nullable|string|min:1',
        ];
    }

    public function messages()
    {
        return [
            'name.required'         => 'Client name is required.',
            'name.min'              => 'Client name must be at least 2 characters.',
            'country.required'      => 'Country is required.',
            'work_phone.regex'      => 'Please enter a valid work phone number.',
            'contact_phone.regex'   => 'Please enter a valid contact phone number.',
            'work_email.email'      => 'Please enter a valid work email address.',
            'work_email.unique'     => 'This work email address is already registered.',
            'contact_email.email'   => 'Please enter a valid contact email address.',
            'contact_email.unique'  => 'This contact email address is already registered.',
            'work_fax.regex'        => 'Please enter a valid fax number.',
        ];
    }

    public function attributes()
    {
        return [
            'name'          => 'client name',
            'work_phone'    => 'work phone',
            'contact_phone' => 'contact phone',
            'work_email'    => 'work email',
            'contact_email' => 'contact email',
            'work_fax'      => 'fax number',
        ];
    }
}
