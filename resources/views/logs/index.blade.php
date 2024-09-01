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

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Filters</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('logs.index') }}">
                <div class="form-group">
                    <label for="user">User</label>
                    <input type="text" id="user" name="user" class="form-control" value="{{ request('user') }}">
                </div>
                <div class="form-group">
                    <label for="created_at">Created At</label>
                    <input type="date" id="created_at" name="created_at" class="form-control" value="{{ request('created_at') }}">
                </div>
                <div class="form-group">
                    <label for="action">Action</label>
                    <select id="action" name="action" class="form-control">
                        <option value="">Select Action</option>
                        <option value="Created" {{ request('action') == 'Created' ? 'Selected' : '' }}>Created</option>
                        <option value="Updated" {{ request('action') == 'Updated' ? 'Selected' : '' }}>Updated</option>
                        <option value="Deleted" {{ request('action') == 'Deleted' ? 'Selected' : '' }}>Deleted</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Logs</h3>
        </div>
        <div class="card-body">
            @if ($logs->isEmpty())
                <p>No logs available.</p>
            @else
                {!! $htmlOutput !!}
            @endif
        </div>
    </div>
</div>
@endsection
