<?php
// Jeremy
namespace App\Decorators;

use Illuminate\Http\Request;

class ProjectLogDecorator extends LogDecorator
{
    protected $logLevel;

    public function __construct($loggable, Request $request)
    {
        $logComponent = new BasicLogComponent('Project', $loggable ? $loggable->id : null);
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

