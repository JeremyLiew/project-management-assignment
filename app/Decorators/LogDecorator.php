<?php

namespace App\Decorators;

use App\Models\Log;

abstract class LogDecorator
{
    protected $loggable;

    public function __construct($loggable)
    {
        $this->loggable = $loggable;
    }

    abstract public function logAction($action, $details);
}
