<?php
// Jeremy
namespace App\Decorators;

use App\Interfaces\LogInterface;
use App\Models\Log;

class BasicLogComponent implements LogInterface
{
    protected $modelType;
    protected $modelId;

    public function __construct($modelType = null, $modelId = null)
    {
        $this->modelType = $modelType;
        $this->modelId = $modelId;
    }

    public function logAction($action, $details)
    {
        $log = new Log();
        $log->action = $action;
        $log->model_type = $this->modelType;
        $log->model_id = $this->modelId;
        $log->user_id = auth()->check() ? auth()->id() : null;
        $log->log_level = 'INFO'; // Default log level
        $log->changes = json_encode($details);
        $log->ip_address = request()->ip();
        $log->save();
    }
}
