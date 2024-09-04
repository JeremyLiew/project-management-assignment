@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>
                
                <div class="card-body">
                    @if(auth()->check())
                        @if(auth()->user()->is_admin == 1)
                            <a href="{{url('admin/routes')}}">Admin</a>
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

@if ($message = Session::get('success'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
    <div id="liveToast" class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <small>Just now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            {{ $message }} You are logged in!
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    const toastLiveExample = document.getElementById('liveToast');
    const toast = new bootstrap.Toast(toastLiveExample);
    toast.show();
</script>
@endsection
