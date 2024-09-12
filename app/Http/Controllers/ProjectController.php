<?php

//Soo Yu Hung

namespace App\Http\Controllers;

use App\Decorators\ProjectLogDecorator;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Budget;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use DOMDocument;
use XSLTProcessor;
use DOMXPath;

class ProjectController extends Controller {

    public function index(Request $request) {
        $projectLogger = new ProjectLogDecorator(null, $request);
        try {
            $projects = Project::with('budget')->get();
            $projectLogger->logAction('Fetched Projects Data', ['status' => '200']);

            $HTML = null;
            $HTMLs = null;
            $html = null;
            if (auth()->user()->role === 'admin' || auth()->user()->role === 'manager') {
                $HTML = $this->getInProgressProjectsHTML();
                $HTMLs = $this->getCompletedProjectsHTML();
                $html = Project::where('status', 'completed')->get();
            }

            return view('projects.index', compact('projects', 'HTML', 'HTMLs', 'html'));
        } catch (\Exception $e) {
            $projectLogger->logAction('Failed to Fetch Projects', ['error' => $e->getMessage()]);
            return redirect()->route('projects.index')->with('error', 'Failed to fetch projects.');
        }
    }

    public function getInProgressProjectsHTML() {
        $xml = public_path('xml/projects.xml');
        $xsl = public_path('xslt/projects.xsl');

        $xmlDoc = new DOMDocument();
        $xmlDoc->load($xml);

        $xslDoc = new DOMDocument();
        $xslDoc->load($xsl);

        $proc = new XSLTProcessor();
        $proc->importStylesheet($xslDoc);

        return $proc->transformToXML($xmlDoc);
    }

    public function getCompletedProjectsHTML() {
        $xml = public_path('xml/projects.xml');
        $xsl = public_path('xslt/projects.xsl');

        $xmlDoc = new DOMDocument();
        $xmlDoc->load($xml);

        $xpath = new DOMXPath($xmlDoc);

        // Find all projects with status 'completed'
        $completedProjects = $xpath->query("//project[status='completed']");

        // Create a new DOMDocument to hold the filtered projects
        $filteredXmlDoc = new DOMDocument();
        $root = $filteredXmlDoc->createElement("projects");
        $filteredXmlDoc->appendChild($root);

        // Append each completed project to the new document
        foreach ($completedProjects as $project) {
            $importedProject = $filteredXmlDoc->importNode($project, true);
            $root->appendChild($importedProject);
        }

        $xslDoc = new DOMDocument();
        $xslDoc->load($xsl);

        $proc = new XSLTProcessor();
        $proc->importStylesheet($xslDoc);

        return $proc->transformToXML($filteredXmlDoc);
    }

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
            ]);

            $membersWithRoles = array_combine($request->members, $request->roles);
            foreach ($membersWithRoles as $memberId => $role) {
                $project->users()->attach($memberId, ['role' => $role]);
            }

            $projectLogger = new ProjectLogDecorator($project, $request);
            $projectLogger->logAction('Created', [
                'name' => $project->name,
                'description' => $project->description,
                'budget_id' => $project->budget_id,
                'members' => $membersWithRoles,
            ]);

            return redirect()->route('projects.index')->with('success', 'Project created successfully with a budget.');
        } catch (\Exception $e) {
            $projectLogger = new ProjectLogDecorator(null, $request);
            $projectLogger->logAction('Failed to Create Project', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create project.');
        }
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

            $status = $request->has('status') ? 'Completed' : $project->status;

            $project->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'budget_id' => $request->input('budget_id'),
                'status' => $status,
            ]);

            $project->users()->detach();

            $project->users()->sync([]);

            $membersWithRoles = array_combine($request->members, $request->roles);
            foreach ($membersWithRoles as $memberId => $role) {
                $project->users()->attach($memberId, ['role' => $role]);
            }

            $projectLogger = new ProjectLogDecorator($project, $request);
            $projectLogger->logAction('Updated', [
                'name' => $project->name,
                'description' => $project->description,
                'budget_id' => $project->budget_id,
                'status' => $project->status,
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
