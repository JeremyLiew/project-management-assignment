<?php
// Jeremy
namespace App\Decorators;

use App\Models\Log;

class AboutUsLogDecorator extends LogDecorator
{
    protected $logLevel = null;

    public function __construct($loggable)
    {
        parent::__construct($loggable);
    }

    public function logAction($action, $details)
    {
        $this->logLevel = $this->determineLogLevel($action);

        $log = new Log();
        $log->action = $action;
        $log->model_type = 'About Us';
        $log->model_id = null;
        $log->user_id = auth()->id();
        $log->log_level = $this->logLevel;
        $log->changes = json_encode($details);
        $log->save();
    }

    private function determineLogLevel($action)
    {
        switch ($action) {
            case 'Fetched About Us Data':
            case 'Search Members':
                return 'INFO';
            case 'Fetch Error':
            case 'Search Error':
                return 'WARNING';
            case 'Fetch Failure':
            case 'Search Failure':
                return 'ERROR';
            default:
                return 'DEBUG';
        }
    }
}
