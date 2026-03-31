@extends('layouts.app', ['title' => 'Support Tickets'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/support" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <span class="badge-soft">Support Tickets</span>
        </div>

        <div class="content-block">
            <div class="support-page-head mb-3">
                <div>
                    <h4 class="mb-1">Support Tickets</h4>
                    <p class="muted-note mb-0">Open a new ticket and track your previous requests.</p>
                </div>
                <button class="support-ticket-cta" type="button" data-bs-toggle="modal" data-bs-target="#openTicketModal">
                    Add Ticket
                </button>
            </div>

            <div class="panel-card mb-3">
                <div class="section-header mt-0">
                    <h5 class="mb-0">Ticket stats</h5>
                </div>

                <div class="support-stats-row">
                    <div class="support-stat-card">
                        <div class="support-stat-icon blue-bg"><i class="bi bi-collection"></i></div>
                        <span>Total</span>
                        <strong>{{ $ticketStats['total'] }}</strong>
                    </div>
                    <div class="support-stat-card">
                        <div class="support-stat-icon sand-bg"><i class="bi bi-life-preserver"></i></div>
                        <span>Open</span>
                        <strong>{{ $ticketStats['open'] }}</strong>
                    </div>
                    <div class="support-stat-card">
                        <div class="support-stat-icon green-bg"><i class="bi bi-check2-circle"></i></div>
                        <span>Resolved</span>
                        <strong>{{ $ticketStats['resolved'] }}</strong>
                    </div>
                </div>
            </div>

            <div class="section-header">
                <h5 class="mb-0">Previous tickets</h5>
            </div>
            <div class="stack-list">
                @forelse ($tickets as $ticket)
                    <div class="history-card support-history-card">
                        <div class="support-history-icon ticket {{ $ticket->status === 'resolved' ? 'resolved' : 'open' }}">
                            <i class="bi {{ $ticket->status === 'resolved' ? 'bi-check2-circle' : 'bi-life-preserver' }}"></i>
                        </div>
                        <div class="history-details">
                            <h6>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</h6>
                            <p>{{ $ticket->message }}</p>
                        </div>
                        <div class="support-history-side">
                            <small>{{ optional($ticket->ticket_date)->format('Y-m-d') }}</small>
                            <div class="support-row-actions">
                                <button class="mini-btn" type="button" data-bs-toggle="modal" data-bs-target="#ticketViewModal{{ $ticket->id }}">View</button>
                                <button class="mini-btn danger" type="button" data-bs-toggle="modal" data-bs-target="#ticketDeleteModal{{ $ticket->id }}">Delete</button>
                            </div>
                        </div>
                    </div>

                 @push('modal')
                        <div class="modal fade" id="ticketViewModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content support-modal">
                                    <div class="modal-body">
                                        <div class="support-modal-head">
                                            <h5>Ticket Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="support-modal-block">
                                            <span>Status</span>
                                            <strong>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</strong>
                                        </div>
                                        <div class="support-modal-block">
                                            <span>Submitted</span>
                                            <strong>{{ optional($ticket->ticket_date)->format('Y-m-d h:i A') }}</strong>
                                        </div>
                                        <div class="support-modal-block">
                                            <span>Your message</span>
                                            <p>{{ $ticket->message }}</p>
                                        </div>
                                        <div class="support-modal-block">
                                            <span>Response</span>
                                            <p>{{ $ticket->reply_message ?? "No response yet." }} </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="ticketDeleteModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content support-modal support-modal-small">
                                    <div class="modal-body text-center">
                                        <div class="support-delete-icon"><i class="bi bi-trash3"></i></div>
                                        <h5>Delete ticket?</h5>
                                        <p>This action will remove the selected support ticket.</p>
                                        <form method="POST" action="/support/{{ $ticket->id }}">
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

                    @endpush                @empty
                    <div class="history-card">
                        <div class="history-details">
                            <h6>No support tickets</h6>
                            <p>Your submitted tickets will appear here.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@push('modal')
    <div class="modal fade" id="openTicketModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content support-modal">
                <div class="modal-body">
                    <div class="support-modal-head">
                        <h5>Open New Ticket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="/support">
                        @csrf
                        <div class="custom-input-group">
                            <textarea class="custom-textarea" name="message" placeholder="Describe your issue or question"></textarea>
                        </div>
                        <div class="support-modal-actions">
                            <button type="button" class="btn btn-soft-neutral" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-main" type="submit">Send Ticket</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endpush
@endsection
