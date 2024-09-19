<?php

// app/Strategies/BudgetUtilizationStrategy.php
namespace App\Strategies;

use App\Models\Budget;
use App\Models\Task;
use App\Models\Project;

class BudgetUtilizationStrategy implements StrategyInterface
{
    public function execute($data)
    {
        $projects = $data['projects'];
        $budgetUtilization = [];

        foreach ($projects as $project) {
            $budget = Budget::find($project->budget_id);
            if ($budget) {
                $totalBudget = $budget->total_amount;
                $totalExpenses = Task::where('project_id', $project->id)
                    ->join('expenses', 'tasks.expense_id', '=', 'expenses.id')
                    ->sum('expenses.amount');
                $utilizationPercentage = $totalBudget > 0 ? ($totalExpenses / $totalBudget) * 100 : 0;

                $budgetUtilization[] = [
                    'projectName' => $project->name,
                    'utilization' => $utilizationPercentage,
                ];
            }
        }

        return $budgetUtilization;
    }
}
