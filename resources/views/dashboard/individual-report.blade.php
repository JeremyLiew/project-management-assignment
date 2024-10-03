@extends('layouts.app')

@section('content')

 <!-- @author Liew Wei Lun -->
 
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="projects-tab" href="{{ route('dashboard') }}" role="tab" aria-controls="projects" aria-selected="true">Projects and Tasks</a>
                </li>
                @php
                    $userEmail = auth()->user()->email;
                    $excludedEmails = ['admin@gmail.com', 'manager@gmail.com'];
                @endphp

                @if (!in_array($userEmail, $excludedEmails))
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="individual-report-tab" href="{{ route('individual_report') }}" role="tab" aria-controls="report" aria-selected="false">Individual Report</a>
                    </li>
                @endif
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="team-report-tab" href="{{ route('team_report') }}" role="tab" aria-controls="team-report" aria-selected="false">Team Report</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="myTabContent">
                <!-- Individual Reports Section -->
                <div class="tab-pane fade show active" id="individual-report" role="tabpanel" aria-labelledby="individual-report-tab">
                    <div class="row">
                    <!-- Project Overload Chart -->
                    <div class="col-md-6 col-lg-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header text-center">
                                <h5 class="m-0">Project Overload</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="projectOverloadChart"></canvas>
                                </div>
                            </div>
                            <div class="card-footer text-muted text-center">
                                Visualize project overload metrics.
                            </div>
                        </div>
                    </div>

                    <!-- Task Overload Chart -->
                    <div class="col-md-6 col-lg-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header text-center">
                                <h5 class="m-0">Task Overload</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="taskOverloadChart"></canvas>
                                </div>
                            </div>
                            <div class="card-footer text-muted text-center">
                                Analyze task overload across the individual.
                            </div>
                        </div>
                    </div>

                    <!-- Budget Utilization Report -->
                    <div class="col-md-6 col-lg-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header text-center">
                                <h5 class="m-0">Budget Utilization Report</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="budgetUtilizationChart"></canvas>
                                </div>
                            </div>
                            <div class="card-footer text-muted text-center">
                                Track your budget utilization effectively.
                            </div>
                        </div>
                    </div>

                    <!-- User Performance Report -->
                    <div class="col-md-6 col-lg-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header text-center">
                                <h5 class="m-0">Individual User Performance Report</h5>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="userPerformanceChart"></canvas>
                                </div>
                            </div>
                            <div class="card-footer text-muted text-center">
                                Review individual user performance metrics.
                            </div>
                        </div>
                    </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap CSS and JS for Tabs -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Project Overload Chart
        var ctxProject = document.getElementById('projectOverloadChart').getContext('2d');
        var projectOverloadChart = new Chart(ctxProject, {
            type: 'doughnut',
            data: {
                labels: ['In Progress', 'Completed'],
                datasets: [{
                    label: 'Project Overload',
                    data: [{{ $inProgressProjects }}, {{ $completedProjects }}],
                    backgroundColor: ['#ff6384', '#36a2eb'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed > 0) {
                                    label += context.parsed;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
        
        // Task Overload Chart
        var ctxTask = document.getElementById('taskOverloadChart').getContext('2d');
        var taskStats = @json($taskStats);

        new Chart(ctxTask, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'In Progress', 'Completed'],
                datasets: [{
                    label: 'Task Overload',
                    data: [
                        taskStats.pendingTasks,
                        taskStats.inProgressTasks,
                        taskStats.completedTaskCount
                    ],
                    backgroundColor: ['#ff6384', '#ffcd56', '#36a2eb'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed > 0) {
                                    label += context.parsed;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Budget Utilization Report
        var ctxBudget = document.getElementById('budgetUtilizationChart').getContext('2d');
        var budgetUtilizationData = @json($budgetUtilization);

        

        var labels = budgetUtilizationData.map(data => data.projectName);
        var data = budgetUtilizationData.map(data => data.utilization);

        new Chart(ctxBudget, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Budget Utilization (%)',
                    data: data,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: 100
                    }
                }
            }
        });

        // Individual User Performance Report (Bar Chart)
        var ctxUser = document.getElementById('userPerformanceChart').getContext('2d');
        var userPerformanceData = @json($completedTaskData);

        console.log(userPerformanceData);

        var labelsUser = userPerformanceData.map(data => data.taskName);
        var dataUser = userPerformanceData.map(data => data.hoursSpent);

        new Chart(ctxUser, {
            type: 'bar',
            data: {
                labels: labelsUser,
                datasets: [{
                    label: 'Hours Spent',
                    data: dataUser,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Task Name'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Hours Spent'
                        }
                    }
                }
            }
        });

 
    });
</script>

@endsection
