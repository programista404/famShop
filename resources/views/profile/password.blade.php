@extends('layouts.app', ['title' => 'Update Password'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/profile" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <span class="badge-soft">Security</span>
        </div>

        <div class="content-block">
            <div class="hero-panel text-center mb-3">
                <div class="avatar-circle mx-auto d-flex align-items-center justify-content-center profile-security-avatar">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <h3 class="mt-3 mb-1">Update Password</h3>
                <p class="muted-note mb-0">Use a strong password with at least 8 characters.</p>
            </div>

            <div class="panel-card">
                <form method="POST" action="/profile/password">
                    @csrf
                    <div class="custom-input-group">
                        <input class="custom-input" type="password" name="current_password" placeholder="Current password">
                    </div>
                    <div class="custom-input-group">
                        <input class="custom-input" type="password" name="password" placeholder="New password">
                    </div>
                    <div class="custom-input-group">
                        <input class="custom-input" type="password" name="password_confirmation" placeholder="Confirm new password">
                    </div>
                    <button class="btn btn-main" type="submit">Save Password</button>
                </form>
            </div>
        </div>
    </div>
@endsection
