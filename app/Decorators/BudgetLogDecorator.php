<?php
// Jeremy
namespace App\Decorators;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class BudgetLogDecorator extends LogDecorator
{
    protected $logLevel = null;

    public function logAction($action, $details)
    {
        $this->logLevel = $this->determineLogLevel($action);

        $log = new Log();
        $log->action = $action;
        $log->model_type = 'Budget';
        $log->model_id = $this->loggable ? $this->loggable->id : null;
        $log->user_id = Auth::check() ? auth()->id() : null;
        $log->log_level = $this->logLevel;
        $log->changes = json_encode($details);
        $log->save();
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
