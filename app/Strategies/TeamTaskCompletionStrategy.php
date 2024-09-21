<?php
// app/Strategies/TeamTaskCompletionStrategy.php
/**
 *
 * @author Liew Wei Lun
 */
namespace App\Strategies;

use App\Models\Task;
use App\Models\User;

class TeamTaskCompletionStrategy implements MultiParameterStrategyInterface
{
    public function execute($projectId, $userId)
    {
        $taskCompletionData = [];
        if($userId != 1 || $userId != 2){
            $tasks = Task::where('project_id', $projectId)->with('user')->get();
        
            
            foreach ($tasks as $task) {
                $taskUserId = $task->user_id;
                if (!$taskUserId) continue;
    
                if (!isset($taskCompletionData[$taskUserId])) {
                    $taskCompletionData[$taskUserId] = [
                        'completed' => 0,
                        'pending' => 0,
                        'inProgress' => 0,
                    ];
                }
    
                if ($task->status === 'Completed') {
                    $taskCompletionData[$taskUserId]['completed'] += 1;
                } elseif ($task->status === 'Pending') {
                    $taskCompletionData[$taskUserId]['pending'] += 1;
                } elseif ($task->status === 'In Progress') {
                    $taskCompletionData[$taskUserId]['inProgress'] += 1;
                }
            }
    
            $completionValues = [
                'Completed' => [],
                'Pending' => [],
                'In Progress' => []
            ];
    
            foreach ($taskCompletionData as $data) {
                $completionValues['Completed'][] = $data['completed'];
                $completionValues['Pending'][] = $data['pending'];
                $completionValues['In Progress'][] = $data['inProgress'];
            }
    
            return $completionValues;
        }
        else{
            $tasks = Task::where('project_id', $projectId)->with('user')->get();

            foreach ($tasks as $task) {
                $taskUserId = $task->user_id;
                if (!$taskUserId) continue;
    
                if (!isset($taskCompletionData[$taskUserId])) {
                    $taskCompletionData[$taskUserId] = [
                        'completed' => 0,
                        'pending' => 0,
                        'inProgress' => 0,
                    ];
                }
    
                if ($task->status === 'Completed') {
                    $taskCompletionData[$taskUserId]['completed'] += 1;
                } elseif ($task->status === 'Pending') {
                    $taskCompletionData[$taskUserId]['pending'] += 1;
                } elseif ($task->status === 'In Progress') {
                    $taskCompletionData[$taskUserId]['inProgress'] += 1;
                }
            }
    
            $completionValues = [
                'Completed' => [],
                'Pending' => [],
                'In Progress' => []
            ];
    
            foreach ($taskCompletionData as $data) {
                $completionValues['Completed'][] = $data['completed'];
                $completionValues['Pending'][] = $data['pending'];
                $completionValues['In Progress'][] = $data['inProgress'];
            }
    
            return $completionValues;
        }
        

    }
}
