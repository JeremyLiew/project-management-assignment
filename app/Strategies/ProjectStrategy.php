<?php
/**
 *
 * @author Liew Wei Lun
 */
namespace App\Strategies;

use DOMDocument;
use App\Models\Budget;
use App\Models\Task;

class ProjectStrategy implements StrategyInterface
{
    public function execute($projects)
    {
        $projectXml = new DOMDocument('1.0', 'UTF-8');
        $projectsElement = $projectXml->createElement('projects');

        foreach ($projects as $project) {
            $budget = Budget::find($project->budget_id);
            $totalCost = Task::where('project_id', $project->id)
                ->whereNotNull('expense_id')
                ->join('expenses', 'tasks.expense_id', '=', 'expenses.id')
                ->sum('expenses.amount') ?? 0;

            $completionTime = $project->completed_at 
                ? $project->created_at->diffInDays($project->completed_at) 
                : ($project->due_date && $project->due_date->isPast() ? 'Expired' : 'In Progress');

            $projectElement = $projectXml->createElement('project');
            $projectElement->appendChild($projectXml->createElement('name', htmlspecialchars($project->name ?? 'N/A')));
            $projectElement->appendChild($projectXml->createElement('description', htmlspecialchars($project->description ?? 'N/A')));
            $projectElement->appendChild($projectXml->createElement('budgetAmount', $budget->total_amount ?? 'N/A'));
            $projectElement->appendChild($projectXml->createElement('totalCost', $totalCost));
            $projectElement->appendChild($projectXml->createElement('Completion_project_time', $completionTime));

            $projectsElement->appendChild($projectElement);
        }

        $projectXml->appendChild($projectsElement);
        return $projectXml;
    }
}
