@extends('layouts.app', ['title' => 'Sign In'])

@section('content')
    <div class="screen-shell">
        <div class="top-nav">
            <a href="/" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
        </div>
        <h2 class="page-title">Sign In</h2>
        <p class="tagline">Welcome back. Continue your healthy shopping journey.</p>
        <form method="POST" action="/login" class="form-container">
            @csrf
            <div class="custom-input-group">
                <input class="custom-input" type="email" name="email" value="{{ old('email') }}" placeholder="Email address">
            </div>
            <div class="custom-input-group">
                <input class="custom-input" type="password" name="password" placeholder="Password">
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <button class="btn btn-main" type="submit">Login</button>
            <p class="text-center mt-4">Don’t have an account? <a href="/register">Create one</a></p>
        </form>
    </div>
@endsection
