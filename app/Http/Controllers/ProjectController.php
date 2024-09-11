<?php

namespace App\Http\Controllers;

use App\Decorators\ProjectLogDecorator;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Budget;
use App\Models\Project;
use App\Models\User;
use DOMDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use XSLTProcessor;

class ProjectController extends Controller {

    // Fetch all projects and return as XML
    public function index(Request $request) {
        $projectLogger = new ProjectLogDecorator(null, $request);
        try {
            $projects = Project::with('users')->get();
            $xml = new \SimpleXMLElement('<projects/>');

            foreach ($projects as $project) {
                $proj = $xml->addChild('project');
                $proj->addChild('id', $project->id);
                $proj->addChild('name', $project->name);
                $proj->addChild('description', $project->description);
                $proj->addChild('budget', $project->budget);
                $proj->addChild('status', $project->status);

                $users = $proj->addChild('users');
                foreach ($project->users as $user) {
                    $userXml = $users->addChild('user');
                    $userXml->addChild('name', $user->name);
                    $userXml->addChild('role', $user->pivot->role);
                }
            }

            // Convert SimpleXMLElement to DOMDocument for XPath queries
            $xmlString = $xml->asXML();
            $xmlDom = new \DOMDocument();
            $xmlDom->loadXML($xmlString);

            $xpath = new \DOMXPath($xmlDom);

            $projects = $xpath->query('/projects');

            foreach ($projects as $project) {
                echo $project->nodeValue;
            }

            // Load the XSLT file for transforming the XML
            $xsltProcessor = new XSLTProcessor();
            $xslt = new DOMDocument();
            $xslt->load(public_path('xslt/project_transform.xsl'));
            $xsltProcessor->importStylesheet($xslt);
            $xmlDom->loadXML($xml->asXML());

            $transformedXml = $xsltProcessor->transformToXML($xmlDom);

            $projectLogger->logAction('Fetched Projects Data', ['status' => '200']);
            return response($transformedXml, 200)->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            $projectLogger->logAction('Failed to Fetch Projects', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch projects'], 500);
        }
    }

    // Create a new project (All users can create projects)
    public function store(StoreProjectRequest $request) {
        try {
            $validator = $request->validated();

            if (count($request->input('members')) !== count(array_unique($request->input('members')))) {
                $projectLogger = new ProjectLogDecorator(null, $request);
                $projectLogger->logAction('Failed to Create Project', ['error' => 'Attempts to create project with duplicate members.']);
                return redirect()->back()->withErrors(['members' => 'Duplicate members are not allowed.'])->withInput();
            }

            $project = Project::create([
                        'name' => $request->input('name'),
                        'description' => $request->input('description'),
                        'budget_id' => $request->input('budget_id'),
                        'status' => 'in-progress', // Default status
            ]);

            $membersWithRoles = array_combine($validator['members'], $validator['roles']);
            foreach ($membersWithRoles as $memberId => $role) {
                $project->users()->attach($memberId, ['role' => $role]);
            }

            $projectLogger = new ProjectLogDecorator($project, $request);
            $projectLogger->logAction('Created', [
                'name' => $project->name,
                'description' => $project->description,
                'budget_id' => $project->budget_id,
                'status' => $project->status,
                'members' => $membersWithRoles,
            ]);

            return redirect()->route('projects.index')->with('success', 'Project created successfully with a budget.');
        } catch (\Exception $e) {
            $projectLogger = new ProjectLogDecorator(null, $request);
            $projectLogger->logAction('Failed to Create Project', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create project.');
        }
    }

    // Assign a project to a user (Admin and Manager only)
    public function assign($id, Request $request) {
        $projectLogger = new ProjectLogDecorator(null, $request);
        $project = Project::find($id);

        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'manager') {
            $projectLogger->logAction('Unauthorized Assignment', ['error' => 'Unauthorized']);
            return redirect()->back()->withErrors(['error' => 'Unauthorized members are not allowed.']);
        }

        $user = User::find($request->input('user_id'));
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Assign as a normal user by default
        $project->users()->attach($user->id, ['role' => 'normal']);

        $projectLogger->logAction('Assigned User', [
            'user_id' => $user->id,
            'project_id' => $project->id
        ]);

        return response()->json(['success' => 'Project assigned successfully'], 200);
    }

    // Method to display only "in-progress" projects for admins and managers
    public function getInProgressProjects(Request $request) {
        try {
            $projects = Project::with('users')
                    ->where('status', 'in-progress')
                    ->get();

            $xml = new \SimpleXMLElement('<projects/>');
            foreach ($projects as $project) {
                $proj = $xml->addChild('project');
                $proj->addChild('id', $project->id);
                $proj->addChild('name', $project->name);
                $proj->addChild('description', $project->description);
                $proj->addChild('budget', $project->budget);
                $proj->addChild('status', $project->status);

                $users = $proj->addChild('users');
                foreach ($project->users as $user) {
                    $userXml = $users->addChild('user');
                    $userXml->addChild('name', $user->name);
                    $userXml->addChild('role', $user->pivot->role);
                }
            }

            return response($xml->asXML(), 200)->header('Content-Type', 'application/xml');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch in-progress projects'], 500);
        }
    }

    // Mark a project as completed (Manager only)
    public function complete($id) {
        $projectLogger = new ProjectLogDecorator(null, null);
        $project = Project::find($id);

        if (Auth::user()->role !== 'manager') {
            $projectLogger->logAction('Unauthorized Status Change', ['error' => 'Unauthorized']);
            return redirect()->back()->withErrors(['error' => 'Unauthorized members are not allowed.']);
        }

        $project->status = 'completed';
        $project->save();

        $projectLogger->logAction('Marked as Completed', [
            'project_id' => $project->id,
            'status' => 'completed'
        ]);

        return response()->json(['success' => 'Project marked as completed'], 200);
    }

    // Show a single project's details in XML format
    public function show($id) {
        $project = Project::with('users')->find($id);

        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        $xml = new \SimpleXMLElement('<project/>');
        $xml->addChild('id', $project->id);
        $xml->addChild('name', $project->name);
        $xml->addChild('description', $project->description);
        $xml->addChild('budget', $project->budget);
        $xml->addChild('status', $project->status);

        // Add users associated with the project
        $users = $xml->addChild('users');
        foreach ($project->users as $user) {
            $userXml = $users->addChild('user');
            $userXml->addChild('name', $user->name);
            $userXml->addChild('role', $user->pivot->role);
        }

        return response($xml->asXML(), 200)->header('Content-Type', 'application/xml');
    }

    public function create() {
        $budgets = Budget::all();
        $users = User::all();
        return view('projects.create', compact('budgets', 'users'));
    }

    public function edit($id, Request $request) {
        try {
            $project = Project::findOrFail($id);
            $budgets = Budget::all();
            $users = User::all();
            $assignedUsers = $project->users->pluck('id')->toArray();
            return view('projects.edit', compact('project', 'budgets', 'users', 'assignedUsers'));
        } catch (\Exception $e) {
            $projectLogger = new ProjectLogDecorator(null, $request);
            $projectLogger->logAction('Failed to Fetch Project for Editing', ['error' => $e->getMessage()]);
            return redirect()->route('projects.index')->with('error', 'Failed to fetch project for editing.');
        }
    }

    public function update(UpdateProjectRequest $request, $id) {
        try {
            $validator = $request->validated();

            if (count($request->input('members')) !== count(array_unique($request->input('members')))) {
                $projectLogger = new ProjectLogDecorator(null, $request);
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

            $projectLogger = new ProjectLogDecorator($project, $request);
            $projectLogger->logAction('Updated', [
                'name' => $project->name,
                'description' => $project->description,
                'budget_id' => $project->budget_id,
                'members' => $membersWithRoles,
            ]);

            return redirect()->route('projects.index')->with('success', 'Project updated successfully with a budget.');
        } catch (\Exception $e) {
            $projectLogger = new ProjectLogDecorator(null, $request);
            $projectLogger->logAction('Failed to Update Project', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update project.');
        }
    }

    public function destroy(Project $project, Request $request) {
        try {
            $membersWithRoles = $project->users->mapWithKeys(function ($user) {
                        return [$user->id => $user->pivot->role];
                    })->toArray();

            $project->delete();

            $projectLogger = new ProjectLogDecorator($project, $request);
            $projectLogger->logAction('Deleted', [
                'name' => $project->name,
                'description' => $project->description,
                'budget_id' => $project->budget_id,
                'members' => $membersWithRoles,
            ]);

            return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
        } catch (\Exception $e) {
            $projectLogger = new ProjectLogDecorator(null, $request);
            $projectLogger->logAction('Failed to Delete Project', ['error' => $e->getMessage()]);
            return redirect()->route('projects.index')->with('error', 'Failed to delete project.');
        }
    }
}
