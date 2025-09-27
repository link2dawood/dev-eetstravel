<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class ValidTourDateRange implements Rule
{
    private $departureDate;
    private $errorMessage;

    /**
     * Create a new rule instance.
     *
     * @param string $departureDate
     */
    public function __construct($departureDate = null)
    {
        $this->departureDate = $departureDate;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value (retirement_date)
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            if (!$this->departureDate || !$value) {
                return true; // Let other rules handle required validation
            }

            $departure = Carbon::createFromFormat('Y-m-d', $this->departureDate);
            $retirement = Carbon::createFromFormat('Y-m-d', $value);

            // Check if tour duration is reasonable (1 day to 90 days)
            $duration = $departure->diffInDays($retirement);

            if ($duration < 0) {
                $this->errorMessage = 'Return date must be after departure date.';
                return false;
            }

            if ($duration > 90) {
                $this->errorMessage = 'Tour duration cannot exceed 90 days.';
                return false;
            }

            if ($duration === 0) {
                // Same day tours are allowed but let's warn about it
                return true;
            }

            // Check for reasonable business logic
            // Tours departing in the next 7 days should have shorter duration
            $daysUntilDeparture = Carbon::now()->diffInDays($departure, false);

            if ($daysUntilDeparture < 7 && $daysUntilDeparture >= 0 && $duration > 30) {
                $this->errorMessage = 'Tours departing within 7 days should not exceed 30 days duration.';
                return false;
            }

            return true;

        } catch (\Exception $e) {
            $this->errorMessage = 'Invalid date format provided.';
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->errorMessage ?: 'The tour date range is invalid.';
    }
}
