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
