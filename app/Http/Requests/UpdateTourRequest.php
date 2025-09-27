<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class UpdateTourRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Allow authenticated users to update tours
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Ensure numeric fields are properly formatted
        $this->merge([
            'pax' => $this->pax ?? 0,
            'pax_free' => $this->pax_free ?? 0,
            'rooms' => $this->rooms ?? 1,
            'total_amount' => $this->total_amount ?? 0,
            'price_for_one' => $this->price_for_one ?? 0,
            'is_quotation' => $this->boolean('is_quotation'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $tourId = $this->route('id'); // Get tour ID from route
        $maxDate = Carbon::now()->addYears(2)->format('Y-m-d');
        $minDate = Carbon::now()->subDays(30)->format('Y-m-d'); // Allow 30 days in past for updates

        $rules = [
            // Basic Information
            'name' => [
                'required',
                'string',
                'max:191',
                'min:3',
                'regex:/^[a-zA-Z0-9\s\-#]+$/' // Allow letters, numbers, spaces, hyphens, hash
            ],
            'external_name' => [
                'required',
                'string',
                'max:191',
                Rule::unique('tours', 'external_name')->ignore($tourId),
                'regex:/^[a-zA-Z0-9\-_]+$/' // URL-friendly format
            ],
            'overview' => 'nullable|string|max:5000',
            'remark' => 'nullable|string|max:5000',

            // Dates - More flexible for updates
            'departure_date' => [
                'required',
                'date',
                'after_or_equal:' . $minDate,
                'before_or_equal:' . $maxDate,
                'before_or_equal:retirement_date'
            ],
            'retirement_date' => [
                'required',
                'date',
                'after_or_equal:departure_date',
                'before_or_equal:' . $maxDate
            ],

            // Passenger Information
            'pax' => [
                'required',
                'integer',
                'min:1',
                'max:500'
            ],
            'pax_free' => [
                'nullable',
                'integer',
                'min:0',
                'max:50',
                'lte:pax' // Free passengers cannot exceed total passengers
            ],
            'rooms' => [
                'nullable',
                'integer',
                'min:1',
                'max:250'
            ],

            // Pricing
            'total_amount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99'
            ],
            'price_for_one' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999.99'
            ],

            // Contact Information
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^[\+]?[0-9\s\-\(\)]+$/' // Phone number format
            ],

            // Administrative
            'responsible' => 'nullable|integer|exists:users,id',
            'client_id' => 'nullable|integer|exists:clients,id',
            'status' => 'nullable|integer|exists:status,id',
            'is_quotation' => 'boolean',
            'itinerary_tl' => 'nullable|string|max:121',

            // Business Dates
            'invoice' => 'nullable|date',
            'ga' => 'nullable|date',

            // File Uploads
            'files' => 'nullable|array',
            'files.*' => [
                'file',
                'max:10240', // 10MB max
                'mimes:jpeg,jpg,png,gif,pdf,doc,docx'
            ],
            'attach' => 'nullable|array',
            'attach.*' => [
                'file',
                'max:5120', // 5MB max
                'mimes:jpeg,jpg,png,gif,pdf,doc,docx,xls,xlsx'
            ]
        ];

        // Additional validation for non-quotation tours
        if (!$this->boolean('is_quotation')) {
            $rules = array_merge($rules, [
                'country_begin' => 'required|string|max:191',
                'city_begin' => 'required|integer|exists:cities,id',
                'country_end' => 'required|string|max:191',
                'city_end' => 'required|integer|exists:cities,id',
                'assigned_user' => 'required|integer|exists:users,id',
                'transfer_id' => 'nullable|integer|exists:transfers,id'
            ]);
        } else {
            $rules = array_merge($rules, [
                'country_begin' => 'nullable|string|max:191',
                'city_begin' => 'nullable|integer',
                'country_end' => 'nullable|string|max:191',
                'city_end' => 'nullable|integer',
                'assigned_user' => 'nullable|integer|exists:users,id',
                'transfer_id' => 'nullable|integer'
            ]);
        }

        return $rules;
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Tour name is required.',
            'name.regex' => 'Tour name can only contain letters, numbers, spaces, hyphens, and hash symbols.',
            'external_name.required' => 'External name is required for system integration.',
            'external_name.unique' => 'This external name is already taken by another tour.',
            'external_name.regex' => 'External name must be URL-friendly (letters, numbers, hyphens, underscores only).',
            'departure_date.required' => 'Departure date is required.',
            'departure_date.after_or_equal' => 'Departure date cannot be more than 30 days in the past.',
            'departure_date.before_or_equal' => 'Departure date cannot be more than 2 years in the future.',
            'retirement_date.required' => 'Return date is required.',
            'retirement_date.after_or_equal' => 'Return date must be on or after departure date.',
            'pax.required' => 'Number of passengers is required.',
            'pax.min' => 'At least 1 passenger is required.',
            'pax.max' => 'Maximum 500 passengers allowed.',
            'pax_free.lte' => 'Free passengers cannot exceed total passengers.',
            'phone.required' => 'Contact phone number is required.',
            'phone.regex' => 'Please enter a valid phone number.',
            'files.*.max' => 'Each file must not exceed 10MB.',
            'files.*.mimes' => 'Files must be of type: JPEG, JPG, PNG, GIF, PDF, DOC, DOCX.',
            'country_begin.required' => 'Starting country is required for confirmed tours.',
            'city_begin.required' => 'Starting city is required for confirmed tours.',
            'country_end.required' => 'Ending country is required for confirmed tours.',
            'city_end.required' => 'Ending city is required for confirmed tours.',
            'assigned_user.required' => 'An assigned user is required for confirmed tours.',
            'responsible.exists' => 'Selected responsible user does not exist.',
            'client_id.exists' => 'Selected client does not exist.',
            'city_begin.exists' => 'Selected starting city does not exist.',
            'city_end.exists' => 'Selected ending city does not exist.',
            'assigned_user.exists' => 'Selected assigned user does not exist.',
            'transfer_id.exists' => 'Selected transfer does not exist.'
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
            'pax_free' => 'free passengers',
            'retirement_date' => 'return date',
            'country_begin' => 'starting country',
            'city_begin' => 'starting city',
            'country_end' => 'ending country',
            'city_end' => 'ending city',
            'assigned_user' => 'assigned user',
            'total_amount' => 'total amount',
            'price_for_one' => 'price per person',
            'itinerary_tl' => 'itinerary team leader'
        ];
    }
}
