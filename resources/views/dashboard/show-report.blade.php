@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Back Button -->
            <a href="{{ url()->previous() }}" class="btn btn-secondary mb-4">Back</a>

            <div class="row">
                <!-- Budget Utilization Line Chart -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card">
                        <div class="card-header">Budget Utilization</div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="budgetUtilizationChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expenses Doughnut Chart -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card">
                        <div class="card-header">Project Expenses</div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="expenseDataChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Team User Performance Bar Chart -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card">
                        <div class="card-header">Team User Performance</div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="teamUserPerformanceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Task Completion Doughnut Chart -->
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card">
                        <div class="card-header">Team Task Completion</div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="taskCompletionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Initialize Budget Utilization Chart
    var ctxBudgetUtilization = document.getElementById('budgetUtilizationChart').getContext('2d');
    var budgetUtilizationChart = new Chart(ctxBudgetUtilization, {
        type: 'line',
        data: {
            labels: @json($budgetUtilization['labels']),
            datasets: [{
                label: 'Budget Utilization',
                data: @json($budgetUtilization['values']),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: false,
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: { beginAtZero: true },
                y: { beginAtZero: true }
            }
        }
    });

    // Initialize Team User Performance Chart
    var ctxTeamUserPerformance = document.getElementById('teamUserPerformanceChart').getContext('2d');
    var userPerformanceData = @json($userPerformance);

    var labelsUser = userPerformanceData.map(item => item.userName);
    var dataUser = userPerformanceData.map(item => item.timeSpent);

    console.log(labelsUser);
    console.log(dataUser);

    var teamUserPerformanceChart = new Chart(ctxTeamUserPerformance, {
        type: 'bar',
        data: {
            labels: labelsUser,
            datasets: [{
                label: 'Time Spent (minutes)',
                data: dataUser,
                backgroundColor: 'rgba(255, 159, 64, 0.5)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'User Name'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Time Spent (minutes)'
                    }
                }
            }
        }
    });

    // Initialize Task Completion Doughnut Chart
    var ctxTaskCompletion = document.getElementById('taskCompletionChart').getContext('2d');
    var taskCompletionChart = new Chart(ctxTaskCompletion, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'In Progress', 'Pending'],
            datasets: [{
                label: 'Task Status',
                data: [
                    @json($completionData['Completed']),
                    @json($completionData['In Progress']),
                    @json($completionData['Pending'])
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
        }
    });

    // Initialize Expense Data Chart
    var ctxExpenseData = document.getElementById('expenseDataChart').getContext('2d');
    var expenseDataChart = new Chart(ctxExpenseData, {
        type: 'doughnut',
        data: {
            labels: @json($expenseData['labels']),
            datasets: [{
                label: 'Expenses',
                data: @json($expenseData['values']),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(255, 159, 64, 0.5)',
                    'rgba(255, 205, 86, 0.5)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 205, 86, 1)'
                ],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
        }
    });
});
</script>
@endsection
