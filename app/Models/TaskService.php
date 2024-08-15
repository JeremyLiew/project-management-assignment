<?php

namespace App\Models;

use App\Models\Task;

class TaskService implements TaskInterface
{
    public function createTask(array $data)
    {
        return Task::create($data);
    }

    public function updateTask(Task $task, array $data)
    {
        $task->update($data);
        return $task;
    }
}
