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

                <button type="submit" class="btn btn-primary mt-3">Update Budget</button>
            </div>
        </div>
    </form>
</div>
@endsection
