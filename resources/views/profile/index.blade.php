@extends('layouts.app', ['title' => 'My Profile'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/dashboard" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <form id="customerLogoutForm" method="POST" action="/logout">
                @csrf
                <button class="mini-btn danger" type="button" data-bs-toggle="modal" data-bs-target="#customerLogoutModal">Logout</button>
            </form>
        </div>

        <div class="content-block">
            <div class="hero-panel text-center mb-3">
                @if ($user->profile_photo)
                    <img src="{{ $user->profile_photo }}" alt="{{ $user->name }}" class="avatar-circle" style="width:110px;height:110px;">
                @else
                    <div class="avatar-circle mx-auto d-flex align-items-center justify-content-center" style="width:110px;height:110px;">
                        <i class="bi bi-person-fill" style="font-size:2rem;"></i>
                    </div>
                @endif
                <h3 class="mt-3 mb-1">{{ $user->name }}</h3>
                <p class="muted-note mb-1">{{ $user->email }}</p>
                <p class="muted-note mb-0">Manage your account details, security, and family tools.</p>
            </div>

            <div class="settings-list mb-3">
                @if ($user->isAdmin())
                    <a href="/admin" class="settings-item text-decoration-none">
                        <div class="settings-icon sand-bg"><i class="bi bi-speedometer2"></i></div>
                        <div class="settings-info">
                            <h6>Admin Dashboard</h6>
                            <p>Manage products, users, and admin activity.</p>
                        </div>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                @endif

                <a href="/profile/edit" class="settings-item text-decoration-none">
                    <div class="settings-icon blue-bg"><i class="bi bi-pencil-square"></i></div>
                    <div class="settings-info">
                        <h6>Edit Profile</h6>
                        <p>Update your name, email, photo, and personal details.</p>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="/profile/password" class="settings-item text-decoration-none">
                    <div class="settings-icon red-bg"><i class="bi bi-shield-lock"></i></div>
                    <div class="settings-info">
                        <h6>Update Password</h6>
                        <p>Change your password and keep your account secure.</p>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="/family" class="settings-item text-decoration-none">
                    <div class="settings-icon green-bg"><i class="bi bi-people-fill"></i></div>
                    <div class="settings-info">
                        <h6>Manage Family</h6>
                        <p>Add members, allergy profiles, and budgets.</p>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="/list" class="settings-item text-decoration-none">
                    <div class="settings-icon blue-bg"><i class="bi bi-list-check"></i></div>
                    <div class="settings-info">
                        <h6>Shopping List</h6>
                        <p>Track items for the active family member.</p>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>

                <a href="/support" class="settings-item text-decoration-none">
                    <div class="settings-icon sand-bg"><i class="bi bi-headset"></i></div>
                    <div class="settings-info">
                        <h6>Support & Feedback</h6>
                        <p>Send suggestions, ratings, or support requests.</p>
                    </div>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('modal')
    <div class="modal fade" id="customerLogoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content logout-modal-card">
                <div class="modal-body logout-modal-body">
                    <div class="logout-modal-icon"><i class="bi bi-box-arrow-right"></i></div>
                    <h5 class="logout-modal-title">Logout?</h5>
                    <p class="logout-modal-text">You will need to sign in again to continue using FamShop.</p>
                </div>
                <div class="logout-modal-actions">
                    <button type="button" class="logout-cancel-btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="customerLogoutForm" class="logout-confirm-btn">Confirm Logout</button>
                </div>
            </div>
        </div>
    </div>
@endpush
