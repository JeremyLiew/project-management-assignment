<?php

namespace App\Console;

use App\Mail\TaskOverdue;
use App\Models\Task;
use App\Notifications\TaskOverdueNotification;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            try {
                $tasks = Task::where('due_date', '<', now())
                             ->where('status', '!=', 'Completed')
                             ->get();
                foreach ($tasks as $task) {
                    Mail::to($task->user->email)->send(new TaskOverdue($task));
                }
            } catch (\Exception $e) {
                Log::error('Error in scheduled task: ' . $e->getMessage());
            }
        })->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
