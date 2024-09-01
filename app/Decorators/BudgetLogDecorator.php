<?php
// Jeremy
namespace App\Decorators;

use App\Models\Log;

class BudgetLogDecorator extends LogDecorator
{
    public function logAction($action, $details)
    {
        $log = new Log();
        $log->action = $action;
        $log->model_type = 'Budget';
        $log->model_id = $this->loggable->id;
        $log->user_id = auth()->id(); // Assuming the user is authenticated
        $log->changes = json_encode($details);
        $log->save();
    }
}
