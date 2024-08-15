<?php

namespace App\Decorators;

use App\Models\Log;

class ProjectLogDecorator extends LogDecorator
{
    public function logAction($action, $details)
    {
        $log = new Log();
        $log->action = $action;
        $log->model_type = 'Project';
        $log->model_id = $this->loggable->id;
        $log->user_id = auth()->id();
        $log->changes = json_encode($details);
        $log->save();
    }
}
