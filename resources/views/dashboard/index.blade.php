@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="projects-tab" href="{{ route('dashboard') }}" role="tab" aria-controls="projects" aria-selected="true">Projects and Tasks</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="individual-report-tab" href="{{ route('individual_report') }}" role="tab" aria-controls="report" aria-selected="false">Indivual Report</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="team-report-tab" href="{{ route('team_report') }}" role="tab" aria-controls="team-report" aria-selected="false">Team Report</a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="myTabContent">
                <!-- Projects and Tasks Section -->
                <div class="tab-pane fade show active" id="projects" role="tabpanel" aria-labelledby="projects-tab">

                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <!-- Render transformed HTML -->
                                {!! $XMLOutput !!}
                            </div>
                        </div>
                    </div>

                </div>
                
        </div>
    </div>
</div>

@endsection
