<?php

// app/Strategies/GenerateXMLStrategy.php
namespace App\Strategies;

use DOMDocument;

class GenerateXMLStrategy implements StrategyInterface
{
    public function execute($data)
    {
        $projects = $data['projects'];
        $xml = new DOMDocument('1.0', 'UTF-8');
        $projectsElement = $xml->createElement('projects');

        foreach ($projects as $project) {
            $budget = $project['budget'];
            $totalCost = $project['totalCost'];
            $completionTime = $project['completionTime'];
    
            $projectElement = $xml->createElement('project');
            $projectElement->appendChild($xml->createElement('name', htmlspecialchars($project['name'] ?? 'N/A')));
            $projectElement->appendChild($xml->createElement('description', htmlspecialchars($project['description'] ?? 'N/A')));
            $projectElement->appendChild($xml->createElement('budgetAmount', $budget['total_amount'] ?? 'N/A'));
            $projectElement->appendChild($xml->createElement('totalCost', $totalCost));
            $projectElement->appendChild($xml->createElement('Completion_project_time', $completionTime));

            foreach ($project['tasks'] as $task) {
                $taskCost = $task['cost'];
                $completionTime = $task['completionTime'];
                $status = $task['status'];
    
                $taskElement = $projectElement->appendChild($xml->createElement('task'));
                $taskElement->appendChild($xml->createElement('name', htmlspecialchars($task['name'] ?? 'N/A')));
                $taskElement->appendChild($xml->createElement('cost', $taskCost));
                $taskElement->appendChild($xml->createElement('created_at', $task['created_at'] ? $task['created_at']->format('Y-m-d') : 'N/A'));
                $taskElement->appendChild($xml->createElement('due_date', $task['due_date'] ? $task['due_date']->format('Y-m-d') : 'N/A'));
                $taskElement->appendChild($xml->createElement('Completion_task_time', $completionTime));
                $taskElement->appendChild($xml->createElement('status', htmlspecialchars($status)));
            }

            $projectsElement->appendChild($projectElement);
        }

        $xml->appendChild($projectsElement);
        return $xml;
    }
}
