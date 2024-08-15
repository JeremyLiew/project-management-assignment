@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Edit Task</h1>

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Task Details</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Task Name -->
                <div class="form-group mb-3">
                    <label for="name">Task Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $task->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Task Description -->
                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3" required>{{ old('description', $task->description) }}</textarea>
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Task Status -->
                <div class="form-group mb-3">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="Pending" {{ old('status', $task->status) == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="In Progress" {{ old('status', $task->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Completed" {{ old('status', $task->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Assign User -->
                <div class="form-group mb-3">
                    <label for="user_id">Assign User</label>
                    <select id="user_id" name="user_id" class="form-control" required>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id', $task->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('user_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Select Project -->
                <div class="form-group mb-3">
                    <label for="project_id">Project</label>
                    <select id="project_id" name="project_id" class="form-control" disabled>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Priority -->
                <div class="form-group mb-3">
                    <label for="priority">Priority</label>
                    <select name="priority" id="priority" class="form-control">
                        <option value="low" {{ old('priority', $task->priority ?? 'low') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $task->priority ?? 'low') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $task->priority ?? 'low') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>

                <!-- Related Expense -->
                <div class="form-group mb-3">
                    <label for="expense_id">Related Expense</label>
                    <select id="expense_id" name="expense_id" class="form-control">
                        <option value="">None</option>
                        @foreach($expenses as $expense)
                            <option value="{{ $expense->id }}" {{ old('expense_id', $task->expense_id) == $expense->id ? 'selected' : '' }}>{{ $expense->description }}</option>
                        @endforeach
                    </select>
                    @error('expense_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update Task</button>
            </form>
        </div>
    </div>
</div>
@endsection
