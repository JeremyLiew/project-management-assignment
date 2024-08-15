@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1>Edit Project</h1>

    <!-- Flash messages -->
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Project Edit Form -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Project Details</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('projects.update', $project->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group mb-3">
                    <label for="name">Project Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $project->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $project->description) }}</textarea>
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="budget_id">Budget</label>
                    <select id="budget_id" name="budget_id" class="form-control" required>
                        <option value="">Select a Budget</option>
                        @foreach($budgets as $budget)
                            <option value="{{ $budget->id }}" {{ old('budget_id', $project->budget_id) == $budget->id ? 'selected' : '' }}>
                                {{ $budget->id }} - ${{ number_format($budget->total_amount, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('budget_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="members_roles">Assign Members and Roles</label>
                    <div id="members_roles">
                        @foreach ($project->users as $user)
                            <div class="member-role mb-2">
                                <select name="members[]" class="form-control mb-2" required>
                                    <option value="">Select a Member</option>
                                    @foreach ($users as $userOption)
                                        <option value="{{ $userOption->id }}" {{ $userOption->id == $user->id ? 'selected' : '' }}>
                                            {{ $userOption->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="roles[]" class="form-control" required>
                                    <option value="Junior" {{ $user->pivot->role == 'Junior' ? 'selected' : '' }}>Junior</option>
                                    <option value="Senior" {{ $user->pivot->role == 'Senior' ? 'selected' : '' }}>Senior</option>
                                    <option value="Project Manager" {{ $user->pivot->role == 'Project Manager' ? 'selected' : '' }}>Project Manager</option>
                                </select>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-secondary" onclick="addMemberRole()">Add Another Member</button>
                    @error('members')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    @error('roles')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update Project</button>
            </form>
        </div>
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
