@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="row justify-content-center mt-5">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ $message }}
                    </div>
                    @elseif (!auth()->check())
                    <div class="alert alert-warning">
                        You are not logged in. Please <a href="{{ route('login') }}">login</a>.
                    </div>
                    @endif
                </div>

                <div class="card-body">
                    @if(auth()->check())
                    @if(auth()->user()->role === 'admin')
                    <a href="{{url('admin/routes')}}">Admin</a>
                    @elseif(auth()->user()->role === 'manager')
                    <a href="{{url('manager/routes')}}">Manager</a>
                    @else
                    <div class="panel-heading">Normal User</div>
                    @endif
                    @else
                    <div class="alert alert-info">
                        You are not logged in. Please <a href="{{ route('login') }}">login</a> to continue.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
