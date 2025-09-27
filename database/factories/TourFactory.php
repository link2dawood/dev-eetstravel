<?php

namespace Database\Factories;

use App\Tour;
use App\Status;
use App\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class TourFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tour::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $departureDate = $this->faker->dateTimeBetween('now', '+6 months');
        $retirementDate = (clone $departureDate)->modify('+' . $this->faker->numberBetween(3, 14) . ' days');

        return [
            'name' => $this->faker->unique()->words(3, true) . ' Tour',
            'overview' => $this->faker->paragraphs(2, true),
            'remark' => $this->faker->optional()->paragraph(),
            'departure_date' => $departureDate->format('Y-m-d'),
            'retirement_date' => $retirementDate->format('Y-m-d'),
            'pax' => $this->faker->numberBetween(10, 50),
            'rooms' => $this->faker->numberBetween(5, 25),
            'country_begin' => $this->faker->country(),
            'city_begin' => $this->faker->city(),
            'country_end' => $this->faker->optional()->country(),
            'city_end' => $this->faker->optional()->city(),
            'invoice' => $this->faker->optional()->numerify('INV-####'),
            'ga' => $this->faker->optional()->company(),
            'status' => $this->faker->numberBetween(1, 4),
            'external_name' => $this->faker->optional()->words(2, true),
            'client_id' => null, // Will be set in states or when needed
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the tour is a draft.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 1,
            ];
        });
    }

    /**
     * Indicate that the tour is active.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 2,
            ];
        });
    }

    /**
     * Indicate that the tour is cancelled.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 3,
            ];
        });
    }

    /**
     * Indicate that the tour is completed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 4,
                'departure_date' => $this->faker->dateTimeBetween('-6 months', '-1 month')->format('Y-m-d'),
                'retirement_date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            ];
        });
    }

    /**
     * Indicate that the tour has started.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function started()
    {
        return $this->state(function (array $attributes) {
            return [
                'departure_date' => $this->faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d'),
            ];
        });
    }

    /**
     * Indicate that the tour is upcoming.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function upcoming()
    {
        return $this->state(function (array $attributes) {
            return [
                'departure_date' => $this->faker->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d'),
            ];
        });
    }

    /**
     * Indicate that the tour has a client.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withClient()
    {
        return $this->state(function (array $attributes) {
            return [
                'client_id' => Client::factory(),
            ];
        });
    }

    /**
     * Indicate that the tour is for a large group.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function largeGroup()
    {
        return $this->state(function (array $attributes) {
            $pax = $this->faker->numberBetween(40, 100);
            return [
                'pax' => $pax,
                'rooms' => (int) ceil($pax / 2), // Assume 2 people per room
            ];
        });
    }

    /**
     * Indicate that the tour is for a small group.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function smallGroup()
    {
        return $this->state(function (array $attributes) {
            $pax = $this->faker->numberBetween(5, 15);
            return [
                'pax' => $pax,
                'rooms' => (int) ceil($pax / 2), // Assume 2 people per room
            ];
        });
    }

    /**
     * Indicate that the tour is long duration.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function longDuration()
    {
        return $this->state(function (array $attributes) {
            $departureDate = $this->faker->dateTimeBetween('now', '+3 months');
            $retirementDate = (clone $departureDate)->modify('+' . $this->faker->numberBetween(15, 30) . ' days');

            return [
                'departure_date' => $departureDate->format('Y-m-d'),
                'retirement_date' => $retirementDate->format('Y-m-d'),
            ];
        });
    }

    /**
     * Indicate that the tour is short duration.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function shortDuration()
    {
        return $this->state(function (array $attributes) {
            $departureDate = $this->faker->dateTimeBetween('now', '+3 months');
            $retirementDate = (clone $departureDate)->modify('+' . $this->faker->numberBetween(1, 5) . ' days');

            return [
                'departure_date' => $departureDate->format('Y-m-d'),
                'retirement_date' => $retirementDate->format('Y-m-d'),
            ];
        });
    }

    /**
     * Indicate that the tour is domestic.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function domestic()
    {
        return $this->state(function (array $attributes) {
            $country = $this->faker->country();
            return [
                'country_begin' => $country,
                'country_end' => $country,
                'city_begin' => $this->faker->city(),
                'city_end' => $this->faker->city(),
            ];
        });
    }

    /**
     * Indicate that the tour is international.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function international()
    {
        return $this->state(function (array $attributes) {
            return [
                'country_begin' => $this->faker->country(),
                'country_end' => $this->faker->country(),
                'city_begin' => $this->faker->city(),
                'city_end' => $this->faker->city(),
            ];
        });
    }

    /**
     * Configure the model factory after creating.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (Tour $tour) {
            // Generate tour days after creation
            $this->generateTourDays($tour);
        });
    }

    /**
     * Generate tour days for the tour.
     *
     * @param Tour $tour
     */
    protected function generateTourDays(Tour $tour)
    {
        if (!$tour->retirement_date) {
            return;
        }

        $startDate = Carbon::parse($tour->departure_date);
        $endDate = Carbon::parse($tour->retirement_date);

        $currentDate = $startDate->copy();
        $dayNumber = 1;

        while ($currentDate <= $endDate) {
            \App\TourDay::create([
                'tour_id' => $tour->id,
                'date' => $currentDate->toDateString(),
                'day_number' => $dayNumber,
                'title' => "Day {$dayNumber}",
                'description' => $this->faker->optional()->sentence(),
            ]);

            $currentDate->addDay();
            $dayNumber++;
        }
    }
}