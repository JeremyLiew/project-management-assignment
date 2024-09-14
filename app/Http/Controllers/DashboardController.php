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

    public function index()
    {
        $user = auth()->user();
        $tasks = Task::where('user_id', $user->id)->get();
        $projectIds = $tasks->pluck('project_id')->unique();
        $projects = Project::whereIn('id', $projectIds)->get();
    
        $xml = new DOMDocument('1.0', 'UTF-8');
        $projectsElement = $xml->createElement('projects');
    
        foreach ($projects as $project) {
            $budget = Budget::find($project->budget_id);
            $totalCost = Task::where('project_id', $project->id)
                ->whereNotNull('expense_id') // Only join when expense_id is present
                ->join('expenses', 'tasks.expense_id', '=', 'expenses.id')
                ->sum('expenses.amount') ?? 0;
    
            $completionTime = $project->completed_at 
                ? $project->created_at->diffInDays($project->completed_at) 
                : ($project->due_date && $project->due_date->isPast() ? 'Expired' : 'In Progress');
    
            $projectElement = $xml->createElement('project');
            $projectElement->appendChild($xml->createElement('name', htmlspecialchars($project->name ?? 'N/A')));
            $projectElement->appendChild($xml->createElement('description', htmlspecialchars($project->description ?? 'N/A')));
            $projectElement->appendChild($xml->createElement('budgetAmount', $budget->total_amount ?? 'N/A'));
            $projectElement->appendChild($xml->createElement('totalCost', $totalCost));
            $projectElement->appendChild($xml->createElement('Completion_project_time', $completionTime));
    
            foreach ($project->tasks as $task) {
                $taskCost = $task->expense ? $task->expense->amount : 0;
                $completionTime = null;
                $status = '';
    
                $updatedDate = $task->updated_at->format('Y-m-d');
                $dueDate = $task->due_date ? $task->due_date->format('Y-m-d') : null;
                $createdDate = $task->created_at->format('Y-m-d');
    
                if ($task->status === 'Completed') {
                    $completionTime = $task->created_at->diffInDays($task->updated_at);
                    $status = ($dueDate && $updatedDate > $dueDate) ? 'Expired' : 'Completed';
                } else {
                    $currentDate = now()->format('Y-m-d');
                    $status = ($dueDate && $currentDate > $dueDate) ? 'Expired' : $task->status;
                    $completionTime = $task->created_at->diffInDays($task->updated_at);
                }
    
                $taskElement = $projectElement->appendChild($xml->createElement('task'));
                $taskElement->appendChild($xml->createElement('name', htmlspecialchars($task->name ?? 'N/A')));
                $taskElement->appendChild($xml->createElement('cost', $taskCost));
                $taskElement->appendChild($xml->createElement('created_at', $task->created_at ? $task->created_at->format('Y-m-d') : 'N/A'));
                $taskElement->appendChild($xml->createElement('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : 'N/A'));
                $taskElement->appendChild($xml->createElement('Completion_task_time', $completionTime));
                $taskElement->appendChild($xml->createElement('status', htmlspecialchars($status)));
            }
    
            $projectsElement->appendChild($projectElement);
        }
    
        $xml->appendChild($projectsElement);
        $xsl = new DOMDocument();
        $xsl->load(public_path('xslt/dashboard.xsl')); // Ensure the path is correct
        $processor = new XSLTProcessor();
        $processor->importStylesheet($xsl);
        $XMLOutput = $processor->transformToXml($xml);
        
        return view('dashboard.index', [
            'XMLOutput' => $XMLOutput
        ]);
    } 

    public function individual_report()
    {
        $user = auth()->user();
        $tasks = Task::where('user_id', $user->id)->get();
        $projectIds = $tasks->pluck('project_id')->unique();
        $projects = Project::whereIn('id', $projectIds)->get();
    
        $completedProjects = $projects->filter(function ($project) {
            return $project->completed_at !== null;
        })->count();
    
        
        $inProgressProjects = $projects->count() - $completedProjects;
        $pendingTasks = $tasks->where('status', 'Pending')->count();
        $inProgressTasks = $tasks->where('status', 'In Progress')->count();
        $completedTaskCount = $tasks->where('status', 'Completed')->count();
    
        $taskStats = [
            'pendingTasks' => $pendingTasks,
            'inProgressTasks' => $inProgressTasks,
            'completedTaskCount' => $completedTaskCount,
        ];
    
        $budgetUtilization = [];
        foreach ($projects as $project) {
            $budget = Budget::find($project->budget_id);
            if ($budget) {
                $totalBudget = $budget->total_amount;
                $totalExpenses = Task::where('project_id', $project->id)
                    ->join('expenses', 'tasks.expense_id', '=', 'expenses.id')
                    ->sum('expenses.amount');
                $utilizationPercentage = $totalBudget > 0 ? ($totalExpenses / $totalBudget) * 100 : 0;
    
                $budgetUtilization[] = [
                    'projectName' => $project->name,
                    'utilization' => $utilizationPercentage,
                ];
            }
        }

    
        $completedTaskData = [];
        foreach ($tasks as $task) {
            if ($task->status === 'Completed') {
                $hoursSpent = $task->created_at->diffInHours($task->updated_at);
    
                $completedTaskData[] = [
                    'taskName' => $task->name,
                    'hoursSpent' => $hoursSpent,
                ];
            }
        }

    
        return view('dashboard.individual-report', compact(
            'completedProjects',
            'inProgressProjects',
            'taskStats',
            'budgetUtilization',
            'completedTaskData',
            'projects'
        ));
    }    

    
    public function team_report()
    {
        

        return view('dashboard.team-report');
    }
    
}
