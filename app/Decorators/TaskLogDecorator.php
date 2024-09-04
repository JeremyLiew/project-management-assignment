<?php
// Jeremy
namespace App\Decorators;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskLogDecorator extends LogDecorator
{
    protected $logLevel;
    protected $ipAddress;

    public function __construct($loggable, Request $request)
    {
        parent::__construct($loggable);
        $this->ipAddress = $request->ip();
    }

    public function logAction($action, $details)
    {
        $this->logLevel = $this->determineLogLevel($action);

        $log = new Log();
        $log->action = $action;
        $log->model_type = 'Task';
        $log->model_id = $this->loggable ? $this->loggable->id : null;
        $log->user_id = Auth::check() ? auth()->id() : null;
        $log->log_level = $this->logLevel;
        $log->changes = json_encode($details);
        $log->ip_address = $this->ipAddress;
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
