<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreOfferRequest extends FormRequest
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
            'name' => trim($this->name ?? ''),
            'offer_number' => trim($this->offer_number ?? ''),
            'total_amount' => $this->total_amount ?? 0,
            'currency' => trim($this->currency ?? ''),
            'pax' => $this->pax ?? 0,
            'is_active' => $this->boolean('is_active', true),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $maxDate = Carbon::now()->addYears(2)->format('Y-m-d');
        $minDate = Carbon::now()->format('Y-m-d');

        return [
            // Basic Information
            'name' => [
                'required',
                'string',
                'max:191',
                'min:3'
            ],
            'offer_number' => [
                'required',
                'string',
                'max:50',
                'unique:offers,offer_number',
                'regex:/^[A-Z0-9\-]+$/' // Uppercase letters, numbers, hyphens
            ],
            'description' => 'nullable|string|max:5000',

            // Financial Information
            'total_amount' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99'
            ],
            'currency' => [
                'required',
                'string',
                'size:3',
                'regex:/^[A-Z]{3}$/' // Currency codes like USD, EUR
            ],

            // Passenger Information
            'pax' => [
                'required',
                'integer',
                'min:1',
                'max:500'
            ],

            // Dates
            'offer_date' => [
                'required',
                'date',
                'after_or_equal:' . $minDate,
                'before_or_equal:' . $maxDate
            ],
            'option_date' => [
                'nullable',
                'date',
                'after_or_equal:offer_date',
                'before_or_equal:' . $maxDate
            ],
            'valid_until' => [
                'required',
                'date',
                'after:offer_date',
                'before_or_equal:' . $maxDate
            ],

            // Administrative
            'client_id' => 'required|integer|exists:clients,id',
            'assigned_user' => 'nullable|integer|exists:users,id',
            'status' => 'nullable|integer|exists:status,id',
            'is_active' => 'boolean',

            // File Uploads
            'files' => 'nullable|array',
            'files.*' => [
                'file',
                'max:10240', // 10MB max
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
            'name.required' => 'Offer name is required.',
            'offer_number.required' => 'Offer number is required.',
            'offer_number.unique' => 'This offer number already exists.',
            'offer_number.regex' => 'Offer number must contain only uppercase letters, numbers, and hyphens.',
            'total_amount.required' => 'Total amount is required.',
            'total_amount.min' => 'Total amount must be greater than 0.',
            'currency.required' => 'Currency is required.',
            'currency.regex' => 'Currency must be a valid 3-letter code (e.g., USD, EUR).',
            'pax.required' => 'Number of passengers is required.',
            'pax.min' => 'At least 1 passenger is required.',
            'pax.max' => 'Maximum 500 passengers allowed.',
            'offer_date.required' => 'Offer date is required.',
            'offer_date.after_or_equal' => 'Offer date cannot be in the past.',
            'option_date.after_or_equal' => 'Option date must be on or after offer date.',
            'valid_until.required' => 'Valid until date is required.',
            'valid_until.after' => 'Valid until date must be after offer date.',
            'client_id.required' => 'Client selection is required.',
            'client_id.exists' => 'Selected client does not exist.',
            'assigned_user.exists' => 'Selected assigned user does not exist.',
            'files.*.max' => 'Each file must not exceed 10MB.',
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
            'pax' => 'passengers',
            'offer_date' => 'offer date',
            'option_date' => 'option date',
            'valid_until' => 'valid until date',
            'client_id' => 'client',
            'assigned_user' => 'assigned user',
            'total_amount' => 'total amount'
        ];
    }
}