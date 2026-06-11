<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        return [
            'method_name'  => fake()->word(),
            'display_name' => fake()->word(),
            'slug'         => fake()->unique()->slug(),
            'status'       => 'active',
        ];
    }
}
