@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Task Management</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Tasks</h3>
            <a href="{{ route('tasks.create') }}" class="btn btn-primary float-right">Create New Task</a>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('tasks.index') }}" class="mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name">Task Name</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Task Name" value="{{ request('name') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                                <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="user_id">Assigned User</label>
                            <select id="user_id" name="user_id" class="form-control">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="project_id">Project</label>
                            <select id="project_id" name="project_id" class="form-control">
                                <option value="">All Projects</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="priority">Priority</label>
                            <select id="priority" name="priority" class="form-control">
                                <option value="">All Priorities</option>
                                @foreach($priorities as $priority)
                                    <option value="{{ $priority }}" {{ request('priority') == $priority ? 'selected' : '' }}>{{ ucfirst($priority) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="due_date">Due Date</label>
                            <input type="date" id="due_date" name="due_date" class="form-control" value="{{ request('due_date') }}">
                        </div>
                    </div>
                    <div class="col-md-3 mt-4">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            @if ($tasks->isEmpty())
            <p>No tasks available.</p>
            @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Task Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Assigned User</th>
                        <th>Project</th>
                        <th>Priority</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                        <tr>
                            <td>{{ $task->name }}</td>
                            <td class="description">{{ $task->description ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $task->status == 'Completed' ? 'bg-success' : ($task->status == 'In Progress' ? 'bg-warning' : 'bg-secondary') }}">
                                    {{ $task->status }}
                                </span>
                            </td>
                            <td>{{ $task->user->name ?? 'N/A' }}</td>
                            <td>{{ $task->project->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ $task->priority == 'high' ? 'bg-danger' : ($task->priority == 'medium' ? 'bg-warning' : 'bg-success') }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </td>
                            <td>{{ $task->due_date->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
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
@endsection

<style>
.table td.description {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: normal;
}
</style>
