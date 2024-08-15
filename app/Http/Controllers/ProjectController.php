<?php

namespace App\Http\Controllers;

use App\Decorators\ProjectLogDecorator;
use App\Models\Budget;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('budget')->get();

        return view('projects.index', compact('projects'));
    }

    public function store(Request $request)
    {
        // Validate request
        $validator = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget_id' => 'required|exists:budgets,id',
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
            'roles' => 'required|array',
            'roles.*' => 'in:Junior,Senior,Project Manager',
        ]);

        if (count($request->input('members')) !== count(array_unique($request->input('members')))) {
            return redirect()->back()->withErrors(['members' => 'Duplicate members are not allowed.'])->withInput();
        }

        $project = Project::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'budget_id' => $request->input('budget_id'),
        ]);

        $membersWithRoles = array_combine($validator['members'], $validator['roles']);
        foreach ($membersWithRoles as $memberId => $role) {
            $project->users()->attach($memberId, ['role' => $role]);
        }

        $projectLogger = new ProjectLogDecorator($project);
        $projectLogger->logAction('Created', [
            'name' => $project->name,
            'description' => $project->description,
            'budget_id' => $project->budget_id
        ]);

        return redirect()->route('projects.index')->with('success', 'Project created successfully with a budget.');
    }

    public function create(){
        $budgets = Budget::all();
        $users = User::all();
        return view('projects.create',compact('budgets','users'));
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);
        $budgets = Budget::all();
        $users = User::all();
        $assignedUsers = $project->users->pluck('id')->toArray();
        return view('projects.edit', compact('project', 'budgets','users', 'assignedUsers'));
    }

    public function update(Request $request, $id)
    {
        $validator = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'budget_id' => 'required|exists:budgets,id',
            'members' => 'required|array',
            'members.*' => 'exists:users,id',
            'roles' => 'required|array',
            'roles.*' => 'in:Junior,Senior,Project Manager',
        ]);

        if (count($request->input('members')) !== count(array_unique($request->input('members')))) {
            return redirect()->back()->withErrors(['members' => 'Duplicate members are not allowed.'])->withInput();
        }

        $project = Project::findOrFail($id);

        $project->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'budget_id' => $request->input('budget_id'),
        ]);

        $project->users()->detach();

        $membersWithRoles = array_combine($validator['members'], $validator['roles']);
        foreach ($membersWithRoles as $memberId => $role) {
            $project->users()->attach($memberId, ['role' => $role]);
        }

        $projectLogger = new ProjectLogDecorator($project);
        $projectLogger->logAction('Updated', [
            'name' => $project->name,
            'description' => $project->description,
            'budget_id' => $project->budget_id
        ]);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully with a budget.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        $projectLogger = new ProjectLogDecorator($project);
        $projectLogger->logAction('Deleted', [
            'name' => $project->name,
            'description' => $project->description,
            'budget_id' => $project->budget_id
        ]);

        return redirect()->route('projects.index')
                         ->with('success', 'Project deleted successfully.');
    }
}
