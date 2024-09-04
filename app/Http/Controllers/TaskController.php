<?php
// Jeremy
namespace App\Http\Controllers;

use App\Decorators\TaskLogDecorator;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\TaskFilterRequest;
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

    public function index(TaskFilterRequest $request)
    {
        $taskLogger = new TaskLogDecorator(null, $request);
        $validatedData = $request->validated();

        try {
            $query = Task::query();

            if (!empty($validatedData['name'])) {
                $query->where('name', 'like', '%' . $validatedData['name'] . '%');
            }
            if (!empty($validatedData['status'])) {
                $query->where('status', $validatedData['status']);
            }
            if (!empty($validatedData['user_id'])) {
                $query->where('user_id', $validatedData['user_id']);
            }
            if (!empty($validatedData['project_id'])) {
                $query->where('project_id', $validatedData['project_id']);
            }
            if (!empty($validatedData['priority'])) {
                $query->where('priority', $validatedData['priority']);
            }

            if (!empty($validatedData['due_date'])) {
                $query->whereDate('due_date', $validatedData['due_date']);
            }

            $tasks = $query->latest()->get();

            $taskLogger->logAction('Fetched Tasks Data', ['status' => '200']);

            return view('tasks.index', [
                'tasks' => $tasks,
                'users' => User::all(),
                'projects' => Project::all(),
                'priorities' => ['low', 'medium', 'high'],
            ]);
        } catch (\Exception $e) {
            $taskLogger->logAction('Failed to Fetch Tasks', ['error' => $e->getMessage()]);

            return redirect()->route('tasks.index')->with('error', 'Failed to fetch tasks.');
        }
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
        try {
            $task = Task::create($request->validated());

            $taskLogger = new TaskLogDecorator($task, $request);
            $taskLogger->logAction('Created', [
                'name' => $task->name,
                'description' => $task->description,
                'project_id' => $task->project_id,
                'user_id' => $task->user_id,
                'status' => $task->status,
                'priority' => $task->priority,
                'expense_id' => $task->expense_id,
                'due_date' => $task->due_date->format('Y-m-d'),
            ]);

            return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
        } catch (\Exception $e) {
            $taskLogger = new TaskLogDecorator(null, $request);
            $taskLogger->logAction('Failed to Create Task', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create task.');
        }
    }

    public function edit($id, Request $request)
    {
        try {
            $task = Task::findOrFail($id);
            $users = User::all();
            $projects = Project::all();
            $expenses = Expense::all();
            return view('tasks.edit', compact('task', 'users', 'projects', 'expenses'));
        } catch (\Exception $e) {
            $taskLogger = new TaskLogDecorator(null, $request);
            $taskLogger->logAction('Failed to Fetch Task for Editing', ['error' => $e->getMessage()]);
            return redirect()->route('tasks.index')->with('error', 'Failed to fetch task for editing.');
        }
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->update($request->validated());

            $taskLogger = new TaskLogDecorator($task, $request);
            $taskLogger->logAction('Updated', [
                'name' => $task->name,
                'description' => $task->description,
                'project_id' => $task->project_id,
                'user_id' => $task->user_id,
                'status' => $task->status,
                'priority' => $task->priority,
                'expense_id' => $task->expense_id,
                'due_date' => $task->due_date->format('Y-m-d'),
            ]);

            return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
        } catch (\Exception $e) {
            $taskLogger = new TaskLogDecorator($task, $request);
            $taskLogger->logAction('Failed to Update Task', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update task.');
        }
    }

    public function destroy(Task $task , Request $request)
    {
        try {
            $task->delete();

            $taskLogger = new TaskLogDecorator($task, $request);
            $taskLogger->logAction('Deleted', [
                'name' => $task->name,
                'description' => $task->description,
                'project_id' => $task->project_id,
                'user_id' => $task->user_id,
                'status' => $task->status,
                'priority' => $task->priority,
                'expense_id' => $task->expense_id,
                'due_date' => $task->due_date->format('Y-m-d'),
            ]);

            return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
        } catch (\Exception $e) {
            $taskLogger = new TaskLogDecorator(null, $request);
            $taskLogger->logAction('Failed to Delete Task', ['error' => $e->getMessage()]);
            return redirect()->route('tasks.index')->with('error', 'Failed to delete task.');
        }
    }
}
