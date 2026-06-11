<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'order_number'   => 'ORD-' . fake()->unique()->numerify('########'),
            'customer_id'    => User::factory(),
            'subtotal'       => fake()->randomFloat(2, 50, 5000),
            'total_amount'   => fake()->randomFloat(2, 50, 5000),
            'order_status'   => 'pending',
            'payment_status' => 'pending',
        ];
    }
}
