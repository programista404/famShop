@extends('layouts.app', ['title' => 'Create Account'])

@section('content')
    <div class="screen-shell">
        <div class="top-nav">
            <a href="/" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
        </div>
        <h2 class="page-title">Sign Up</h2>
        <p class="tagline">Create your FamShop account and start healthier shopping.</p>
        <form method="POST" action="/register" class="form-container">
            @csrf
            <div class="custom-input-group">
                <input class="custom-input" type="text" name="name" value="{{ old('name') }}" placeholder="Full name">
            </div>
            <div class="custom-input-group">
                <input class="custom-input" type="email" name="email" value="{{ old('email') }}" placeholder="Email address">
            </div>
            <div class="custom-input-group">
                <input class="custom-input" type="password" name="password" placeholder="Password">
            </div>
            <div class="custom-input-group">
                <input class="custom-input" type="password" name="password_confirmation" placeholder="Confirm password">
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <select class="custom-select" name="gender">
                        <option value="">Gender</option>
                        <option value="female">Female</option>
                        <option value="male">Male</option>
                    </select>
                </div>
                <div class="col-6">
                    <input class="custom-input" type="number" name="age" value="{{ old('age') }}" placeholder="Age">
                </div>
            </div>
            <button class="btn btn-main mt-4" type="submit">Create Account</button>
            <p class="text-center mt-4">Already have an account? <a href="/login">Sign In</a></p>
        </form>
    </div>
@endsection
