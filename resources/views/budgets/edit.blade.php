@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Edit Budget</h1>

    <form action="{{ route('budgets.update', $budget->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Budget Details</h3>
            </div>
            <div class="card-body">
                <!-- Total Amount -->
                <div class="form-group mb-3">
                    <label for="total_amount">Total Amount</label>
                    <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" value="{{ old('total_amount', $budget->total_amount) }}" required>
                    @error('total_amount')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Expense Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h3 class="card-title">Expenses</h3>
                    </div>
                    <div class="card-body">
                        <div id="expense-fields">
                            @foreach ($budget->expenses as $key => $expense)
                                <div class="expense-row mt-3">
                                    <div class="form-group mb-3">
                                        <label for="expense_amount_{{ $key }}">Expense Amount</label>
                                        <input type="number" step="0.01" name="expenses[{{ $key }}][amount]" class="form-control" id="expense_amount_{{ $key }}" value="{{ old('expenses.' . $key . '.amount', $expense->amount) }}" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="expense_description_{{ $key }}">Expense Description</label>
                                        <input type="text" name="expenses[{{ $key }}][description]" class="form-control" id="expense_description_{{ $key }}" value="{{ old('expenses.' . $key . '.description', $expense->description) }}" required>
                                    </div>
                                    <input type="hidden" name="expenses[{{ $key }}][id]" value="{{ $expense->id }}">
                                    <button type="button" class="btn btn-danger btn-sm delete-expense" data-index="{{ $key }}">Delete</button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-secondary mt-3" id="add-expense">Add Another Expense</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update Budget and Expenses</button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let expenseIndex = {{ count($budget->expenses) }};

    // Add new expense fields
    document.getElementById('add-expense').addEventListener('click', function () {
        let expenseFields = `
            <div class="expense-row mt-3">
                <div class="form-group mb-3">
                    <label for="expense_amount_${expenseIndex}">Expense Amount</label>
                    <input type="number" step="0.01" name="expenses[${expenseIndex}][amount]" class="form-control" id="expense_amount_${expenseIndex}" required>
                </div>
                <div class="form-group mb-3">
                    <label for="expense_description_${expenseIndex}">Expense Description</label>
                    <input type="text" name="expenses[${expenseIndex}][description]" class="form-control" id="expense_description_${expenseIndex}" required>
                </div>
                <button type="button" class="btn btn-danger btn-sm delete-expense" data-index="${expenseIndex}">Delete</button>
            </div>`;
        document.getElementById('expense-fields').insertAdjacentHTML('beforeend', expenseFields);
        expenseIndex++;
    });

    // Delete expense fields
    document.getElementById('expense-fields').addEventListener('click', function (event) {
        if (event.target.classList.contains('delete-expense')) {
            let index = event.target.getAttribute('data-index');
            document.querySelector(`.expense-row .delete-expense[data-index="${index}"]`).parentElement.remove();
        }
    });
});
</script>
@endsection
