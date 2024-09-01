<?php
// Jeremy
namespace App\Observers;

use App\Mail\TaskAssigned;
use App\Models\Task;
use Illuminate\Support\Facades\Mail;
use App\Services\GanttChartService;
use App\Services\ProgressTrackingService;
use App\Services\ResourceAllocationService;

class TaskObserver {

    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void {
        $this->sendTaskAssignmentEmail($task);

        ProgressTrackingService::initialize($task);
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void {
        if ($task->isDirty('user_id')) {
            $this->sendTaskAssignmentEmail($task);
        }

        GanttChartService::update($task);
        ProgressTrackingService::update($task);
        ResourceAllocationService::update($task);
    }

    protected function sendTaskAssignmentEmail(Task $task) {
        $assignee = $task->user;
        $data = [
            'assignee' => $assignee->name,
            'task' => $task->name,
        ];
        Mail::to($assignee->email)->send(new TaskAssigned($data));
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void {
        //
    }
}
