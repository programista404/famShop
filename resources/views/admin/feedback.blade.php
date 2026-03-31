@extends('layouts.admin', ['title' => 'Admin Feedback'])

@section('content')
    <div class="admin-screen">
        <div class="support-page-head mb-3">
            <div>
                <h4 class="mb-1">Feedback</h4>
                <p class="muted-note mb-0">Review customer feedback and remove low-value submissions when needed.</p>
            </div>
        </div>

        <div class="panel-card admin-table-card">
            <div class="table-responsive">
                <table id="adminFeedbackTable" class="table admin-table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>User</th>
                        <th>Type</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($feedbackItems as $feedback)
                        <tr>
                            <td>{{ $feedback->user->name ?? 'Unknown user' }}</td>
                            <td>{{ ucfirst($feedback->type) }}</td>
                            <td>{{ $feedback->rating ? $feedback->rating . '/5' : '-' }}</td>
                            <td>{{ $feedback->comment ?: '-' }}</td>
                            <td>{{ optional($feedback->created_at)->format('Y-m-d') }}</td>
                            <td>
                                <button class="mini-btn danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteFeedbackModal{{ $feedback->id }}">Delete</button>
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
        $('#adminFeedbackTable').DataTable({
            pageLength: 10,
            order: [[4, 'desc']]
        });
    </script>
@endsection

@push('modal')
    @foreach ($feedbackItems as $feedback)
        <div class="modal fade" id="deleteFeedbackModal{{ $feedback->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content support-modal support-modal-small">
                    <div class="modal-body text-center">
                        <div class="support-delete-icon"><i class="bi bi-trash3"></i></div>
                        <h5>Delete feedback?</h5>
                        <p>This feedback item will be removed.</p>
                        <form method="POST" action="/admin/feedback/{{ $feedback->id }}">
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
