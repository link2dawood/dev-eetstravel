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
        $clean = [];
        foreach (['name', 'country', 'city', 'address', 'work_phone', 'contact_phone', 'work_email', 'contact_email', 'work_fax'] as $field) {
            if (!$this->exists($field)) continue;
            $value = $this->input($field);
            if (is_string($value)) {
                $value = trim($value);
                if (in_array($field, ['work_email', 'contact_email'])) $value = strtolower($value);
            }
            $clean[$field] = $value;
        }
        $contacts = $this->input('contacts');
        if (is_array($contacts)) {
            foreach ($contacts as &$contact) {
                if (!is_array($contact)) continue;
                foreach (['contact_full_name', 'contact_mobile_phone', 'contact_work_phone', 'contact_email'] as $field) {
                    if (isset($contact[$field]) && is_string($contact[$field])) $contact[$field] = trim($contact[$field]);
                }
            }
            unset($contact);
            $clean['contacts'] = $contacts;
        }
        $this->merge($clean);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $clientId = $this->route('id') ?? $this->route('client');
        if ($clientId instanceof \App\Client) $clientId = $clientId->getKey();

        $rules = ValidationService::clientBusinessRules(true, $clientId);
        $client = $clientId ? \App\Client::findOrFail($clientId) : null;
        if ($client) {
            // Imported clients can already share addresses. Keep their unchanged
            // values editable; newly supplied addresses must still be unique.
            foreach (['work_email', 'contact_email'] as $field) {
                if ((is_string($this->input($field)) || $this->input($field) === null)
                    && strtolower(trim((string) $this->input($field))) === strtolower(trim((string) $client->$field))) {
                    $rules[$field] = array_filter($rules[$field], function ($rule) {
                        return !is_string($rule) || strpos($rule, 'unique:') !== 0;
                    });
                }
            }
        }

        $rules['address'] = ValidationService::addressRules(true, 191, 5);
        $rules['name'][] = 'regex:/[\pL\pN]/u';
        foreach (['work_phone', 'contact_phone'] as $field) $rules[$field][] = 'regex:/[0-9]/';
        foreach ($rules as &$fieldRules) array_unshift($fieldRules, 'bail');
        unset($fieldRules);

        return $rules + [
            'country' => 'bail|sometimes|nullable|string|max:191',
            'city' => 'bail|sometimes|nullable|integer|exists:cities,id',
            'account_no' => 'bail|nullable|string|max:250',
            'company_address' => 'bail|nullable|string|max:250',
            'invoice_address' => 'bail|nullable|string|max:250',
            'work_fax' => array_merge(ValidationService::phoneRules(false), ['regex:/[0-9]/']),
            'contacts_loaded' => 'nullable|boolean',
            'contacts' => 'bail|sometimes|array|max:100',
            'contacts.*' => 'bail|required|array',
            'contacts.*.contact_full_name' => ['bail', 'required', 'string', 'max:199', "regex:/^[\pL\pM\pN\s.'’(),&\/-]+$/u", 'regex:/[\pL\pN]/u'],
            'contacts.*.contact_mobile_phone' => array_merge(ValidationService::phoneRules(false), ['regex:/[0-9]/']),
            'contacts.*.contact_work_phone' => array_merge(ValidationService::phoneRules(false), ['regex:/[0-9]/']),
            'contacts.*.contact_email' => ['bail', 'nullable', 'string', 'email', 'max:199'],
            // File Uploads
            'files' => 'nullable|array',
            'files.*' => ValidationService::fileRules(false, 5120, 'jpeg,jpg,png,gif,pdf,doc,docx'),
            'attach' => 'nullable|array',
            'attach.*' => ValidationService::fileRules(false, 5120, 'jpeg,jpg,png,gif,pdf,doc,docx'),

            // Additional fields specific to updates
            'password' => ['bail', 'nullable', 'string', 'min:6', 'max:128', function ($attribute, $value, $fail) {
                // The legacy password column stores ciphertext in VARCHAR(250).
                if (strlen(\Illuminate\Support\Facades\Crypt::encryptString($value)) > 250) {
                    $fail('The client portal password is too long. Please use a shorter password.');
                }
            }],
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
            'contacts.array' => 'Contacts must be a list of contact rows.',
            'contacts.max' => 'A client can have at most 100 contacts.',
            'contacts.*.array' => 'Each contact must contain valid contact fields.',
            'contacts.*.contact_full_name.required' => 'Enter a full name for every contact, or remove the empty row.',
            'contacts.*.contact_full_name.regex' => 'Enter a contact name using letters, numbers and normal name punctuation.',
            'contacts.*.contact_email.email' => 'Enter a valid email address for each contact.',
            'name.required' => 'Client name is required.',
            'name.regex' => 'Client name can only contain letters, digits, spaces and & , . \' - ( ) /.',
            'name.min' => 'Client name must be at least 2 characters.',
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
            'name' => 'client name',
            'work_fax' => 'fax number'
        ];
    }
}
