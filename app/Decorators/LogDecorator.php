<?php
// Jeremy
namespace App\Decorators;

use App\Interfaces\LogInterface;

abstract class LogDecorator implements LogInterface
{
    protected $logComponent;

    public function __construct(LogInterface $logComponent)
    {
        $this->logComponent = $logComponent;
    }

    public function logAction($action, $details)
    {
        $this->logComponent->logAction($action, $details);
    }
}
