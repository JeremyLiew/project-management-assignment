<?php

namespace App\Models;

interface TaskInterface
{
    public function createTask(array $data);
    public function updateTask(Task $task, array $data);
}
