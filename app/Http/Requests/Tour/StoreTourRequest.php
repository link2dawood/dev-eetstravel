<?php

namespace App\Http\Requests\Tour;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTourRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return auth()->check() && auth()->user()->can('tour.create');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:3',
                Rule::unique('tours')->whereNull('deleted_at')
            ],
            'overview' => 'nullable|string|max:5000',
            'remark' => 'nullable|string|max:2000',
            'departure_date' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'retirement_date' => [
                'nullable',
                'date',
                'after:departure_date'
            ],
            'pax' => [
                'required',
                'integer',
                'min:1',
                'max:1000'
            ],
            'rooms' => [
                'nullable',
                'integer',
                'min:1',
                'max:500'
            ],
            'country_begin' => 'required|string|max:100',
            'city_begin' => 'required|string|max:100',
            'country_end' => 'nullable|string|max:100',
            'city_end' => 'nullable|string|max:100',
            'invoice' => 'nullable|string|max:255',
            'ga' => 'nullable|string|max:255',
            'status' => [
                'required',
                'integer',
                'exists:statuses,id'
            ],
            'responsible_users' => 'nullable|array',
            'responsible_users.*' => 'exists:users,id',
            'assigned_users' => 'nullable|array',
            'assigned_users.*' => 'exists:users,id',
            'external_name' => 'nullable|string|max:255',
            'client_id' => 'nullable|exists:clients,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages()
    {
        return [
            'name.required' => 'Tour name is required.',
            'name.unique' => 'A tour with this name already exists.',
            'name.min' => 'Tour name must be at least 3 characters.',
            'departure_date.required' => 'Departure date is required.',
            'departure_date.after_or_equal' => 'Departure date cannot be in the past.',
            'retirement_date.after' => 'Return date must be after departure date.',
            'pax.required' => 'Number of passengers is required.',
            'pax.min' => 'At least 1 passenger is required.',
            'pax.max' => 'Maximum 1000 passengers allowed.',
            'rooms.min' => 'At least 1 room is required.',
            'rooms.max' => 'Maximum 500 rooms allowed.',
            'country_begin.required' => 'Starting country is required.',
            'city_begin.required' => 'Starting city is required.',
            'status.required' => 'Tour status is required.',
            'status.exists' => 'Selected status is invalid.',
            'responsible_users.*.exists' => 'One or more selected responsible users are invalid.',
            'assigned_users.*.exists' => 'One or more selected assigned users are invalid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'pax' => 'passengers',
            'ga' => 'general agent',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Clean and format data before validation
        $this->merge([
            'name' => trim($this->name),
            'overview' => $this->overview ? trim($this->overview) : null,
            'remark' => $this->remark ? trim($this->remark) : null,
            'country_begin' => $this->country_begin ? trim($this->country_begin) : null,
            'city_begin' => $this->city_begin ? trim($this->city_begin) : null,
            'country_end' => $this->country_end ? trim($this->country_end) : null,
            'city_end' => $this->city_end ? trim($this->city_end) : null,
            'pax' => $this->pax ? (int) $this->pax : null,
            'rooms' => $this->rooms ? (int) $this->rooms : null,
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Custom validation logic
            if ($this->departure_date && $this->retirement_date) {
                $departure = \Carbon\Carbon::parse($this->departure_date);
                $retirement = \Carbon\Carbon::parse($this->retirement_date);

                if ($retirement->diffInDays($departure) > 365) {
                    $validator->errors()->add('retirement_date', 'Tour duration cannot exceed 365 days.');
                }
            }

            // Validate that at least one responsible user is assigned
            if (empty($this->responsible_users)) {
                $validator->errors()->add('responsible_users', 'At least one responsible user must be assigned.');
            }

            // Business logic validation
            if ($this->pax && $this->rooms) {
                $avgPaxPerRoom = $this->pax / $this->rooms;
                if ($avgPaxPerRoom > 4) {
                    $validator->errors()->add('rooms', 'Too many passengers per room. Please add more rooms or reduce passengers.');
                }
            }
        });
    }
}