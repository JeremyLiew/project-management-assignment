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
            // Get the logged-in user
            $user = auth()->user();

            if ($user->role === 'admin' || $user->role === 'manager') {
                // Admins and managers can see all projects
                $projects = Project::with('budget', 'creator')->get();

                // Get additional project data for admins and managers
                $xslt = $this->xslt();
                $complete = $this->getCompletedProjects();
                $inprogress = $this->getInProgressProjects();
            } else {
                // Regular users can see their own projects and assigned projects
                $userProjects = $this->getUserProjects($user);
                $assignedProjects = $this->getAssignProjects($user);

                // Merge and remove duplicates (if any)
                $projects = $userProjects->merge($assignedProjects)->unique('id');

                // Admin-specific data is not needed for regular users
                $xslt = $complete = $inprogress = '';
            }

            // Log successful action
            $projectLogger->logAction('Fetched Projects Data', ['status' => '200']);

            // Pass data to the view
            return view('projects.index', compact('projects', 'xslt', 'complete', 'inprogress'));
        } catch (\Exception $e) {
            // Log error and redirect with error message
            $projectLogger->logAction('Failed to Fetch Projects', ['error' => $e->getMessage()]);
            return redirect()->route('projects.index')->with('error', 'Failed to fetch projects.');
        }
    }

    private function getUserProjects($user) {
        return Project::where('creator_id', $user->id)
                        ->with('budget')
                        ->get();
    }

    private function getAssignProjects($user) {
        return Project::whereHas('users', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->with('budget')->get();
    }

    public function xslt() {
        // Load the original XML from the file
        $xml = public_path('xml/projects.xml');
        $xsl = public_path('xslt/projects.xsl');

        // Load the original XML data
        $xmlDoc = new DOMDocument();
        $xmlDoc->load($xml);

        // Create a new XML document for merging XML and database data
        $mergedXmlDoc = new DOMDocument();
        $mergedXmlDoc->formatOutput = true;

        // Create the root element 'projects'
        $root = $mergedXmlDoc->createElement("projects");
        $mergedXmlDoc->appendChild($root);

        // Append original XML data (ensure it's appended correctly)
        foreach ($xmlDoc->getElementsByTagName('project') as $project) {
            $importedXml = $mergedXmlDoc->importNode($project, true);
            $root->appendChild($importedXml);
        }

        // Fetch database data and add it as new XML nodes
        $dbProjects = Project::with('budget')->get();
        foreach ($dbProjects as $dbProject) {
            $projectElement = $mergedXmlDoc->createElement("project");

            // Add database fields to the XML node
            $idElement = $mergedXmlDoc->createElement("id", $dbProject->id);
            $nameElement = $mergedXmlDoc->createElement("name", $dbProject->name);
            $descriptionElement = $mergedXmlDoc->createElement("description", $dbProject->description);
            $budgetElement = $mergedXmlDoc->createElement("budget", $dbProject->budget->total_amount ?? 0);
            $statusElement = $mergedXmlDoc->createElement("status", $dbProject->status);

            $projectElement->appendChild($idElement);
            $projectElement->appendChild($nameElement);
            $projectElement->appendChild($descriptionElement);
            $projectElement->appendChild($budgetElement);
            $projectElement->appendChild($statusElement);

            // Append database project node to the root
            $root->appendChild($projectElement);
        }

        // Apply the XSLT transformation to the merged XML
        $xslDoc = new DOMDocument();
        $xslDoc->load($xsl);

        $proc = new XSLTProcessor();
        $proc->importStylesheet($xslDoc);

        // Return the transformed XML as HTML
        return $proc->transformToXML($mergedXmlDoc);
    }

    public function getCompletedProjects() {
        // Fetch completed projects from the database
        $completedDbProjects = Project::where('status', 'completed')->with('budget')->get();

        // Load XML and XSL files
        $xml = public_path('xml/projects.xml');
        $xsl = public_path('xslt/projects.xsl');

        $xmlDoc = new DOMDocument();
        $xmlDoc->load($xml);

        $xpath = new DOMXPath($xmlDoc);

        // Find completed projects in the XML
        $completedXmlProjects = $xpath->query("//project[status='completed']");

        // Create a new DOMDocument to hold the filtered projects
        $filteredXmlDoc = new DOMDocument();
        $root = $filteredXmlDoc->createElement("projects");
        $filteredXmlDoc->appendChild($root);

        // Append completed XML projects to the new document
        foreach ($completedXmlProjects as $project) {
            $importedProject = $filteredXmlDoc->importNode($project, true);
            $root->appendChild($importedProject);
        }

        // Convert database projects into XML format and append to the new document
        foreach ($completedDbProjects as $dbProject) {
            $projectElement = $filteredXmlDoc->createElement("project");

            // Adding the project data from the database to the XML format, including ID and formatted budget
            $idElement = $filteredXmlDoc->createElement("id", $dbProject->id);
            $nameElement = $filteredXmlDoc->createElement("name", $dbProject->name);
            $descriptionElement = $filteredXmlDoc->createElement("description", $dbProject->description);
            $statusElement = $filteredXmlDoc->createElement("status", $dbProject->status);
            $budgetElement = $filteredXmlDoc->createElement("budget", number_format($dbProject->budget->total_amount ?? 0, 2));

            $projectElement->appendChild($idElement);
            $projectElement->appendChild($nameElement);
            $projectElement->appendChild($descriptionElement);
            $projectElement->appendChild($statusElement);
            $projectElement->appendChild($budgetElement);

            // Append the newly created project element to the root node
            $root->appendChild($projectElement);
        }

        // Load and apply the XSL transformation
        $xslDoc = new DOMDocument();
        $xslDoc->load($xsl);

        $proc = new XSLTProcessor();
        $proc->importStylesheet($xslDoc);

        // Return the transformed XML as HTML
        return $proc->transformToXML($filteredXmlDoc);
    }

    public function getInProgressProjects() {
        // Fetch completed projects from the database
        $inProgressDbProjects = Project::where('status', 'in-progress')->with('budget')->get();

        // Load XML and XSL files
        $xml = public_path('xml/projects.xml');
        $xsl = public_path('xslt/projects.xsl');

        $xmlDoc = new DOMDocument();
        $xmlDoc->load($xml);

        $xpath = new DOMXPath($xmlDoc);

        // Find completed projects in the XML
        $inProgressXmlProjects = $xpath->query("//project[status='in-progress']");

        // Create a new DOMDocument to hold the filtered projects
        $filteredXmlDoc = new DOMDocument();
        $root = $filteredXmlDoc->createElement("projects");
        $filteredXmlDoc->appendChild($root);

        // Append completed XML projects to the new document
        foreach ($inProgressXmlProjects as $project) {
            $importedProject = $filteredXmlDoc->importNode($project, true);
            $root->appendChild($importedProject);
        }

        // Convert database projects into XML format and append to the new document
        foreach ($inProgressDbProjects as $dbProject) {
            $projectElement = $filteredXmlDoc->createElement("project");

            // Adding the project data from the database to the XML format, including ID and formatted budget
            $idElement = $filteredXmlDoc->createElement("id", $dbProject->id);
            $nameElement = $filteredXmlDoc->createElement("name", $dbProject->name);
            $descriptionElement = $filteredXmlDoc->createElement("description", $dbProject->description);
            $statusElement = $filteredXmlDoc->createElement("status", $dbProject->status);
            $budgetElement = $filteredXmlDoc->createElement("budget", number_format($dbProject->budget->total_amount ?? 0, 2));

            $projectElement->appendChild($idElement);
            $projectElement->appendChild($nameElement);
            $projectElement->appendChild($descriptionElement);
            $projectElement->appendChild($statusElement);
            $projectElement->appendChild($budgetElement);

            // Append the newly created project element to the root node
            $root->appendChild($projectElement);
        }

        // Load and apply the XSL transformation
        $xslDoc = new DOMDocument();
        $xslDoc->load($xsl);

        $proc = new XSLTProcessor();
        $proc->importStylesheet($xslDoc);

        // Return the transformed XML as HTML
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
                        'creator_id' => auth()->id(),
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
            $completedAt = $status === 'Completed' ? now() : $project->completed_at;

            $project->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'budget_id' => $request->input('budget_id'),
                'status' => $status,
                'completed_at' => $completedAt,
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
