<?php

namespace App\Strategies;

use DOMDocument;

class CompletedTasksStrategy implements StrategyInterface
{
    public function execute($data)
    {
        $tasks = $data; // Expecting $data to be a collection of tasks
        
        $xmlDoc = new DOMDocument();
        $root = $xmlDoc->createElement("tasks");
        $xmlDoc->appendChild($root);

        foreach ($tasks as $task) {
            $taskElement = $xmlDoc->createElement("task");

            $taskElement->appendChild($xmlDoc->createElement("name", htmlspecialchars($task->name)));
            $taskElement->appendChild($xmlDoc->createElement("cost", $task->expense ? $task->expense->amount : 0));
            $taskElement->appendChild($xmlDoc->createElement("created_at", $task->created_at->format('Y-m-d')));
            $taskElement->appendChild($xmlDoc->createElement("due_date", $task->due_date ? $task->due_date->format('Y-m-d') : 'N/A'));
            $taskElement->appendChild($xmlDoc->createElement("Completion_task_time", $task->created_at->diffInDays($task->updated_at)));
            $taskElement->appendChild($xmlDoc->createElement("status", htmlspecialchars($task->status)));
            $taskElement->appendChild($xmlDoc->createElement("project_name", htmlspecialchars($task->project->name)));

            $root->appendChild($taskElement);
        }

        return $xmlDoc;
    }
}
