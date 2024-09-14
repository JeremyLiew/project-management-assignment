@extends('layouts.app')
<!-- Jeremy -->
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

                <div class="form-group mb-3">
                    <label for="name">Task Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $task->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $task->description) }}</textarea>
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

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

                <div class="form-group mb-3">
                    <label for="user_id">Assign User</label>
                    <select id="user_id" name="user_id" class="form-control" required>
                        <!-- Users will be loaded dynamically based on selected project -->
                    </select>
                    @error('user_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

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

                <div class="form-group mb-3">
                    <label for="priority">Priority</label>
                    <select name="priority" id="priority" class="form-control">
                        <option value="low" {{ old('priority', $task->priority ?? 'low') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $task->priority ?? 'low') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $task->priority ?? 'low') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                </div>

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

                <div class="form-group mb-3">
                    <label for="due_date">Due Date</label>
                    <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
                    @error('due_date')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update Task</button>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_id');
    const userSelect = document.getElementById('user_id');
    const dueDateInput = document.getElementById('due_date');

    const today = new Date().toISOString().split('T')[0];
    dueDateInput.setAttribute('min', today);

    function fetchUsers(projectId, selectedUserId = null) {
        if (!projectId) {
            userSelect.innerHTML = '<option value="">Select a User</option>';
            return;
        }

        axios.get(`/projects/${projectId}/users`)
            .then(response => {
                if (response.data.success) {
                    let options = '<option value="">Select a User</option>';
                    response.data.users.forEach(user => {
                        const selected = selectedUserId && user.id == selectedUserId ? 'selected' : '';
                        options += `<option value="${user.id}" ${selected}>${user.name}</option>`;
                    });
                    userSelect.innerHTML = options;
                } else {
                    alert(response.data.message || 'Failed to fetch users.');
                    userSelect.innerHTML = '<option value="">Select a User</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching users:', error);
                alert('An error occurred while fetching users.');
                userSelect.innerHTML = '<option value="">Select a User</option>';
            });
    }

    const selectedProjectId = "{{ old('project_id', $task->project_id) }}";
    const selectedUserId = "{{ old('user_id', $task->user_id) }}";
    fetchUsers(selectedProjectId, selectedUserId);
});
</script>
