<?php

namespace App\Http\Controllers;

use App\Decorators\TaskLogDecorator;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Mail\TaskAssigned;
use App\Models\Expense;
use App\Models\LoggingDecorator;
use App\Models\LoggingTaskDecorator;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{

    public function index()
    {
        $tasks = Task::all();

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $users = User::all();
        $projects = Project::all();
        $expenses = Expense::all();

        return view('tasks.create', compact('users', 'projects', 'expenses'));
    }

    public function store(StoreTaskRequest $request)
    {

        $task = Task::create($request->validated());

        $taskLogger = new TaskLogDecorator($task);
        $taskLogger->logAction('Created', [
            'name' => $task->name,
            'description' => $task->description,
            'project_id' => $task->project_id,
            'user_id' => $task->user_id
        ]);
        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $users = User::all();
        $projects = Project::all();
        $expenses = Expense::all();

        return view('tasks.edit', compact('task', 'users', 'projects', 'expenses'));
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        $task = Task::findOrFail($id);
        $task->update($request->validated());

        $taskLogger = new TaskLogDecorator($task);
        $taskLogger->logAction('Updated', [
            'name' => $task->name,
            'description' => $task->description,
            'project_id' => $task->project_id,
            'user_id' => $task->user_id
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {

        $task->delete();

        $taskLogger = new TaskLogDecorator($task);
        $taskLogger->logAction('Deleted', [
            'name' => $task->name,
            'description' => $task->description,
            'project_id' => $task->project_id,
            'user_id' => $task->user_id
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
