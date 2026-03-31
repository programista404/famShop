@extends('layouts.admin', ['title' => 'Update Profile'])

@section('content')
    <div class="admin-screen">
        <div class="support-page-head mb-3">
            <div>
                <h4 class="mb-1">Update Profile</h4>
                <p class="muted-note mb-0">Edit your admin account details and profile image.</p>
            </div>
        </div>

        <div class="panel-card">
            <form method="POST" action="/admin/profile" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="custom-input-group mb-0">
                            <input class="custom-input" type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Full name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="custom-input-group mb-0">
                            <input class="custom-input" type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Email">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="custom-input-group mb-0">
                            <select class="custom-select" name="gender">
                                <option value="">Gender</option>
                                <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="custom-input-group mb-0">
                            <input class="custom-input" type="number" name="age" value="{{ old('age', $user->age) }}" placeholder="Age">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="custom-input-group mb-0">
                            <input class="custom-input" type="file" name="photo" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="support-modal-actions justify-content-start">
                    <button class="btn btn-main" type="submit">Save Profile</button>
                </div>
            </form>
        </div>
    </div>
@endsection
