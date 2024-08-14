<?php

namespace App\Models;

interface TaskInterface
{
    public function getName();
    public function getDescription();
    public function getStatus();
    public function getPriority();
}
