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
                                        
                                        <!-- Project Selection Dropdown -->
                                        <div class="form-group mb-3">
                                            <label for="project_id">Select Project</label>
                                            <select name="project_id" id="project_id" class="form-control" required>
                                                <option value="">Select a Project</option>
                                                @foreach($projects as $project)
                                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('project_id')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        @if (in_array($userRole, $allowedRoles))
                                            <!-- User Selection Dropdown for Admin/Manager -->
                                            <div class="form-group mb-3">
                                                <label for="user_id">Select User</label>
                                                <select name="user_id" id="user_id" class="form-control" required>
                                                    <option value="">Choose User</option>
                                                </select>
                                                @error('user_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        @endif

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
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_id');
    const userSelect = document.getElementById('user_id');

    function fetchUsers(projectId, selectedUserId = null) {
        if (!projectId) {
            userSelect.innerHTML = '<option value="">Choose User</option>';
            return;
        }

        axios.get(`/dashboard/${projectId}/users`)
            .then(response => {
                if (response.data.success) {
                    let options = '<option value="">Choose User</option>';
                    options += '<option value="1">Whole Team</option>';
                    response.data.users.forEach(user => {
                        const selected = selectedUserId && user.id == selectedUserId ? 'selected' : '';
                        options += `<option value="${user.id}" ${selected}>${user.name}</option>`;
                    });
                    userSelect.innerHTML = options;
                } else {
                    alert(response.data.message || 'Failed to fetch users.');
                    userSelect.innerHTML = '<option value="">Choose User</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching users:', error);
                alert('An error occurred while fetching users.');
                userSelect.innerHTML = '<option value="">Choose User</option>';
            });
    }

    projectSelect.addEventListener('change', function() {
        const selectedProjectId = this.value;
        fetchUsers(selectedProjectId);
    });

    @if(old('project_id'))
        fetchUsers("{{ old('project_id') }}", "{{ old('user_id') }}");
    @endif
});
</script>

@endsection
