<?php

namespace Database\Factories;

use App\Models\Food;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

//factory for generating test food data
class FoodFactory extends Factory
{
    protected $model = Food::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->word() . ' ' . fake()->word(),
            'calories' => fake()->randomFloat(2, 10, 500),
            'protein' => fake()->randomFloat(2, 0, 50),
            'carbs' => fake()->randomFloat(2, 0, 80),
            'fat' => fake()->randomFloat(2, 0, 40),
            'fiber' => fake()->randomFloat(2, 0, 15),
            'sugar' => fake()->randomFloat(2, 0, 30),
            'sodium_mg' => fake()->randomFloat(2, 0, 500),
            'carbon_footprint_gco2e' => fake()->randomFloat(2, 5, 500),
        ];
    }
}