@extends('layouts.app')
<!-- Jeremy -->
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
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="project_id">Select Project</label>
                    <select name="project_id" id="project_id" class="form-control" required>
                        <option value="">Select a Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="user_id">Assign to User</label>
                    <select name="user_id" id="user_id" class="form-control" required>
                        <option value="">Select a User</option>
                        <!-- Users will be loaded dynamically based on selected project -->
                    </select>
                    @error('user_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="priority">Priority</label>
                    <select name="priority" id="priority" class="form-control">
                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                    </select>
                    @error('priority')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="expense_id">Related Expense (Optional)</label>
                    <select name="expense_id" id="expense_id" class="form-control">
                        <option value="">None</option>
                        <!-- Expenses will be loaded dynamically based on selected project -->
                    </select>
                    @error('expense_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="due_date">Due Date</label>
                    <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date') }}" required>
                    @error('due_date')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Create Task</button>
            </div>
        </div>
    </form>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const projectSelect = document.getElementById('project_id');
    const userSelect = document.getElementById('user_id');
    const expenseSelect = document.getElementById('expense_id');
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

    function fetchExpenses(projectId, selectedExpenseId = null) {
        if (!projectId) {
            expenseSelect.innerHTML = '<option value="">None</option>';
            return;
        }

        axios.get(`/projects/${projectId}/expenses`)
            .then(response => {
                if (response.data.success) {
                    let options = '<option value="">None</option>';
                    response.data.expenses.forEach(expense => {
                        const selected = selectedExpenseId && expense.id == selectedExpenseId ? 'selected' : '';
                        options += `<option value="${expense.id}" ${selected}>${expense.description}</option>`;
                    });
                    expenseSelect.innerHTML = options;
                } else {
                    alert(response.data.message || 'Failed to fetch expenses.');
                    expenseSelect.innerHTML = '<option value="">None</option>';
                }
            })
            .catch(error => {
                console.error('Error fetching expenses:', error);
                alert('An error occurred while fetching expenses.');
                expenseSelect.innerHTML = '<option value="">None</option>';
            });
    }

    projectSelect.addEventListener('change', function() {
        const selectedProjectId = this.value;
        fetchUsers(selectedProjectId);
        fetchExpenses(selectedProjectId);
    });

    @if(old('project_id'))
        fetchUsers("{{ old('project_id') }}", "{{ old('user_id') }}");
        fetchExpenses("{{ old('project_id') }}", "{{ old('expense_id') }}");
    @endif
});

</script>

