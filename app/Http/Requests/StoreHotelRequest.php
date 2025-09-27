<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\ValidationService;

class StoreHotelRequest extends FormRequest
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
            'address_first' => trim($this->address_first ?? ''),
            'city' => trim($this->city ?? ''),
            'country' => trim($this->country ?? ''),
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
        return ValidationService::hotelBusinessRules() + [
            // Star rating
            'star_rating' => 'nullable|integer|min:1|max:5',

            // Booking and reference information
            'booking_contact' => 'nullable|string|max:191',
            'booking_contact_email' => ValidationService::emailRules(false),
            'booking_contact_phone' => ValidationService::phoneRules(false),
            'reference_id' => 'nullable|string|max:100',

            // Location and coordinates
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'google_place_id' => 'nullable|string|max:255',

            // Additional contact
            'work_fax' => ValidationService::phoneRules(false),

            // File uploads
            'files' => 'nullable|array',
            'files.*' => ValidationService::fileRules(false, 10240, 'jpeg,jpg,png,gif,pdf,doc,docx'),

            // Room types and pricing
            'room_types' => 'nullable|array',
            'room_types.*' => 'integer|exists:room_types,id',
            'room_prices' => 'nullable|array',
            'room_prices.*' => 'numeric|min:0|max:99999.99',

            // Services and amenities
            'services' => 'nullable|array',
            'services.*' => 'integer|exists:services,id',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:191'
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
            'name.required' => 'Hotel name is required.',
            'name.regex' => 'Hotel name can only contain letters, numbers, spaces, and common business symbols.',
            'address_first.required' => 'Hotel address is required.',
            'address_first.min' => 'Hotel address must be at least 5 characters.',
            'city.required' => 'City is required.',
            'country.required' => 'Country is required.',
            'work_phone.required' => 'Work phone number is required.',
            'work_phone.regex' => 'Please enter a valid work phone number.',
            'work_email.required' => 'Work email address is required.',
            'work_email.unique' => 'This work email address is already registered.',
            'star_rating.min' => 'Star rating must be between 1 and 5.',
            'star_rating.max' => 'Star rating must be between 1 and 5.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'room_prices.*.max' => 'Room price cannot exceed 99,999.99.',
            'files.*.max' => 'Each file must not exceed 10MB.',
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
            'name' => 'hotel name',
            'address_first' => 'hotel address',
            'star_rating' => 'star rating',
            'booking_contact' => 'booking contact',
            'booking_contact_email' => 'booking contact email',
            'booking_contact_phone' => 'booking contact phone',
            'work_fax' => 'fax number'
        ];
    }
}