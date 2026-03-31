@extends('layouts.app', ['title' => 'Support Center'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/profile" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <span class="badge-soft">Support Center</span>
        </div>

        <div class="content-block">
            <div class="hero-panel text-center mb-3">
                <div class="avatar-circle mx-auto d-flex align-items-center justify-content-center profile-security-avatar">
                    <i class="bi bi-headset"></i>
                </div>
                <h3 class="mt-3 mb-1">Help, feedback, and support</h3>
                <p class="muted-note mb-0">Choose the right channel to report issues, share ideas, or track your support requests.</p>
            </div>

            <div class="support-hub-grid">
                <a href="/support/feedback" class="support-hub-card text-decoration-none">
                    <div class="support-hub-icon feedback"><i class="bi bi-chat-square-heart"></i></div>
                    <h5>Feedback</h5>
                    <p>Send ratings, suggestions, and bug reports with quick stats.</p>
                    <strong>{{ $feedbackCount }} submitted</strong>
                </a>

                <a href="/support/tickets" class="support-hub-card text-decoration-none">
                    <div class="support-hub-icon tickets"><i class="bi bi-life-preserver"></i></div>
                    <h5>Support Tickets</h5>
                    <p>Create a ticket and review your previous support conversations.</p>
                    <strong>{{ $openTicketsCount }} open</strong>
                </a>
            </div>
        </div>
    </div>
@endsection
