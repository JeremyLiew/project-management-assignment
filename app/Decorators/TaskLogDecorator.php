<?php
// Jeremy
namespace App\Decorators;

use Illuminate\Http\Request;

class TaskLogDecorator extends LogDecorator
{
    protected $logLevel;

    public function __construct($loggable, Request $request)
    {
        $logComponent = new BasicLogComponent('Task', $loggable ? $loggable->id : null);
        parent::__construct($logComponent);
        $this->logLevel = $this->determineLogLevel($loggable, $request);
    }

    public function logAction($action, $details)
    {
        $this->logComponent->logLevel = $this->logLevel;
        $this->logComponent->logAction($action, $details);
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
