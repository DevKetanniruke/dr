@extends('layouts.app')

@section('title', 'Staff Management')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-person-gear text-teal me-2"></i> Clinic Staff Management</h4>
            <p class="text-muted mb-0">Manage doctor, receptionist, and administrator user accounts.</p>
        </div>
        <button class="btn btn-brand shadow-sm d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus-fill fs-5"></i> Add New Staff Account
        </button>
    </div>

    <!-- Staff Table -->
    <div class="card card-custom">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th>Staff Name</th>
                            <th>Email Address</th>
                            <th>Role</th>
                            <th>Specialization / Title</th>
                            <th>Phone Number</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="fw-bold text-dark">
                                    {{ $user->name }}
                                    @if($user->id === Auth::id())
                                        <span class="badge bg-light text-primary border ms-1">You</span>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge badge-role badge-role-{{ $user->role }}">
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="small text-muted">{{ $user->specialization ?? '—' }}</td>
                                <td class="small">{{ $user->phone ?? '—' }}</td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle">Active</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-light border" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    @if($user->id !== Auth::id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Deactivate staff user account?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-archive"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>

                            <!-- Edit User Modal -->
                            <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content text-start">
                                        <form action="{{ route('users.update', $user) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit Staff Account - {{ $user->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                                                    <select name="role" class="form-select" required>
                                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin / Owner</option>
                                                        <option value="doctor" {{ $user->role === 'doctor' ? 'selected' : '' }}>Doctor / Physician</option>
                                                        <option value="receptionist" {{ $user->role === 'receptionist' ? 'selected' : '' }}>Receptionist</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">Specialization / Department</label>
                                                    <input type="text" name="specialization" class="form-control" value="{{ old('specialization', $user->specialization) }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">Phone Number</label>
                                                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">Account Status <span class="text-danger">*</span></label>
                                                    <select name="is_active" class="form-select" required>
                                                        <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                                                        <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive / Suspended</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-medium">New Password (leave blank to keep existing)</label>
                                                    <input type="password" name="password" class="form-control" placeholder="••••••••">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-brand">Update Account</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2 text-teal"></i> Create Staff Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-medium">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" required placeholder="Dr. Jane Smith">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control" required placeholder="jane@clinic.com">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label fw-medium">Role <span class="text-danger">*</span></label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="doctor">Doctor / Physician</option>
                            <option value="receptionist">Receptionist</option>
                            <option value="admin">Admin / Clinic Owner</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="specialization" class="form-label fw-medium">Specialization / Role Title</label>
                        <input type="text" name="specialization" id="specialization" class="form-control" placeholder="e.g. Pediatrician, Front Desk Manager">
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label fw-medium">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="+1 (555) 000-0000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand">Create Staff User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
