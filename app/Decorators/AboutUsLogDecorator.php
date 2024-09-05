<?php
// Jeremy
namespace App\Decorators;

use Illuminate\Http\Request;

class AboutUsLogDecorator extends LogDecorator
{
    protected $logLevel;

    public function __construct(Request $request)
    {
        $logComponent = new BasicLogComponent('About Us');
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
