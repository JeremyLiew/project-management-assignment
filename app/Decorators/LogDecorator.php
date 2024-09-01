<?php
// Jeremy
namespace App\Decorators;

use App\Interfaces\LogInterface;
use App\Models\Log;

abstract class LogDecorator implements LogInterface
{
    protected $loggable;

    public function __construct($loggable)
    {
        $this->loggable = $loggable;
    }
}
