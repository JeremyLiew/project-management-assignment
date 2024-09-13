@extends('layouts.app')

@section('content')
<div class="container mt-1">
    <h1>Project Management</h1>

    <!-- Flash messages -->
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <!-- Project Listing -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Projects</h3>
            <a href="{{ route('projects.create') }}" class="btn btn-primary float-right">Create New Project</a>
        </div>
        <input type="text" id="search-input" class="form-control w-auto" placeholder="Search projects...">
        <div id="search-results" class="card-body">
            @if ($projects->isEmpty())
            <p>No projects available.</p>
            @else
            <table id="user-projects-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Budget</th>
                        <th>Status</th>
                        <th>Assigned Members</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                    <tr>
                        <td>{{ $project->id }}</td>
                        <td>{{ $project->name }}</td>
                        <td>{{ $project->description }}</td>
                        <td>${{ number_format($project->budget->total_amount ?? 0, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $project->status === 'completed' ? 'success' : 'warning' }}">
                                {{ $project->status }}
                            </span>
                        </td>
                        <td>
                            @foreach ($project->users as $user)
                            {{ $user->name }}<br>
                            @endforeach
                        </td>
                        <td>
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

<!-- Additional In-Progress Projects for Admin/Manager -->
@if (auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
<div class="container mt-1">
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Projects List</h3>
            <select id="project-status" class="form-select w-auto">
                <option value="" disabled selected>STATUS</option>
                <option value="all">ALL</option>
                <option value="completed">COMPLETED</option>
                <option value="inprogress">IN-PROGRESS</option>
            </select>
        </div>
        <input type="text" id="am-search-input" class="form-control w-auto" placeholder="Search projects...">
        <div id="search-results" class="card-body">
            <div id="am-projects-table" class="table table-bordered">
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    function filterTable(tableId, inputId) {
        $(inputId).on('input', function () {
            var searchText = $(this).val().toLowerCase();
            $(tableId + ' tbody tr').each(function () {
                var rowText = $(this).text().toLowerCase();
                if (rowText.indexOf(searchText) === -1) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
    }

    // Filter user projects table
    filterTable('#user-projects-table', '#search-input');

    // Filter admin/manager projects table
    filterTable('#am-projects-table', '#am-search-input');

    $('#project-status').on('change', function () {
        let status = this.value;
        let tableBody = document.querySelector('#am-projects-table');

        // Clear the search input
        $('#am-search-input').val('');

        if (status === 'all') {
            tableBody.innerHTML = `{!! $xslt !!}`;
        } else if (status === 'completed') {
            tableBody.innerHTML = `{!! $complete !!}`;
        } else if (status === 'inprogress') {
            tableBody.innerHTML = `{!! $inprogress !!}`;
        }

        // Re-apply filter to the new content
        filterTable('#am-projects-table', '#am-search-input');
    });
});
</script>
@endif
</div>
@endsection
