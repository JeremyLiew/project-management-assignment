<?php

// app/Strategies/UserPerformanceStrategy.php
namespace App\Strategies;

use App\Models\Task;
use App\Models\User;

class UserPerformanceStrategy implements StrategyInterface
{
    public function execute($data)
    {
        $tasks = $data['tasks'];
        $userPerformance = [];
        
        foreach ($tasks as $task) {
            $userId = $task->user_id; 
            if (!$userId) continue; 

            $timeSpentOnTask = $task->updated_at->diffInMinutes($task->created_at);

            if (!isset($userPerformance[$userId])) {
                $userPerformance[$userId] = [
                    'userName' => User::find($userId)->name,
                    'timeSpent' => 0,
                ];
            }

            $userPerformance[$userId]['timeSpent'] += $timeSpentOnTask;
        }

        return [
            'labels' => array_column($userPerformance, 'userName'),
            'values' => array_column($userPerformance, 'timeSpent'),
        ];
    }
}
