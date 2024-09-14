@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="projects-tab" href="{{ route('dashboard') }}" role="tab" aria-controls="projects" aria-selected="true">Projects and Tasks</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="individual-report-tab" href="{{ route('individual_report') }}" role="tab" aria-controls="report" aria-selected="false">Indivual Report</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="team-report-tab" href="{{ route('team_report') }}" role="tab" aria-controls="team-report" aria-selected="false">Team Report</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="myTabContent">                   
                <!-- Team Report Section -->
                <div class="tab-pane fade" id="team-report" role="tabpanel" aria-labelledby="team-report-tab">
                    <div class="row">

                        <!-- Project Selector -->
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header">Select Project</div>
                                <div class="card-body">
                                    <form id="projectSelectorForm">
                                        <div class="mb-3">
                                            <label for="projectSelect" class="form-label">Choose a Project</label>

                                        </div>
                                        <button type="submit" class="btn btn-primary">View Report</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Example Team Performance Chart -->
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header">Team Performance</div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="teamPerformanceChart"></canvas>
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
        // Team report
        var ctx = document.getElementById('teamPerformanceChart').getContext('2d');
        var teamPerformanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [], // Dynamic labels
                datasets: [{
                    label: 'Team Performance',
                    data: [], // Dynamic data
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
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
                        beginAtZero: true
                    }
                }
            }
        });

        document.getElementById('projectSelectorForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var projectId = document.getElementById('projectSelect').value;

        // Fetch data based on selected project
        fetch(`/api/team-performance/${projectId}`)
            .then(response => response.json())
            .then(data => {
                // Update chart with fetched data
                teamPerformanceChart.data.labels = data.labels;
                teamPerformanceChart.data.datasets[0].data = data.values;
                teamPerformanceChart.update();
            });
    });

    });
</script>

<style>
    .chart-container {
        position: relative;
        height: 250px; /* Adjust height as needed */
        width: 100%; /* Full width */
    }

    canvas {
        height: 100% !important; /* Make sure the canvas takes the full height of the container */
        width: 100% !important; /* Make sure the canvas takes the full width of the container */
    }
</style>


@endsection