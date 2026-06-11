<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'uuid'              => (string) Str::uuid(),
            'first_name'        => fake()->firstName(),
            'last_name'         => fake()->lastName(),
            'email'             => fake()->unique()->safeEmail(),
            'phone'             => fake()->unique()->numerify('01#########'),
            'email_verified_at' => now(),
            'password'          => 'Password1!',
            'avatar'            => null,
            'status'            => 'active',
            'remember_token'    => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn() => ['email_verified_at' => null]);
    }

    public function suspended(): static
    {
        return $this->state(fn() => ['status' => 'suspended']);
    }

    public function admin(): static
    {
        return $this->afterCreating(function (User $user) {
            $role = \App\Models\Role::where('slug', 'administrator')->first();
            if ($role) $user->roles()->attach($role);
        });
    }

    public function customer(): static
    {
        return $this->afterCreating(function (User $user) {
            $role = \App\Models\Role::where('slug', 'customer')->first();
            if ($role) $user->roles()->attach($role);

            \App\Models\CustomerProfile::factory()->create(['user_id' => $user->id]);
        });
    }
}
