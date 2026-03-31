@extends('layouts.admin', ['title' => 'Admin Users'])

@section('content')
    <div class="admin-screen">
        <div class="support-page-head mb-3">
            <div>
                <h4 class="mb-1">Users</h4>
                <p class="muted-note mb-0">Manage account details and admin access from a simple user table.</p>
            </div>
        </div>

        <div class="panel-card admin-table-card">
            <div class="table-responsive">
                <table id="adminUsersTable" class="table admin-table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->gender ?: '-' }}</td>
                            <td>{{ $user->age ?: '-' }}</td>
                            <td>{{ optional($user->created_at)->format('Y-m-d') }}</td>
                            <td>
                                <div class="support-row-actions">
                                    <button class="mini-btn" type="button" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">Edit</button>
                                    <button class="mini-btn danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $('#adminUsersTable').DataTable({
            pageLength: 10,
            order: [[4, 'desc']]
        });
    </script>
@endsection

@push('modal')
    @foreach ($users as $user)
        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content support-modal">
                    <div class="modal-body">
                        <div class="support-modal-head">
                            <h5>Edit User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="/admin/users/{{ $user->id }}">
                            @csrf
                            @method('PUT')
                            <div class="custom-input-group"><input class="custom-input" type="text" name="name" value="{{ $user->name }}" placeholder="Full name"></div>
                            <div class="custom-input-group"><input class="custom-input" type="email" name="email" value="{{ $user->email }}" placeholder="Email"></div>
                            <div class="support-modal-actions">
                                <button type="button" class="btn btn-soft-neutral" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-main" type="submit">Save User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content support-modal support-modal-small">
                    <div class="modal-body text-center">
                        <div class="support-delete-icon"><i class="bi bi-trash3"></i></div>
                        <h5>Delete user?</h5>
                        <p>{{ $user->name }} will be removed from the system.</p>
                        <form method="POST" action="/admin/users/{{ $user->id }}">
                            @csrf
                            @method('DELETE')
                            <div class="support-modal-actions">
                                <button type="button" class="btn btn-soft-neutral" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endpush
