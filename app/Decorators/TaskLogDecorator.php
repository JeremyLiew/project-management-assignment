<?php

namespace App\Decorators;

use App\Models\Log;

class TaskLogDecorator extends LogDecorator
{
    public function logAction($action, $details)
    {
        $log = new Log();
        $log->action = $action;
        $log->model_type = 'Task';
        $log->model_id = $this->loggable->id;
        $log->user_id = auth()->id();
        $log->changes = json_encode($details);
        $log->save();
    }
}
