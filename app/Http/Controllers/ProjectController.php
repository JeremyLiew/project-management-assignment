<?php

namespace App\Http\Controllers;

use App\Decorators\ProjectLogDecorator;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Budget;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projectLogger = new ProjectLogDecorator(null);
        try {
            $projects = Project::with('budget')->get();
            $projectLogger->logAction('Fetched Projects Data', ['status' => '200']);
            return view('projects.index', compact('projects'));
        } catch (\Exception $e) {
            $projectLogger->logAction('Failed to Fetch Projects', ['error' => $e->getMessage()]);
            return redirect()->route('projects.index')->with('error', 'Failed to fetch projects.');
        }
    }

    public function store(StoreProjectRequest $request)
    {
        try {
            $validator = $request->validated();

            if (count($request->input('members')) !== count(array_unique($request->input('members')))) {
                $projectLogger = new ProjectLogDecorator(null);
                $projectLogger->logAction('Failed to Create Project', ['error' => 'Attempts to create project with duplicate members.']);
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
                'budget_id' => $project->budget_id,
                'members' => $membersWithRoles,
            ]);

            return redirect()->route('projects.index')->with('success', 'Project created successfully with a budget.');
        } catch (\Exception $e) {
            $projectLogger = new ProjectLogDecorator(null);
            $projectLogger->logAction('Failed to Create Project', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create project.');
        }
    }

    public function create(){
        $budgets = Budget::all();
        $users = User::all();
        return view('projects.create',compact('budgets','users'));
    }

    public function edit($id)
    {
        try {
            $project = Project::findOrFail($id);
            $budgets = Budget::all();
            $users = User::all();
            $assignedUsers = $project->users->pluck('id')->toArray();
            return view('projects.edit', compact('project', 'budgets', 'users', 'assignedUsers'));
        } catch (\Exception $e) {
            $projectLogger = new ProjectLogDecorator(null);
            $projectLogger->logAction('Failed to Fetch Project for Editing', ['error' => $e->getMessage()]);
            return redirect()->route('projects.index')->with('error', 'Failed to fetch project for editing.');
        }
    }

    public function update(UpdateProjectRequest $request, $id)
    {
        try {
            $validator = $request->validated();

            if (count($request->input('members')) !== count(array_unique($request->input('members')))) {
                $projectLogger = new ProjectLogDecorator(null);
                $projectLogger->logAction('Failed to Update Project', ['error' => 'Attempts to create project with duplicate members.']);
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
                'budget_id' => $project->budget_id,
                'members' => $membersWithRoles,
            ]);

            return redirect()->route('projects.index')->with('success', 'Project updated successfully with a budget.');
        } catch (\Exception $e) {
            $projectLogger = new ProjectLogDecorator(null);
            $projectLogger->logAction('Failed to Update Project', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update project.');
        }
    }

    public function destroy(Project $project)
    {
        try {
            $membersWithRoles = $project->users->mapWithKeys(function ($user) {
                return [$user->id => $user->pivot->role];
            })->toArray();

            $project->delete();

            $projectLogger = new ProjectLogDecorator($project);
            $projectLogger->logAction('Deleted', [
                'name' => $project->name,
                'description' => $project->description,
                'budget_id' => $project->budget_id,
                'members' => $membersWithRoles,
            ]);

            return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
        } catch (\Exception $e) {
            $projectLogger = new ProjectLogDecorator(null);
            $projectLogger->logAction('Failed to Delete Project', ['error' => $e->getMessage()]);
            return redirect()->route('projects.index')->with('error', 'Failed to delete project.');
        }
    }
}
