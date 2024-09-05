<?php
// Jeremy
namespace App\Decorators;

use Illuminate\Http\Request;

class AuthLogDecorator extends LogDecorator
{
    protected $logLevel;

    public function __construct(Request $request)
    {
        $logComponent = new BasicLogComponent('Authentication');
        parent::__construct($logComponent);
        $this->logLevel = $this->determineLogLevel(null);
    }

    public function logAction($action, $details)
    {
        $this->logLevel = $this->determineLogLevel($action);
        $this->logComponent->logLevel = $this->logLevel;
        $this->logComponent->logAction($action, $details);
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
