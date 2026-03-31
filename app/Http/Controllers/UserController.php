<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('profile.index', [
            'user' => auth()->user(),
        ]);
    }

    public function edit()
    {
        return view('profile.edit', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email,' . auth()->id()],
            'gender' => ['nullable', 'string', 'max:20'],
            'age' => ['nullable', 'integer', 'between:1,120'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $user = auth()->user();

        if ($request->hasFile('photo')) {
            $validated['profile_photo'] = $request->file('photo')->store('avatars', 'public');
        }

        unset($validated['photo']);
        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function editPassword()
    {
        return view('profile.password', [
            'user' => auth()->user(),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.',
            ])->withInput();
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
