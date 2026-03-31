@extends('layouts.app', ['title' => 'Feedback'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/support" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <span class="badge-soft">Feedback</span>
        </div>

        <div class="content-block">
            <div class="panel-card mb-3">
                <div class="section-header mt-0">
                    <h5 class="mb-0">Feedback overview</h5>
                </div>

                <div class="support-stats-row">
                    <div class="support-stat-card">
                        <div class="support-stat-icon blue-bg"><i class="bi bi-chat-left-text"></i></div>
                        <span>Total</span>
                        <strong>{{ $feedbackStats['total'] }}</strong>
                    </div>
                    <div class="support-stat-card">
                        <div class="support-stat-icon green-bg"><i class="bi bi-star-fill"></i></div>
                        <span>Avg Rating</span>
                        <strong>{{ $feedbackStats['average_rating'] ?? '-' }}</strong>
                    </div>
                    <div class="support-stat-card">
                        <div class="support-stat-icon red-bg"><i class="bi bi-bug"></i></div>
                        <span>Bug Reports</span>
                        <strong>{{ $feedbackStats['bugs'] }}</strong>
                    </div>
                    <div class="support-stat-card">
                        <div class="support-stat-icon sand-bg"><i class="bi bi-lightbulb"></i></div>
                        <span>Suggestions</span>
                        <strong>{{ $feedbackStats['suggestions'] }}</strong>
                    </div>
                </div>
            </div>

            <div class="panel-card mb-3">
                <div class="section-header mt-0">
                    <h5 class="mb-0">Share feedback</h5>
                </div>

                <form method="POST" action="/feedback">
                    @csrf
                    <div class="custom-input-group">
                        <select class="custom-select" name="type">
                            <option value="rating">Rating</option>
                            <option value="suggestion">Suggestion</option>
                            <option value="bug">Bug report</option>
                        </select>
                    </div>
                    <div class="custom-input-group">
                        <input class="custom-input" type="number" name="rating" min="1" max="5" placeholder="Rating from 1 to 5">
                    </div>
                    <div class="custom-input-group">
                        <textarea class="custom-textarea" name="comment" placeholder="Write your feedback"></textarea>
                    </div>
                    <button class="btn btn-main" type="submit">Send Feedback</button>
                </form>
            </div>

            <div class="section-header">
                <h5 class="mb-0">Recent feedback</h5>
            </div>
            <div class="stack-list">
                @forelse ($feedbackItems as $feedback)
                    <div class="history-card support-history-card">
                        <div class="support-history-icon feedback">
                            <i class="bi {{ $feedback->type === 'bug' ? 'bi-bug' : ($feedback->type === 'suggestion' ? 'bi-lightbulb' : 'bi-star-fill') }}"></i>
                        </div>
                        <div class="history-details">
                            <h6>{{ ucfirst($feedback->type) }}</h6>
                            <p>{{ $feedback->comment ?: 'No comment provided.' }}</p>
                        </div>
                        <div class="support-history-side">
                            <span class="history-status-badge is-safe">{{ $feedback->rating ? $feedback->rating . '/5' : 'Saved' }}</span>
                            <div class="support-row-actions">
                                <button class="mini-btn" type="button" data-bs-toggle="modal" data-bs-target="#feedbackViewModal{{ $feedback->id }}">View</button>
                                <button class="mini-btn danger" type="button" data-bs-toggle="modal" data-bs-target="#feedbackDeleteModal{{ $feedback->id }}">Delete</button>
                            </div>
                        </div>
                    </div>

                @push('modal')
                        <div class="modal fade" id="feedbackViewModal{{ $feedback->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content support-modal">
                                    <div class="modal-body">
                                        <div class="support-modal-head">
                                            <h5>Feedback Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="support-modal-block">
                                            <span>Type</span>
                                            <strong>{{ ucfirst($feedback->type) }}</strong>
                                        </div>
                                        <div class="support-modal-block">
                                            <span>Rating</span>
                                            <strong>{{ $feedback->rating ? $feedback->rating . '/5' : 'Not rated' }}</strong>
                                        </div>
                                        <div class="support-modal-block">
                                            <span>Comment</span>
                                            <p>{{ $feedback->comment ?: 'No comment provided.' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="feedbackDeleteModal{{ $feedback->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content support-modal support-modal-small">
                                    <div class="modal-body text-center">
                                        <div class="support-delete-icon"><i class="bi bi-trash3"></i></div>
                                        <h5>Delete feedback?</h5>
                                        <p>This action will remove the selected feedback item.</p>
                                        <form method="POST" action="/feedback/{{ $feedback->id }}">
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
                @endpush
                @empty
                    <div class="history-card">
                        <div class="history-details">
                            <h6>No feedback yet</h6>
                            <p>Your ratings and suggestions will appear here.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
