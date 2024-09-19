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
                    <a class="nav-link" id="individual-report-tab" href="{{ route('individual_report') }}" role="tab" aria-controls="report" aria-selected="false">Individual Report</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="team-report-tab" data-bs-toggle="tab" href="#team-report" role="tab" aria-controls="team-report" aria-selected="false">Team Report</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="myTabContent">
                <!-- Team Report Section -->
                <div class="tab-pane fade show active" id="team-report" role="tabpanel" aria-labelledby="team-report-tab">
                    <div class="row">
                        <!-- Project Selector -->
                        <div class="col-md-12 mb-4">
                            <div class="card">
                                <div class="card-header">Select Project</div>
                                @php
                                    $userRole = auth()->user()->role; // Assuming 'role' is the column for user roles
                                    $allowedRoles = ['admin', 'manager'];
                                @endphp

                                <div class="card-body">
                                    <form id="projectSelectorForm" action="{{ route('report.generate') }}" method="POST">
                                        @csrf
                                        @if (in_array($userRole, $allowedRoles))
                                            <!-- User Selection Dropdown -->
                                            <div class="mb-3">
                                                <label for="userSelect" class="form-label">Choose a User</label>
                                                <select id="userSelect" class="form-select" name="user_id">
                                                    <option value="">Select a User</option>
                                                        <option value="1">Whole project</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        
                                        <!-- Project Selection Dropdown -->
                                        <div class="mb-3">
                                            <label for="projectSelect" class="form-label">Choose a Project</label>
                                            <select id="projectSelect" class="form-select" name="project_id">
                                                <option value="">Select a Project</option>
                                                @foreach($projects as $project)
                                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary">View Report</button>
                                    </form>
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

</script>

@endsection
