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

                @php
                    $userEmail = auth()->user()->email;
                    $excludedEmails = ['admin@gmail.com', 'manager@gmail.com'];
                @endphp

                @if (!in_array($userEmail, $excludedEmails))
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="individual-report-tab" href="{{ route('individual_report') }}" role="tab" aria-controls="report" aria-selected="false">Individual Report</a>
                    </li>
                @endif

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
                                <!-- Render transformed HTML for projects -->
                                {!! $projectXMLOutput !!}
                            </div>
                        </div>
                    </div>


                </div>
            </div>

            @if (auth()->user()->role === 'user')
            <div class="container mt-1">
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Task List</h3>
                        <select id="task-status" class="form-select w-auto">
                            <option value="" disabled selected>STATUS</option>
                            <option value="all">ALL</option>
                            <option value="completed">COMPLETED</option>
                            <option value="inprogress">IN-PROGRESS</option>
                            <option value="pending">PENDING</option>
                        </select>
                    </div>

                    <div id="search-results" class="card-body">
                        <div id="am-task-table" class="table">
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
    // Function to filter the table rows based on search input
    function filterTable(tableId, inputId) {
        $(inputId).on('input', function () {
            var searchText = $(this).val().toLowerCase();
            $(tableId + ' tbody tr').each(function () {
                var rowText = $(this).text().toLowerCase();
                if (rowText.indexOf(searchText) === -1) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        });
    }

    // Handle the project status filter for Admin/Manager
    $('#task-status').on('change', function () {
        let status = this.value;
        let tableBody = document.querySelector('#am-task-table');

        // Clear the search input
        $('#am-search-input').val('');

        // Update the table content based on status
        if (status === 'all') {
            tableBody.innerHTML = `{!! $taskXMLOutput !!}`;  // Use server-rendered content
        } else if (status === 'completed') {
            tableBody.innerHTML = `{!! $completedTasksXMLOutput !!}`;
        } else if (status === 'inprogress') {
            tableBody.innerHTML = `{!! $inProgressTasksXMLOutput !!}`;
        } else if (status === 'pending') {
            tableBody.innerHTML = `{!! $pendingTasksXMLOutput !!}`;
        }

        // Re-apply filter to the new content
        filterTable('#am-task-table');
    });

    // Initially apply the filter to both tables
    filterTable('#user-task-table');
    filterTable('#am-task-table');
});
</script>
@endsection
