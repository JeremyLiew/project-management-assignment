<?php

namespace App\Strategies;

use App\Models\Project;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\ProjectUserMapping; // Import the model

class TeamBudgetUtilizationStrategy implements MultiParameterStrategyInterface
{
    public function execute($projectId, $userId)
    {
        if ($userId != 1 || $userId != 2) {
            // Logic for non-admin users
            $project = Project::findOrFail($projectId);
            $budget = Budget::find($project->budget_id);
            $expenses = Expense::where('budget_id', $project->budget_id)->get();

            $totalExpenses = $expenses->sum('amount');

            return [
                'labels' => ['Project Budget', 'Project Expenses'],
                'values' => [$budget->total_amount, $totalExpenses],
            ];
        } else {
            // Logic for admin users
            // Retrieve projects associated with the user
            $projects = Project::whereHas('userMappings', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->get();

            $totalBudget = 0;
            $totalExpenses = 0;

            foreach ($projects as $project) {
                $budget = Budget::find($project->budget_id);
                $expenses = Expense::where('budget_id', $project->budget_id)->get();

                $totalBudget += $budget->total_amount;
                $totalExpenses += $expenses->sum('amount');
            }

            return [
                'labels' => ['Total Budget', 'Total Expenses'],
                'values' => [$totalBudget, $totalExpenses],
            ];
        }
    }
}
