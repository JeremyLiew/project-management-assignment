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
        <div class="card-body">
            @if ($projects->isEmpty())
            <p>No projects available.</p>
            @else
            <table class="table table-bordered">
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

    <!-- Additional In-Progress Projects for Admin/Manager -->
    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
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
        <div id="projects-table">
        </div>
    </div>

    <script>
        document.getElementById('project-status').addEventListener('change', function () {
            let status = this.value;
            let tableBody = document.querySelector('#projects-table');

            if (status === 'all') {
                tableBody.innerHTML = `{!! $xslt !!}`;
            } else if (status === 'completed') {
                tableBody.innerHTML = `{!! $complete !!}`;
            } else if (status === 'inprogress') {
                tableBody.innerHTML = `{!! $inprogress !!}`;
            }
        });
    </script>
    @endif
</div>
@endsection
