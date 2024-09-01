<!-- Jeremy -->
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Create New Task</h1>

    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf

        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Task Details</h3>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="name">Task Name</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="user_id">Assign to User</label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <option value="">Select a User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="priority">Priority</label>
                    <select name="priority" id="priority" class="form-control">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                    @error('priority')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="project_id">Select Project</label>
                    <select name="project_id" id="project_id" class="form-control" required>
                        <option value="">Select a Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="expense_id">Related Expense (Optional)</label>
                    <select name="expense_id" id="expense_id" class="form-control">
                        <option value="">None</option>
                        @foreach($expenses as $expense)
                            <option value="{{ $expense->id }}">{{ $expense->description }}</option>
                        @endforeach
                    </select>
                    @error('expense_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Create Task</button>
            </div>
        </div>
    </form>
</div>
@endsection
