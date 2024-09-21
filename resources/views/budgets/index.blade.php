@extends('layouts.app')

@section('content')
 <!-- @author Liew Wei Lun -->

<div class="container mt-5">
    <h1>Budget Management</h1>

    <!-- Flash messages -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Budget</h3>
        <button id="fetch-data-btn" class="btn btn-primary">Fetch Budgets</button> <!-- Added button -->
    </div>
        <div id="search-results" class="card-body">
            <!-- Results will be displayed here -->
        </div>
    </div>

    <!-- Budget Listing -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Budgets</h3>
            <a href="{{ route('budgets.create') }}" class="btn btn-primary float-right">Create New Budget</a>
        </div>
        <div class="card-body">
            @if ($budgets->isEmpty())
                <p>No budgets available.</p>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Total Amount</th>
                            <th>Expenses</th>
                            <th>Extra Cost</th> <!-- New column for extra cost -->
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($budgets as $budget)
                            <tr>
                                <td>{{ $budget->id }}</td>
                                <td>${{ number_format($budget->total_amount, 2) }}</td>
                                <td>
                                    @if ($budget->expenses->isEmpty())
                                        No expenses
                                    @else
                                        <ul>
                                            @foreach ($budget->expenses as $expense)
                                                <li>${{ number_format($expense->amount, 2) }} - {{ $expense->description }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                                <td>${{ number_format($budget->extra_cost, 2) }}</td> <!-- Display extra cost -->
                                <td>
                                    <a href="{{ route('budgets.edit', $budget->id) }}" class="btn btn-warning">Edit</a>
                                    <form action="{{ route('budgets.destroy', $budget->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#fetch-data-btn').on('click', function () {
    $.ajax({
        url: 'http://localhost:5194/api/budgets/display',  // Correct port and endpoint
        method: 'GET',
        success: function (data) {
            var resultsHtml = '<table class="table table-bordered"><thead><tr><th>ID</th><th>Total Amount</th></tr></thead><tbody>';
            if (Array.isArray(data)) {
                data.forEach(function (budget) {
                    resultsHtml += '<tr><td>' + budget.id + '</td><td>$' + (budget.total_amount || 0).toFixed(2) + '</td></tr>';
                });
            } else {
                resultsHtml += '<tr><td colspan="2">No results found</td></tr>';
            }
            resultsHtml += '</tbody></table>';
            $('#search-results').html(resultsHtml);
        },
        error: function (xhr, status, error) {
            console.error('Error fetching budgets:', error);
            $('#search-results').html('<p class="text-danger">Error fetching budget data</p>');
        }
    });
});
</script>

@endsection
