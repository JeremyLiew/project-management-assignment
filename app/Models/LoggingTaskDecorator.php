<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;

class LoggingTaskDecorator extends TaskDecorator
{
    public function getName()
    {
        $name = parent::getName();

        Log::info("Task Name accessed: " . $name);
        return $name;
    }
}
