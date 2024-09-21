@extends('layouts.app')

@section('content')
    <div class="alert alert-danger">
        <h4>Service Unavailable</h4>
        <p>{{ $message }}</p>
    </div>
@endsection
