@extends('layouts.app')

@section('content')
<div class="container">
    <div class="company-about mb-5">
        <h2 class="display-4 text-center">About Us</h2>
        @if(isset($aboutUsContent))
            <div class="about-us-content mt-4">
                <h3 class="display-5">Our Story</h3>
                <p>{{ $aboutUsContent['company_story'] }}</p>

                <h3 class="display-5 mt-4">Mission</h3>
                <p>{{ $aboutUsContent['mission'] }}</p>

                <h3 class="display-5 mt-4">Vision</h3>
                <p>{{ $aboutUsContent['vision'] }}</p>

                <h3 class="display-5 mt-4">Values</h3>
                <p>{{ $aboutUsContent['values'] }}</p>
            </div>
        @else
            <p>No company information available.</p>
        @endif
    </div>

    <hr class="my-4">

    <h2 class="display-4 text-center">Team Member Information</h2>

    <form class="form-inline justify-content-center mb-4" method="POST" action="{{ route('about-us-post') }}">
        @csrf
        <div class="form-group mx-sm-3">
            <input type="text" name="query" class="form-control form-control-lg" placeholder="Search by name or skill" value="{{ old('query') }}">
        </div>
        <button type="submit" class="btn btn-primary btn-lg">Search</button>
    </form>

    <div id="member-info">
        @if(isset($members) && count($members) > 0)
            <div class="row">
                @foreach($members as $member)
                    <div class="col-md-4 mb-4">
                        <div class="member-card card shadow-sm">
                            <img src="{{ $member['image'] }}" alt="{{ $member['name'] }}" class="card-img-top member-image">
                            <div class="card-body">
                                <h5 class="card-title">{{ $member['name'] }}</h5>
                                <p class="card-text"><strong>Role:</strong> {{ $member['role'] }}</p>
                                <p class="card-text"><strong>Email:</strong> {{ $member['email'] }}</p>
                                <p class="card-text"><strong>Skills:</strong> {{ implode(', ', $member['skills']) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif(isset($error))
            <p class="text-danger">{{ $error }}</p>
        @else
            <p class="text-muted">No members available.</p>
        @endif
    </div>
</div>
@endsection
