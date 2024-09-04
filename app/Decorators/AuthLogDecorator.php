<?php
namespace App\Decorators;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthLogDecorator extends LogDecorator
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
        $log->model_type = 'Authentication';
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
            case 'Login Successful':
            case 'Registration Successful':
            case 'Logout Successful':
                return 'INFO';
            case 'Login Failed':
            case 'Validation Failed':
            case 'Registration Failed':
                return 'ERROR';
            default:
                return 'DEBUG';
        }
    }
}
