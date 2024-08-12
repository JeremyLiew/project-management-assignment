<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        $budget = Budget::inRandomOrder()->first();
        $category = ExpenseCategory::inRandomOrder()->first();
        return [
            'amount' => $this->faker->randomFloat(2, 100, 1000),
            'description' => $this->faker->sentence(),
            'budget_id' => $budget ? $budget->id : null,
            'expense_category_id' => $category ? $category->id : null,
        ];
    }
}
