<?php

/**
 *
 * @author Liew Wei Lun
 */

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
use App\Strategies\TeamTaskCompletionStrategy;
use App\Strategies\TeamUserPerformanceStrategy;
use App\Strategies\TeamBudgetUtilizationStrategy;
use App\Strategies\ProjectStrategy;
use App\Strategies\TaskStrategy;
use App\Strategies\CompletedTasksStrategy;
use App\Strategies\InProgressTasksStrategy;
use App\Strategies\PendingTasksStrategy;
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

        $dashboardService = new DashboardService();

        // Project XML generation
        $dashboardService->setStrategy(new ProjectStrategy());
        $projectXml = $dashboardService->executeStrategy($projects);

        $projectXsl = new DOMDocument();
        $projectXsl->load(public_path('xslt/dashboard_project.xsl'));
        $projectProcessor = new XSLTProcessor();
        $projectProcessor->importStylesheet($projectXsl);
        $projectXMLOutput = $projectProcessor->transformToXml($projectXml);

        // Task XML generation
        $tasks = $projects->flatMap->tasks;
        $dashboardService->setStrategy(new TaskStrategy());
        $taskXml = $dashboardService->executeStrategy($tasks);

        $taskXsl = new DOMDocument();
        $taskXsl->load(public_path('xslt/dashboard_task.xsl'));
        $taskProcessor = new XSLTProcessor();
        $taskProcessor->importStylesheet($taskXsl);
        $taskXMLOutput = $taskProcessor->transformToXml($taskXml);

        // Fetching and transforming tasks with different statuses
        $completedTasksXml = $this->getCompletedTasks();
        $inProgressTasksXml = $this->getInProgressTasks();
        $pendingTasksXml = $this->getPendingTasks();

        return view('dashboard.index', [
            'projectXMLOutput' => $projectXMLOutput,
            'taskXMLOutput' => $taskXMLOutput,
            'completedTasksXMLOutput' => $completedTasksXml,
            'inProgressTasksXMLOutput' => $inProgressTasksXml,
            'pendingTasksXMLOutput' => $pendingTasksXml,
        ]);
    }

    protected function getCompletedTasks()
    {
        $dashboardService = new DashboardService();
        $dashboardService->setStrategy(new CompletedTasksStrategy());

        $completedTasks = Task::where('status', 'completed')->with('expense', 'project')->get();
        $completedTasksXmlDoc = $dashboardService->executeStrategy($completedTasks);

        $completedTasksXsl = new DOMDocument();
        $completedTasksXsl->load(public_path('xslt/dashboard_task.xsl'));
        $completedTasksProcessor = new XSLTProcessor();
        $completedTasksProcessor->importStylesheet($completedTasksXsl);

        return $completedTasksProcessor->transformToXml($completedTasksXmlDoc);
    }

    protected function getInProgressTasks()
    {
        $dashboardService = new DashboardService();
        $dashboardService->setStrategy(new InProgressTasksStrategy());

        $inProgressTasks = Task::where('status', 'in progress')->with('expense', 'project')->get();
        $inProgressTasksXmlDoc = $dashboardService->executeStrategy($inProgressTasks);

        $inProgressTasksXsl = new DOMDocument();
        $inProgressTasksXsl->load(public_path('xslt/dashboard_task.xsl'));
        $inProgressTasksProcessor = new XSLTProcessor();
        $inProgressTasksProcessor->importStylesheet($inProgressTasksXsl);

        return $inProgressTasksProcessor->transformToXml($inProgressTasksXmlDoc);
    }

    protected function getPendingTasks()
    {
        $dashboardService = new DashboardService();
        $dashboardService->setStrategy(new PendingTasksStrategy());

        $pendingTasks = Task::where('status', 'pending')->with('expense', 'project')->get();
        $pendingTasksXmlDoc = $dashboardService->executeStrategy($pendingTasks);

        $pendingTasksXsl = new DOMDocument();
        $pendingTasksXsl->load(public_path('xslt/dashboard_task.xsl'));
        $pendingTasksProcessor = new XSLTProcessor();
        $pendingTasksProcessor->importStylesheet($pendingTasksXsl);

        return $pendingTasksProcessor->transformToXml($pendingTasksXmlDoc);
    }
    
    public function individual_report()
    {
        $user = auth()->user();
        $projects = $user->projects;
        $tasks = Task::where('user_id', $user->id)->get();


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
    
        // Check if the authenticated user is a manager or admin
        if ($user->role === 'manager' || $user->role === 'admin') {
            $users = User::get();

            $projects = Project::get();
        } else {
            // For other roles, get only the user's own projects
            $projects = $user->projects;
    
            // Get users with the role 'user'
            $users = User::where('role', 'user')->get();
        }
    
        return view('dashboard.team-report', compact(['projects', 'users']));
    }
    
    public function getProjectUsers($projectId)
    {
        $project = Project::find($projectId);
    
        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ], 404);
        }
    
        $users = $project->users;
    
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
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
