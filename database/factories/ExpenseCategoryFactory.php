<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseCategoryFactory extends Factory
{


    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}
