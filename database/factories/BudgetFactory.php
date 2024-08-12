<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    protected $model = \App\Models\Budget::class;

    public function definition()
    {
        return [
            'total_amount' => fake()->randomFloat(2, 5000, 50000),
            // 'project_id' => \App\Models\Project::factory(), // Automatically create a related project
        ];
    }
}
