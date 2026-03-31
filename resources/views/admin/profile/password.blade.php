@extends('layouts.admin', ['title' => 'Update Password'])

@section('content')
    <div class="admin-screen">
        <div class="support-page-head mb-3">
            <div>
                <h4 class="mb-1">Update Password</h4>
                <p class="muted-note mb-0">Change your admin password and keep the dashboard secure.</p>
            </div>
        </div>

        <div class="panel-card">
            <form method="POST" action="/admin/profile/password">
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
                <div class="support-modal-actions justify-content-start">
                    <button class="btn btn-main" type="submit">Save Password</button>
                </div>
            </form>
        </div>
    </div>
@endsection
