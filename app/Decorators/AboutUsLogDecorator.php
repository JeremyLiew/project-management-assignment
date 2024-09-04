<?php
// Jeremy
namespace App\Decorators;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AboutUsLogDecorator extends LogDecorator
{
    protected $logLevel = null;
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
        $log->model_type = 'About Us';
        $log->model_id = null;
        $log->user_id = Auth::check() ? auth()->id() : null;
        $log->log_level = $this->logLevel;
        $log->changes = json_encode($details);
        $log->ip_address = $this->ipAddress;
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
