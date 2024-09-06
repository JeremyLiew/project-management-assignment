@extends('layouts.app')

@section('content')
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
@endsection
