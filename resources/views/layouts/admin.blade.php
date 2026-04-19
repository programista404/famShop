<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'FamShop Admin' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
</head>
<body class="admin-body">
    <div class="bg-decor decor-1"></div>
    <div class="bg-decor decor-2"></div>
    <div class="bg-decor decor-3"></div>
    <div class="bg-decor decor-4"></div>
    <div class="bg-decor decor-5"></div>
    <div class="bg-decor decor-6"></div>
    <div class="bottom-accent"></div>

    <div class="admin-shell">
        <aside class="admin-sidebar">
            <a href="/admin" class="admin-brand text-decoration-none">
                <div class="admin-brand-logo">
                    <img src="{{ asset('assets/img/start.png') }}" alt="FamShop logo">
                </div>
                <div>
                    <strong>FamShop</strong>
                    <span>Admin Panel</span>
                </div>
            </a>

            <nav class="admin-menu">
                <a href="/admin" class="admin-menu-link {{ request()->is('admin') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
                <a href="/admin/products" class="admin-menu-link {{ request()->is('admin/products*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Products</span>
                </a>
                <a href="/admin/users" class="admin-menu-link {{ request()->is('admin/users*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Users</span>
                </a>
                <a href="/admin/feedback" class="admin-menu-link {{ request()->is('admin/feedback*') ? 'active' : '' }}">
                    <i class="bi bi-chat-square-heart"></i>
                    <span>Feedback</span>
                </a>
                <a href="/admin/support" class="admin-menu-link {{ request()->is('admin/support*') ? 'active' : '' }}">
                    <i class="bi bi-life-preserver"></i>
                    <span>Support</span>
                </a>
            </nav>
        </aside>

        <main class="admin-main">
            <div class="admin-topbar">
                <div>
                    <p class="admin-kicker">Admin Panel</p>
                    <h4 class="admin-topbar-title mb-0">{{ $title ?? 'Admin' }}</h4>
                </div>
                <div class="admin-hero-actions">
                    <div class="dropdown">
                        <button class="admin-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if (auth()->user()->profile_photo)
                                <img src="{{ famshopUserPhoto(auth()->user()->profile_photo) }}" alt="{{ auth()->user()->name }}">
                            @else
                                <span class="admin-profile-fallback"><i class="bi bi-person-fill"></i></span>
                            @endif
                            <span>{{ auth()->user()->name }}</span>
                        </button>
                        <ul class="dropdown-menu admin-dropdown-menu">
                            <li><a class="dropdown-item" href="/admin/profile/edit"><i class="bi bi-pencil-square"></i> Update Profile</a></li>
                            <li><a class="dropdown-item" href="/admin/profile/password"><i class="bi bi-shield-lock"></i> Update Password</a></li>
                        </ul>
                    </div>
                    <form id="adminLogoutForm" method="POST" action="/logout">
                        @csrf
                        <button class="admin-logout-inline" type="button" data-bs-toggle="modal" data-bs-target="#adminLogoutModal">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>

            @if (session('success'))
                <div class="flash-box flash-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="flash-box flash-error">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="flash-box flash-error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <div class="modal fade" id="adminLogoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content logout-modal-card">
                <div class="modal-body logout-modal-body">
                    <div class="logout-modal-icon"><i class="bi bi-box-arrow-right"></i></div>
                    <h5 class="logout-modal-title">Logout?</h5>
                    <p class="logout-modal-text">Your admin session will be closed and you will return to the login page.</p>
                </div>
                <div class="logout-modal-actions">
                    <button type="button" class="logout-cancel-btn" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="adminLogoutForm" class="logout-confirm-btn">Confirm Logout</button>
                </div>
            </div>
        </div>
    </div>

    @stack('modal')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
