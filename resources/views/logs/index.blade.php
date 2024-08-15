@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Log Management</h1>

    <!-- Flash messages -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Log Listing -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Logs</h3>
        </div>
        <div class="card-body">
            @if ($logs->isEmpty())
                <p>No logs available.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Action</th>
                            <th>Model Type</th>
                            <th>Model ID</th>
                            <th>User</th>
                            <th>Changes</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>{{ $log->action }}</td>
                                <td>{{ $log->model_type }}</td>
                                <td>{{ $log->model_id }}</td>
                                <td>{{ optional($log->user)->name ?? 'N/A' }}</td>
                                <td>
                                    <pre>{{ json_encode(json_decode($log->changes), JSON_PRETTY_PRINT) }}</pre>
                                </td>
                                <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
