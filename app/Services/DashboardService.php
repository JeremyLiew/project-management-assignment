<?php

namespace App\Services;

use App\Strategies\StrategyInterface;
use App\Strategies\MultiParameterStrategyInterface;

class DashboardService
{
    protected $strategy;

    public function setStrategy($strategy)
    {
        if (!$strategy instanceof StrategyInterface && !$strategy instanceof MultiParameterStrategyInterface) {
            throw new \InvalidArgumentException("Invalid strategy");
        }
        $this->strategy = $strategy;
    }

    public function executeStrategy(...$data)
    {
        if ($this->strategy instanceof MultiParameterStrategyInterface) {
            return $this->strategy->execute(...$data);
        }

        return $this->strategy->execute($data[0]);
    }
}
