@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Team Member Information</h2>
    <form class="form-inline" method="POST" action="{{ route('about-us-post') }}">
        @csrf
        <div class="form-group">
            <label for="id">Member ID</label>
            <input type="text" name="id" class="form-control" placeholder="Enter Member ID" required>
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
    </form>
    <p>&nbsp;</p>
    <div id="member-info">
        @if(isset($member))
            <h3>{{ $member['name'] }}</h3>
            <p><strong>Role:</strong> {{ $member['role'] }}</p>
            <p><strong>Email:</strong> {{ $member['email'] }}</p>
            <img src="{{ asset('images/' . $member['image']) }}" alt="{{ $member['name'] }}" style="width:150px;height:auto;">
        @elseif(isset($error))
            <p>{{ $error }}</p>
        @endif
    </div>
</div>
@endsection
