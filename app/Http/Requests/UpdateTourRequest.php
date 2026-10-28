<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Http\Requests\Concerns\ResolvesCityIds;
use App\Http\Requests\Concerns\TourFieldRules;
use App\Tour;

class UpdateTourRequest extends FormRequest
{
    use ResolvesCityIds, TourFieldRules;

    /** @var Tour|null|false */
    private $currentTour = false;

    /**
     * The tour being edited. POST /tour/{id}/update names the parameter id; the resource PUT route names it tour.
     */
    private function currentTour()
    {
        if ($this->currentTour === false) {
            $id = $this->route('id') ?? $this->route('tour');
            $this->currentTour = $id ? Tour::find($id) : null;
        }

        return $this->currentTour;
    }

    /**
     * Rules for one inline-edited field, matching the full edit form.
     */
    private function inlineValueRules($field)
    {
        $tour = $this->currentTour();

        switch ($field) {
            case 'name':
                return ['required', 'string', 'min:3', 'max:191', 'regex:' . self::$nameRegex];
            case 'external_name':
                return ['required', 'string', 'max:191', 'regex:/^[a-zA-Z0-9\-_]+$/',
                    Rule::unique('tours', 'external_name')->ignore($tour ? $tour->id : null)];
            case 'departure_date':
            case 'retirement_date':
                // Order against the other date is checked in TourController@update
                return ['required', 'date_format:Y-m-d'];
            case 'status':
                return ['required', 'integer', 'exists:status,id'];
            case 'pax':
                return ['required', 'integer', 'min:1', 'max:500',
                    'min:' . max(1, (int) ($tour->pax_free ?? 0))];
            case 'pax_free':
                return ['required', 'integer', 'min:0', 'max:50', 'max:' . (int) ($tour->pax ?? 0)];
            case 'phone':
                return ['nullable', 'string', 'max:20', 'regex:/^[\+]?[0-9\s\-\(\)]+$/'];
            case 'itinerary_tl':
                return ['nullable', 'string', 'max:121'];
            case 'city_begin':
            case 'city_end':
                // Google Places place_id; a new city is only created from a validated place
                return ['required', 'string', 'max:191', 'regex:/^[A-Za-z0-9_-]+$/'];
            default: // country_begin, country_end
                return ['nullable', 'string', 'max:191', 'exists:countries,alias'];
        }
    }

    /** Fields the inline editors (touredit-* cells) may change one at a time */
    private const INLINE_FIELDS = [
        'name', 'external_name', 'departure_date', 'retirement_date', 'status',
        'country_begin', 'city_begin', 'country_end', 'city_end',
        'pax', 'pax_free', 'phone', 'itinerary_tl',
    ];

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
        // The edit form posts cities as names; validation expects cities.id
        $this->mergeResolvedCityIds();
    }

    /**
     * Inline edits post a single fieldName/fieldValue pair, not the whole form.
     */
    private function isInlineEdit()
    {
        return $this->ajax() && $this->filled('fieldName');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $tour = $this->currentTour();
        $tourId = $tour ? $tour->id : null;

        if ($this->isInlineEdit()) {
            $field = $this->input('fieldName');
            $rules = [
                'fieldName' => ['required', Rule::in(self::INLINE_FIELDS)],
                'fieldValue' => in_array($field, self::INLINE_FIELDS, true) ? $this->inlineValueRules($field) : ['nullable'],
            ];
            if (in_array($field, ['city_begin', 'city_end'], true)) {
                $rules['cityName'] = ['required', 'string', 'max:191', 'not_regex:/[<>]/'];
                $rules['countryAlias'] = ['required', 'string', 'exists:countries,alias'];
            }

            return $rules;
        }
        $maxDate = Carbon::now()->addYears(2)->format('Y-m-d');
        $minDate = Carbon::now()->subDays(30)->format('Y-m-d'); // Allow 30 days in past for updates

        // The 30-day limit stops moving a tour into the past; a running or finished tour
        // saved with its departure date unchanged must still be editable.
        $storedDeparture = $tour && $tour->departure_date ? Carbon::parse($tour->departure_date)->format('Y-m-d') : null;
        try {
            $postedDeparture = $this->filled('departure_date') ? Carbon::parse($this->input('departure_date'))->format('Y-m-d') : null;
        } catch (\Exception $e) {
            $postedDeparture = null; // the date rule reports it
        }
        $departureChanged = !$storedDeparture || $postedDeparture !== $storedDeparture;

        $rules = [
            // Basic Information
            'name' => [
                'required',
                'string',
                'max:191',
                'min:3',
                'regex:' . self::$nameRegex
            ],
            // TourController@update regenerates the external name on save
            'external_name' => [
                'nullable',
                'string',
                'max:191',
                Rule::unique('tours', 'external_name')->ignore($tourId),
                'regex:/^[a-zA-Z0-9\-_]+$/' // URL-friendly format
            ],
            'overview' => 'nullable|string|max:5000',
            'remark' => 'nullable|string|max:5000',

            // Dates - More flexible for updates
            'departure_date' => array_values(array_filter([
                'required',
                'date',
                $departureChanged ? 'after_or_equal:' . $minDate : null,
                // Order against the return date is checked (with its own message) on retirement_date
                'before_or_equal:' . $maxDate,
            ])),
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
            // Optional, as on create: new tours have no phone yet
            'phone' => [
                'nullable',
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
                'country_begin' => 'nullable|string|max:191',
                'city_begin' => 'nullable|integer|exists:cities,id',
                'country_end' => 'nullable|string|max:191',
                'city_end' => 'nullable|integer|exists:cities,id',
                'assigned_user' => 'required',
                'assigned_user.*' => 'integer|exists:users,id',
                'transfer_id' => 'nullable|integer|exists:transfers,id'
            ]);
        } else {
            $rules = array_merge($rules, [
                'country_begin' => 'nullable|string|max:191',
                'city_begin' => 'nullable|integer',
                'country_end' => 'nullable|string|max:191',
                'city_end' => 'nullable|integer',
                'assigned_user' => 'nullable',
                'assigned_user.*' => 'integer|exists:users,id',
                'transfer_id' => 'nullable|integer'
            ]);
        }

        return array_merge($rules, $this->childAndRoomRules());
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
            'name.regex' => self::$nameRegexMessage,
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
        if ($this->isInlineEdit()) {
            return ['fieldValue' => str_replace('_', ' ', (string) $this->input('fieldName'))];
        }

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
