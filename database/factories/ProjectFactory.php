<?php

namespace Database\Factories;

use App\Models\Budget;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{

    public function definition(): array
    {

        $budget = Budget::inRandomOrder()->first();
        return [
            'name' => 'Default Project Name',
            'description' => fake()->sentence(),
            'budget_id' => $budget->id,
        ];
    }
}
