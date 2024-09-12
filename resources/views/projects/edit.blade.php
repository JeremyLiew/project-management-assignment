@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Edit Project</h1>

    <form action="{{ route('projects.update', $project->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="name">Project Name</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $project->name) }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="description">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $project->description) }}</textarea>
        </div>

        <div class="form-group mb-3">
            <label for="budget_id">Budget</label>
            <select id="budget_id" name="budget_id" class="form-control" required>
                @foreach($budgets as $budget)
                <option value="{{ $budget->id }}" {{ old('budget_id', $project->budget_id) == $budget->id ? 'selected' : '' }}>
                    {{ $budget->id }} - ${{ number_format($budget->total_amount, 2) }}
                </option>
                @endforeach 
            </select>
        </div>

        @if (auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
        <div class="form-group mb-3">
            <label for="status">Current Status: {{ $project->status }}</label>
            @if ($project->status == 'completed')
            <p>Already Marked as Completed</p>
            @else
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="status" name="status" value="Completed">
                <label class="form-check-label" for="status">
                    Mark as Completed
                </label>
            </div>
            @endif
        </div>

        <div id="members_roles">
            @foreach($project->users as $projectUser)
            <div class="member-role mb-2">
                <select name="members[]" class="form-control mb-2" required>
                    @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ $projectUser->id == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                    @endforeach
                </select>
                <select name="roles[]" class="form-control" required>
                    <option value="Junior" {{ $projectUser->pivot->role == 'Junior' ? 'selected' : '' }}>Junior</option>
                    <option value="Senior" {{ $projectUser->pivot->role == 'Senior' ? 'selected' : '' }}>Senior</option>
                    <option value="Project Manager" {{ $projectUser->pivot->role == 'Project Manager' ? 'selected' : '' }}>Project Manager</option>
                </select>
            </div>
            @endforeach
            <button type="button" class="btn btn-secondary mb-3" onclick="addMemberRole()">Add Another Member</button>
        </div>
        @endif

        <button type="submit" class="btn btn-primary">Update Project</button>
    </form>
</div>

<script>
    function addMemberRole() {
        const container = document.getElementById('members_roles');
        const newDiv = document.createElement('div');
        newDiv.className = 'member-role mb-2';
        newDiv.innerHTML = `
            <select name="members[]" class="form-control mb-2" required>
                <option value="">Select a Member</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <select name="roles[]" class="form-control" required>
                <option value="Junior">Junior</option>
                <option value="Senior">Senior</option>
                <option value="Project Manager">Project Manager</option>
            </select>
        `;
        container.appendChild(newDiv);
    }
</script>
</div>
@endsection
