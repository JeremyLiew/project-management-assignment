<?php
// Jeremy
namespace App\Decorators;

use App\Models\Log;

class TaskLogDecorator extends LogDecorator
{
    protected $logLevel;

    public function logAction($action, $details)
    {
        $this->logLevel = $this->determineLogLevel($action);

        $log = new Log();
        $log->action = $action;
        $log->model_type = 'Task';
        $log->model_id = $this->loggable ? $this->loggable->id : null;
        $log->user_id = auth()->id();
        $log->log_level = $this->logLevel;
        $log->changes = json_encode($details);
        $log->save();
    }

    private function determineLogLevel($action)
    {
        switch ($action) {
            case 'Fetched Tasks Data':
            case 'Created':
            case 'Updated':
            case 'Viewed':
                return 'INFO';
            case 'Failed to Fetch Tasks':
            case 'Failed to Create Task':
            case 'Failed to Fetch Task for Editing':
            case 'Failed to Update Task':
            case 'Failed to Delete Task':
                return 'ERROR';
            case 'Deleted':
                return 'WARNING';
            default:
                return 'DEBUG';
        }
    }
}
