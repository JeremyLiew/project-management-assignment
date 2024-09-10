<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log; 
use App\Models\Project;
use App\Models\Task;
use App\Models\Expense;
use App\Models\User;
use App\Models\Budget;
use DOMDocument;
use DOMXPath;
use XSLTProcessor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    
    public function __construct() {
        $this->middleware('auth');
    }

    public function adminHome() {
        return view('adminHome');
    }

    public function index(Request $request)
    {
        $user = auth()->user(); // Get the currently authenticated user
    
        // Fetch tasks for the specific user
        $tasks = Task::where('user_id', $user->id)->get();
    
        // Fetch distinct projects from the user's tasks
        $projectIds = $tasks->pluck('project_id')->unique();
        $projects = Project::whereIn('id', $projectIds)->get();

        // Generate XML from projects and tasks
        $xml = new DOMDocument('1.0', 'UTF-8');
        $projectsElement = $xml->createElement('projects');

        foreach ($projects as $project) {
            // Fetch the budget associated with the project
            $budget = Budget::find($project->budget_id); // Using budget_id to find the related budget

            $totalCost = Task::where('project_id', $project->id)
                ->join('expenses', 'tasks.expense_id', '=', 'expenses.id')
                ->sum('expenses.amount'); // Assuming there's an expenses table with an amount field

            // Check if the project is completed or expired
            if ($project->completed_at) {
                $completionTime = $project->created_at->diffInDays($project->completed_at);
            } else {
                $completionTime = $project->due_date && $project->due_date->isPast() ? 'Expired' : 'In Progress';
            }

            $projectElement = $xml->createElement('project');

            $projectElement->appendChild($xml->createElement('name', htmlspecialchars($project->name ?? 'N/A')));
            $projectElement->appendChild($xml->createElement('description', htmlspecialchars($project->description ?? 'N/A')));
            $projectElement->appendChild($xml->createElement('budgetAmount', $budget->total_amount ?? 'N/A'));
            $projectElement->appendChild($xml->createElement('totalCost', $totalCost ?? 'N/A'));
            $projectElement->appendChild($xml->createElement('Completion_project_time', $completionTime ?? 'N/A'));

            foreach ($project->tasks as $task) {
                $taskCost = $task->expense ? $task->expense->amount : 0; // Assuming task has an associated expense
                $completionTime = null;
                $status = '';
                
                // Format 'updated_at', 'due_date', and 'created_at' to 'YYYY-MM-DD'
                $updatedDate = $task->updated_at->format('Y-m-d');
                $dueDate = $task->due_date ? $task->due_date->format('Y-m-d') : null;
                $createdDate = $task->created_at->format('Y-m-d');
    
                if ($task->status === 'Completed') {
                    // Task is completed
                    $completionTime = $task->created_at->diffInDays($task->updated_at); // Time from creation to completion
    
                    // Check if it was completed before or after the due date
                    if ($dueDate && $updatedDate > $dueDate) {
                        $status = 'Expired'; // Completed after the due date
                    } else {
                        $status = 'Completed'; // Completed on or before the due date
                    }
                } else {
                    // Task is not completed
                    $currentDate = now()->format('Y-m-d'); // Current date in 'YYYY-MM-DD'
    
                    if ($dueDate && $currentDate > $dueDate) {
                        $status = 'Expired'; // Task is past the due date and not completed
                    } else {
                        $status = $task->status; // Task is still within the due date
                    }
                    
                    // Calculate the duration from creation to the last update
                    $completionTime = $task->created_at->diffInDays($task->updated_at); 
                }

                $taskElement = $projectElement->appendChild($xml->createElement('task'));

                $taskElement->appendChild($xml->createElement('name', htmlspecialchars($task->name ?? 'N/A')));
                $taskElement->appendChild($xml->createElement('cost', $taskCost ?? 'N/A'));
                $taskElement->appendChild($xml->createElement('created_at', $task->created_at ? $task->created_at->format('Y-m-d') : 'N/A'));
                $taskElement->appendChild($xml->createElement('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : 'N/A'));
                $taskElement->appendChild($xml->createElement('Completion_task_time', $completionTime ?? 'N/A'));
                $taskElement->appendChild($xml->createElement('status', htmlspecialchars($status ?? 'N/A')));
            }

            $projectsElement->appendChild($projectElement);
        }

        $xml->appendChild($projectsElement);

        // Load XSLT stylesheet
        $xsl = new DOMDocument();
        $xsl->load(public_path('xslt/dashboard.xsl'));

        // Apply XSLT transformation
        $processor = new XSLTProcessor();
        $processor->importStylesheet($xsl);

        $XMLOutput = $processor->transformToXml($xml);

        // Return view with transformed HTML
        return view('dashboard.index', ['XMLOutput' => $XMLOutput]);
    }
}
