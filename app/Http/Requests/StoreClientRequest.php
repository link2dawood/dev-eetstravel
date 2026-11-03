<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\ValidationService;

class StoreClientRequest extends FormRequest
{
    public function authorize() { return true; }

    protected function prepareForValidation()
    {
        $clean = [];
        foreach (['name', 'country', 'city', 'address', 'work_phone', 'contact_phone', 'work_email', 'contact_email', 'work_fax'] as $field) {
            if (!$this->exists($field)) continue;
            $value = $this->input($field);
            if (is_string($value)) {
                $value = trim($value);
                if (in_array($field, ['work_email', 'contact_email'], true)) $value = strtolower($value);
            }
            $clean[$field] = $value;
        }
        $this->merge($clean);
    }

    public function rules()
    {
        $rules = ValidationService::clientBusinessRules();
        $rules['address'] = ValidationService::addressRules(true, 191, 5);
        foreach (['work_phone', 'contact_phone'] as $field) $rules[$field][] = 'regex:/[0-9]/';
        foreach ($rules as &$fieldRules) array_unshift($fieldRules, 'bail');
        unset($fieldRules);
        return $rules + [
            'country' => ['bail', 'required', 'string', 'min:2', 'max:191', 'regex:/^[a-zA-Z\s-]+$/'],
            'city' => ValidationService::cityRules(true),
            'account_no' => 'nullable|string|max:250',
            'company_address' => 'nullable|string|max:250',
            'invoice_address' => 'nullable|string|max:250',
            'work_fax' => array_merge(ValidationService::phoneRules(false), ['regex:/[0-9]/']),
            'files' => 'nullable|array|max:10',
            'files.*' => ValidationService::fileRules(false, 5120, 'jpeg,jpg,png,gif,pdf,doc,docx'),
            'attach' => 'nullable|array|max:10',
            'attach.*' => ValidationService::fileRules(false, 5120, 'jpeg,jpg,png,gif,pdf,doc,docx'),
            'attach_bus' => 'prohibited',
            'password' => \App\Services\ClientPortalPassword::rules(),
        ];
    }

    public function messages()
    {
        return ValidationService::getCommonMessages() + ['work_email.unique' => 'This work email address is already registered.'];
    }
}
