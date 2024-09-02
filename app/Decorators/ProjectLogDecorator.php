<?php
// Jeremy
namespace App\Decorators;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class ProjectLogDecorator extends LogDecorator
{
    protected $logLevel = null;

    public function logAction($action, $details)
    {
        $this->logLevel = $this->determineLogLevel($action);

        $log = new Log();
        $log->action = $action;
        $log->model_type = 'Project';
        $log->model_id = $this->loggable ? $this->loggable->id : null;
        $log->user_id = Auth::check() ? auth()->id() : null;
        $log->log_level = $this->logLevel;
        $log->changes = json_encode($details);
        $log->save();
    }

    private function determineLogLevel($action)
    {
        switch ($action) {
            case 'Fetched Projects Data':
            case 'Created':
            case 'Updated':
            case 'Fetched':
            case 'Viewed':
                return 'INFO';
            case 'Failed to Fetch Projects':
            case 'Failed to Create Project':
            case 'Failed to Fetch Project for Editing':
            case 'Failed to Update Project':
            case 'Failed to Delete Project':
                return 'ERROR';
            case 'Deleted':
                return 'WARNING';
            default:
                return 'DEBUG';
        }
    }
}

