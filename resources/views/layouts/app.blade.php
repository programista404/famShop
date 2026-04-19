<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'FamShop Assistant' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
</head>
<body>
    <div class="bg-decor decor-1"></div>
    <div class="bg-decor decor-2"></div>
    <div class="bg-decor decor-3"></div>
    <div class="bg-decor decor-4"></div>
    <div class="bg-decor decor-5"></div>
    <div class="bg-decor decor-6"></div>
    <div class="bottom-accent"></div>

    <div class="app-shell">
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

        @auth
            @php $isAdminRoute = request()->is('admin') || request()->is('admin/*'); @endphp
            @if (! $isAdminRoute)
            <nav class="bottom-navbar">
                <a href="/dashboard" class="nav-btn {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i><span>Home</span>
                </a>
                <a href="/scan" class="nav-btn {{ request()->is('scan') || request()->is('scan/*') ? 'active' : '' }}">
                    <i class="bi bi-qr-code-scan"></i><span>Scan</span>
                </a>
                <a href="/cart" class="nav-btn {{ request()->is('cart*') ? 'active' : '' }}">
                    <span class="nav-icon-wrap">
                        <i class="bi bi-cart3"></i>
                        @if (($globalCartCount ?? 0) > 0)
                            <span class="cart-badge">{{ $globalCartCount }}</span>
                        @endif
                    </span>
                    <span>Cart</span>
                </a>
                <a href="/profile" class="nav-btn {{ request()->is('profile') || request()->is('family*') || request()->is('support') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i><span>Profile</span>
                </a>
            </nav>
            @endif
        @endauth
    </div>
@stack('modal')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
