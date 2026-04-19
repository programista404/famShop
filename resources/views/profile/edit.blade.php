@extends('layouts.app', ['title' => 'Edit Profile'])

@section('content')
    <div class="screen-shell screen-top">
        <div class="top-nav">
            <a href="/profile" class="text-decoration-none text-reset"><i class="bi bi-reply-fill"></i></a>
            <span class="badge-soft">Edit Profile</span>
        </div>

        <div class="content-block">
            <div class="hero-panel text-center mb-3">
                @if ($user->profile_photo)
                    <img src="{{ famshopUserPhoto($user->profile_photo) }}" alt="{{ $user->name }}" class="avatar-circle" style="width:110px;height:110px;">
                @else
                    <div class="avatar-circle mx-auto d-flex align-items-center justify-content-center" style="width:110px;height:110px;">
                        <i class="bi bi-person-fill" style="font-size:2rem;"></i>
                    </div>
                @endif
                <h3 class="mt-3 mb-1">{{ $user->name }}</h3>
                <p class="muted-note mb-0">Update your personal details and profile image.</p>
            </div>

            <div class="panel-card">
                <form method="POST" action="/profile" enctype="multipart/form-data">
                    @csrf
                    <div class="custom-input-group">
                        <input class="custom-input" type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Full name">
                    </div>
                    <div class="custom-input-group">
                        <input class="custom-input" type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Email address">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <select class="custom-select" name="gender">
                                <option value="">Gender</option>
                                <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <input class="custom-input" type="number" name="age" value="{{ old('age', $user->age) }}" placeholder="Age">
                        </div>
                    </div>
                    <div class="custom-input-group mt-3">
                        <input class="custom-input" type="file" name="photo" accept="image/*">
                    </div>
                    <button class="btn btn-main mt-2" type="submit">Save Profile</button>
                </form>
            </div>
        </div>
    </div>
@endsection
