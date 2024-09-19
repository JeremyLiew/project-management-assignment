<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log; 
use App\Models\Project;
use App\Models\Task;
use App\Models\Expense;
use App\Models\User;
use App\Models\Budget;
use App\Services\DashboardService;
use App\Strategies\BudgetUtilizationStrategy;
use App\Strategies\CompletedTaskDataStrategy;
use App\Strategies\GenerateXMLStrategy;
use App\Strategies\TransformXMLStrategy;
use App\Strategies\TeamTaskCompletionStrategy;
use App\Strategies\TeamUserPerformanceStrategy;
use App\Strategies\TeamBudgetUtilizationStrategy;
use DOMDocument;
use DOMXPath;
use XSLTProcessor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;
    
    public function __construct(DashboardService $dashboardService)
    {
        $this->middleware('auth');
        $this->dashboardService = $dashboardService;
    }

    public function adminHome() {
        return view('adminHome');
    }

    public function index()
    {
        $user = auth()->user();
        $projects = $user->projects;

        $projectData = [];
        foreach ($projects as $project) {
            $budget = Budget::find($project->budget_id);
            $totalCost = Task::where('project_id', $project->id)
                ->whereNotNull('expense_id')
                ->join('expenses', 'tasks.expense_id', '=', 'expenses.id')
                ->sum('expenses.amount') ?? 0;
    
            $completionTime = $project->completed_at 
                ? $project->created_at->diffInDays($project->completed_at) 
                : ($project->due_date && $project->due_date->isPast() ? 'Expired' : 'In Progress');
    
            $tasksData = [];
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
    
                $tasksData[] = [
                    'name' => $task->name,
                    'cost' => $taskCost,
                    'created_at' => $task->created_at,
                    'due_date' => $task->due_date,
                    'completionTime' => $completionTime,
                    'status' => $status,
                ];
            }
    
            $projectData[] = [
                'name' => $project->name,
                'description' => $project->description,
                'budget' => $budget,
                'totalCost' => $totalCost,
                'completionTime' => $completionTime,
                'tasks' => $tasksData,
            ];
        }

        // Generate XML
        $this->dashboardService->setStrategy(new GenerateXMLStrategy());
        $xml = $this->dashboardService->executeStrategy(['projects' => $projectData]);

        // Transform XML
        $this->dashboardService->setStrategy(new TransformXMLStrategy());
        $XMLOutput = $this->dashboardService->executeStrategy(['xml' => $xml]);

        return view('dashboard.index', [
            'XMLOutput' => $XMLOutput
        ]);
    }

    public function individual_report()
    {
        $user = auth()->user();
        $projectIds = DB::table('project_user_mappings')
        ->where('user_id', $user->id)
        ->pluck('project_id');

        $tasks = Task::where('user_id', $user->id)->get();
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

        // Budget Utilization
        $this->dashboardService->setStrategy(new BudgetUtilizationStrategy());
        $budgetUtilization = $this->dashboardService->executeStrategy(['projects' => $projects]);

        // Completed Task Data
        $this->dashboardService->setStrategy(new CompletedTaskDataStrategy());
        $completedTaskData = $this->dashboardService->executeStrategy(['tasks' => $tasks]);

        return view('dashboard.individual-report', compact(
            'completedProjects',
            'inProgressProjects',
            'taskStats',
            'budgetUtilization',
            'completedTaskData',
            'projects',
            'tasks',
        ));
    }

    public function team_report()
    {
        $user = auth()->user();
        $tasks = Task::where('user_id', $user->id)->get();
        $projectIds = $tasks->pluck('project_id')->unique();
        $projects = Project::whereIn('id', $projectIds)->get();
        $users = User::where('role', 'user')->get();
    
        return view('dashboard.team-report', compact(['projects','users']));
    }
    
    public function generateReport(Request $request)
    {
        // Get the project and user IDs from the form submission
        $projectId = $request->input('project_id');
        $userId = $request->input('user_id', auth()->id()); // Use the authenticated user's ID if none provided
    
        // Fetch the project and associated budget
        $project = Project::findOrFail($projectId);
        $budget = Budget::find($project->budget_id);
        $expenses = Expense::where('budget_id', $project->budget_id)->get();
    
        // Data for the expenses chart
        $expenseData = [
            'labels' => $expenses->pluck('description')->toArray(),
            'values' => $expenses->pluck('amount')->toArray(),
        ];
    
        // Instantiate strategies
        $budgetUtilizationStrategy = new TeamBudgetUtilizationStrategy();
        $userPerformanceStrategy = new TeamUserPerformanceStrategy();
        $taskCompletionStrategy = new TeamTaskCompletionStrategy();
    
        // Execute strategies
        $budgetUtilization = $budgetUtilizationStrategy->execute($projectId, $userId);
        $userPerformance = $userPerformanceStrategy->execute($projectId, $userId);
        $taskCompletionData = $taskCompletionStrategy->execute($projectId, $userId);


    
        // Return a view with the data
        return view('dashboard.show-report', [
            'budgetUtilization' => $budgetUtilization,
            'expenseData' => $expenseData,
            'userPerformance' => $userPerformance,
            'completionData' => $taskCompletionData,
            'project' => $project,
            'user' => User::find($userId),
        ]);
    }


}
