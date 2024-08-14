@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Create New Task</h1>
    <form action="{{ route('tasks.store') }}" method="POST">
    @csrf
    <div class="form-group">
        <label for="name">Task Name</label>
        <input type="text" name="name" id="name" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" class="form-control" rows="3"></textarea>
    </div>

    <div class="form-group">
        <label for="user_id">Assign to User</label>
        <select name="user_id" id="user_id" class="form-control" required>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="priority">Priority</label>
        <select name="priority" id="priority" class="form-control">
            <option value="low" {{ old('priority', $task->priority ?? 'low') == 'low' ? 'selected' : '' }}>Low</option>
            <option value="medium" {{ old('priority', $task->priority ?? 'low') == 'medium' ? 'selected' : '' }}>Medium</option>
            <option value="high" {{ old('priority', $task->priority ?? 'low') == 'high' ? 'selected' : '' }}>High</option>
        </select>
    </div>

    <div class="form-group">
        <label for="project_id">Select Project</label>
        <select name="project_id" id="project_id" class="form-control" required>
            @foreach($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="expense_id">Related Expense (Optional)</label>
        <select name="expense_id" id="expense_id" class="form-control">
            <option value="">None</option>
            @foreach($expenses as $expense)
                <option value="{{ $expense->id }}">{{ $expense->description }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Create Task</button>
</form>

</div>
@endsection
