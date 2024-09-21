<?php
/**
 *
 * @author Liew Wei Lun
 */
namespace App\Strategies;

use DOMDocument;
use App\Models\Task;

class TaskStrategy implements StrategyInterface
{
    public function execute($tasks)
    {
        // Ensure tasks are loaded with their related project and expense
        if (!$tasks->first()->relationLoaded('project') || !$tasks->first()->relationLoaded('expense')) {
            $tasks = Task::with(['project', 'expense'])->whereIn('id', $tasks->pluck('id'))->get();
        }

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

            $tasksElement->appendChild($taskElement);
        }

        $taskXml->appendChild($tasksElement);
        return $taskXml;
    }
}