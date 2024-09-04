<?php
// Jeremy
namespace App\Decorators;

use Illuminate\Http\Request;

class BudgetLogDecorator extends LogDecorator
{
    protected $logLevel;

    public function __construct($loggable, Request $request)
    {
        $logComponent = new BasicLogComponent('Budget', $loggable ? $loggable->id : null);
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
            case 'Fetched Budgets Data':
            case 'Created':
            case 'Updated':
            case 'Viewed':
                return 'INFO';
            case 'Failed to Fetch Budgets':
            case 'Failed to Create Budget':
            case 'Failed to Fetch Budget for Editing':
            case 'Failed to Update Budget':
            case 'Failed to Delete Budget':
                return 'ERROR';
            case 'Failed to Fetch Budgets':
                return 'ERROR';
            case 'Deleted':
                return 'WARNING';
            default:
                return 'DEBUG';
        }
    }
}
