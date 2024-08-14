<?php

namespace App\Models;

abstract class TaskDecorator implements TaskInterface
{
    protected $task;

    public function __construct(TaskInterface $task)
    {
        $this->task = $task;
    }

    public function getName()
    {
        return $this->task->getName();
    }

    public function getDescription()
    {
        return $this->task->getDescription();
    }

    public function getStatus()
    {
        return $this->task->getStatus();
    }

    public function getPriority()
    {
        return $this->task->getPriority();
    }
}
