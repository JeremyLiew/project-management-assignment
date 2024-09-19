<?php

namespace App\Strategies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TeamUserPerformanceStrategy implements MultiParameterStrategyInterface
{
    public function execute($projectId, $userId)
    {
        $userPerformance = [];
        if ($userId != 1 || $userId != 2) {
            // Non-admin user logic
            
            $tasks = Task::where('project_id', $projectId)->with('user')->get();

            foreach ($tasks as $task) {
                $taskUserId = $task->user_id;
                if (!$taskUserId) continue;

                $timeSpentOnTask = $task->updated_at->diffInMinutes($task->created_at);

                if (!isset($userPerformance[$taskUserId])) {
                    $userPerformance[$taskUserId] = [
                        'userName' => User::find($taskUserId)->name,
                        'timeSpent' => 0,
                    ];
                }

                $userPerformance[$taskUserId]['timeSpent'] += $timeSpentOnTask;
            }

            $userNames = [];
            $timeSpent = [];

            foreach ($userPerformance as $performance) {
                $userNames[] = $performance['userName'];
                $timeSpent[] = $performance['timeSpent'];
            }

            return [
                'labels' => $userNames,
                'values' => array_map(function ($name, $time) {
                    return [
                        'userName' => $name,
                        'timeSpent' => $time,
                    ];
                }, $userNames, $timeSpent),
            ];
        } else {
            // Admin user logic
            // Retrieve all tasks for the project and group by user
            $tasks = Task::where('project_id', $projectId)
                ->select('user_id', DB::raw('SUM(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as time_spent'))
                ->groupBy('user_id')
                ->with('user')
                ->get();

            
            foreach ($tasks as $task) {
                $userId = $task->user_id;
                $userName = $task->user->name;

                $userPerformance[$userId] = [
                    'userName' => $userName,
                    'timeSpent' => $task->time_spent,
                ];
            }

            $userNames = [];
            $timeSpent = [];

            foreach ($userPerformance as $performance) {
                $userNames[] = $performance['userName'];
                $timeSpent[] = $performance['timeSpent'];
            }

            return [
                'labels' => $userNames,
                'values' => array_map(function ($name, $time) {
                    return [
                        'userName' => $name,
                        'timeSpent' => $time,
                    ];
                }, $userNames, $timeSpent),
            ];
        }
    }
}
