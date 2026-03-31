@extends('layouts.app', ['title' => 'FamShop Assistant'])

@section('content')
    <div class="screen-shell">
        <div class="logo-wrapper">
            <img src="{{ asset('assets/img/start.png') }}" alt="FamShop logo" class="landing-logo">
        </div>
        <h1 class="brand-name">FamShop Assistant</h1>
        <p class="tagline">Shop smart, stay safe, and manage every family member in one place.</p>
        <div class="form-container">
            <a href="/register" class="btn btn-main">Get Started</a>
            <a href="/login" class="btn btn-alt mt-3 d-block text-center text-decoration-none">Sign In</a>
        </div>
    </div>
@endsection
