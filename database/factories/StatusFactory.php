<?php

namespace Database\Factories;

use App\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatusFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Status::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'type' => $this->faker->randomElement(['tour', 'hotel', 'restaurant', 'transfer', 'guide', 'event']),
            'color' => $this->faker->hexColor(),
            'description' => $this->faker->optional()->sentence(),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the status is for tours.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forTour()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'tour',
            ];
        });
    }

    /**
     * Indicate that the status is inactive.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}