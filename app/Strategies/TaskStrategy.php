<?php

namespace App\Strategies;

use DOMDocument;

class TaskStrategy implements StrategyInterface
{
    public function execute($tasks)
    {
        $taskXml = new DOMDocument('1.0', 'UTF-8');
        $tasksElement = $taskXml->createElement('tasks');

        foreach ($tasks as $task) {
            $taskElement = $taskXml->createElement('task');
            $taskElement->appendChild($taskXml->createElement('name', htmlspecialchars($task->name ?? 'N/A')));
            $taskElement->appendChild($taskXml->createElement('cost', $task->expense ? $task->expense->amount : 0));
            $taskElement->appendChild($taskXml->createElement('created_at', $task->created_at ? $task->created_at->format('Y-m-d') : 'N/A'));
            $taskElement->appendChild($taskXml->createElement('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : 'N/A'));
            $taskElement->appendChild($taskXml->createElement('Completion_task_time', $task->created_at->diffInDays($task->updated_at)));
            $taskElement->appendChild($taskXml->createElement('status', htmlspecialchars($task->status)));

            $projectNameElement = $taskXml->createElement('project_name', htmlspecialchars($task->project->name));
            $taskElement->appendChild($projectNameElement);

            $tasksElement->appendChild($taskElement);
        }

        $taskXml->appendChild($tasksElement);
        return $taskXml;
    }
}
