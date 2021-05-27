<?php

namespace Database\Factories;

use App\BookingService;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BookingService::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'service_name' => $this->faker->name,
        ];
    }
}
