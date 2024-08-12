<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // Display a listing of tasks
    public function index()
    {
        $tasks = Task::all();

        return view('tasks.index', compact('tasks'));
    }

    // Show the form for creating a new task
    public function create()
    {
        $users = User::all();
        $projects = Project::all();
        $expenses = Expense::all();

        return view('tasks.create', compact('users', 'projects', 'expenses'));
    }


    // Store a newly created task in storage
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'expense_id' => 'nullable|exists:expenses,id',
        ]);

        // Create a new task using the validated data
        $task = Task::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'user_id' => $request->input('user_id'),
            'project_id' => $request->input('project_id'),
            'expense_id' => $request->input('expense_id'),
        ]);

        // Optionally, you can return a response or redirect
        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    // Show the form for editing the specified task
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $users = User::all();
        $projects = Project::all();
        $expenses = Expense::all();

        return view('tasks.edit', compact('task', 'users', 'projects', 'expenses'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'expense_id' => 'nullable|exists:expenses,id', // Expense is optional
        ]);

        $task = Task::findOrFail($id);
        $task->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'user_id' => $request->input('user_id'),
            'project_id' => $request->input('project_id'),
            'expense_id' => $request->input('expense_id'),
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    // Remove the specified task from storage
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
