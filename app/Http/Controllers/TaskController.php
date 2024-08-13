<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Mail\TaskAssigned;
use App\Models\Expense;
use App\Models\Project;
use App\Models\Task;
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
        $result = $request->validated();

        $task = Task::create([
            'name' => $result['name'],
            'description' => $result['description'],
            'user_id' => $result['user_id'],
            'project_id' => $result['project_id'],
            'expense_id' => $result['expense_id'],
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
        $result = $request->validated();

        $task = Task::findOrFail($id);
        $originalUserId = $task->user_id;
        $task->update([
            'name' => $result['name'],
            'description' => $result['description'],
            'status' => $result['status'],
            'user_id' => $result['user_id'],
            'project_id' => $result['project_id'],
            'expense_id' => $result['expense_id'],
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
