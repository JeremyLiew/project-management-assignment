<?php

namespace App\Models;

use App\Models\Log;

class LoggingDecorator implements TaskInterface
{
    protected $taskService;

    public function __construct(TaskInterface $taskService)
    {
        $this->taskService = $taskService;
    }

    public function createTask(array $data)
    {
        $task = $this->taskService->createTask($data);
        $this->logAction('created', $task);
        return $task;
    }

    public function updateTask(Task $task, array $data)
    {
        $originalData = $task->getOriginal();
        $task = $this->taskService->updateTask($task, $data);
        $this->logAction('updated', $task, $originalData);
        return $task;
    }

    protected function logAction($action, Task $task, $originalData = null)
    {
        Log::create([
            'action' => $action,
            'model_type' => Task::class,
            'model_id' => $task->id,
            'user_id' => auth()->id(),
            'changes' => json_encode($task->getChanges())
        ]);
    }
}
