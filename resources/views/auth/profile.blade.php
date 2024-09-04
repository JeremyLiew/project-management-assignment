@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="mb-4">Profile</h2>
            <div class="card">
                <div class="card-header">
                    Profile Information
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name:</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" required>
                        </div>
                        <button type="submit" class="btn btn-success">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
