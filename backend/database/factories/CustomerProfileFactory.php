<?php

namespace Database\Factories;

use App\Models\CustomerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomerProfileFactory extends Factory
{
    protected $model = CustomerProfile::class;

    public function definition(): array
    {
        return [
            'gender'         => fake()->randomElement(['male', 'female', 'other']),
            'birth_date'     => fake()->dateTimeBetween('-60 years', '-18 years'),
            'loyalty_points' => fake()->numberBetween(0, 5000),
            'loyalty_tier'   => fake()->randomElement(['bronze', 'silver', 'gold']),
            'total_spent'    => fake()->randomFloat(2, 0, 100000),
            'referral_code'  => strtoupper(Str::random(8)),
        ];
    }
}
