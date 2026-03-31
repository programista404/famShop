@extends('layouts.admin', ['title' => 'Admin Support'])

@section('content')
    <div class="admin-screen">
        <div class="support-page-head mb-3">
            <div>
                <h4 class="mb-1">Support Tickets</h4>
                <p class="muted-note mb-0">Reply to tickets, update status, and remove resolved or invalid requests.</p>
            </div>
        </div>

        <div class="panel-card admin-table-card">
            <div class="table-responsive">
                <table id="adminSupportTable" class="table admin-table align-middle mb-0">
                    <thead>
                    <tr>
                        <th>User</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->user->name ?? 'Unknown user' }}</td>
                            <td>{{ $ticket->message }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</td>
                            <td>{{ optional($ticket->ticket_date)->format('Y-m-d') }}</td>
                            <td>
                                <div class="support-row-actions">
                                    <button class="mini-btn" type="button" data-bs-toggle="modal" data-bs-target="#editTicketModal{{ $ticket->id }}">Reply</button>
                                    <button class="mini-btn danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteTicketModal{{ $ticket->id }}">Delete</button>
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
        $('#adminSupportTable').DataTable({
            pageLength: 10,
            order: [[3, 'desc']]
        });
    </script>
@endsection

@push('modal')
    @foreach ($tickets as $ticket)
        <div class="modal fade" id="editTicketModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content support-modal">
                    <div class="modal-body">
                        <div class="support-modal-head">
                            <h5>Reply to Ticket</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="/admin/support/{{ $ticket->id }}">
                            @csrf
                            @method('PUT')
                            <div class="support-modal-block">
                                <span>User message</span>
                                <p>{{ $ticket->message }}</p>
                            </div>
                            <div class="custom-input-group">
                                <select class="custom-select" name="status">
                                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In progress</option>
                                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                            @if ($hasReplyColumn)
                                <div class="custom-input-group">
                                    <textarea class="custom-textarea" name="reply_message" placeholder="Write a reply">{{ $ticket->reply_message }}</textarea>
                                </div>
                            @else
                                <div class="support-modal-block">
                                    <span>Reply</span>
                                    <p>Reply storage is not available yet. Add the reply_message column first.</p>
                                </div>
                            @endif
                            <div class="support-modal-actions">
                                <button type="button" class="btn btn-soft-neutral" data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-main" type="submit">Save Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteTicketModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content support-modal support-modal-small">
                    <div class="modal-body text-center">
                        <div class="support-delete-icon"><i class="bi bi-trash3"></i></div>
                        <h5>Delete support ticket?</h5>
                        <p>This support ticket will be removed.</p>
                        <form method="POST" action="/admin/support/{{ $ticket->id }}">
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
