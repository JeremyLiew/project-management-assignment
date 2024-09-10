@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <!-- Render transformed HTML -->
            {!! $XMLOutput !!}
        </div>
    </div>
</div>
@endsection
