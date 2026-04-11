<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DriverVehicle>
 */
class DriverVehicleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'driver_id'     => User::factory(),
            'vehicle_type'  => fake()->randomElement(['سيارة صغيرة', 'سيارة دفع رباعي', 'شاحنة', 'فان', 'دراجة نارية']),
            'license_plate' => fake()->regexify('[A-Z]{3}-[0-9]{4}'),
        ];
    }
}
