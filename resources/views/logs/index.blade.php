<!-- Jeremy -->
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Log Management</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card-header">
            <h3 class="card-title">Filters</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('logs.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="user">User</label>
                        <input type="text" id="user" name="user" class="form-control" placeholder="User Name" value="{{ request('user') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="created_at">Created At</label>
                        <input type="date" id="created_at" name="created_at" class="form-control" value="{{ request('created_at') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="action">Action</label>
                        <select id="action" name="action" class="form-control">
                            <option value="">Select Action</option>
                            <option value="Created" {{ request('action') == 'Created' ? 'selected' : '' }}>Created</option>
                            <option value="Updated" {{ request('action') == 'Updated' ? 'selected' : '' }}>Updated</option>
                            <option value="Deleted" {{ request('action') == 'Deleted' ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="log_level">Log Level</label>
                        <select id="log_level" name="log_level" class="form-control">
                            <option value="">All Levels</option>
                            <option value="INFO" {{ request('log_level') == 'INFO' ? 'selected' : '' }}>INFO</option>
                            <option value="DEBUG" {{ request('log_level') == 'DEBUG' ? 'selected' : '' }}>DEBUG</option>
                            <option value="ERROR" {{ request('log_level') == 'ERROR' ? 'selected' : '' }}>ERROR</option>
                            <option value="WARNING" {{ request('log_level') == 'WARNING' ? 'selected' : '' }}>WARNING</option>
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </form>
    </div>
</div>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Logs</h3>
        </div>
        <div class="card-body">
            @if ($logs->isEmpty())
                <p>No logs available.</p>
            @else
                <div class="table-responsive">
                    {!! $htmlOutput !!}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

