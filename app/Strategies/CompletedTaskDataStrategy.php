<?php
/**
 *
 * @author Liew Wei Lun
 */
// app/Strategies/CompletedTaskDataStrategy.php
namespace App\Strategies;

use App\Models\Task;

class CompletedTaskDataStrategy implements StrategyInterface
{
    public function execute($data)
    {
        $tasks = $data['tasks'];
        $completedTaskData = [];

        foreach ($tasks as $task) {

                $hoursSpent = $task->created_at->diffInHours($task->updated_at);

                $completedTaskData[] = [
                    'taskName' => $task->name,
                    'hoursSpent' => $hoursSpent,
                ];

        }

        return $completedTaskData;
    }
}
