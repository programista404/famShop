<?php

namespace App\Http\Controllers;

use App\Models\FamilyMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function landing()
    {
        return view('auth.landing');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'gender' => ['nullable', 'string', 'max:20'],
            'age' => ['nullable', 'integer', 'between:1,120'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'gender' => $validated['gender'] ?? null,
            'age' => $validated['age'] ?? null,
        ]);

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Account created successfully.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'The provided login details are incorrect.']);
        }

        $request->session()->regenerate();

        if ($request->user()->isAdmin()) {
            return redirect('/admin')->with('success', 'Signed in successfully.');
        }

        $activeMemberId = $request->session()->get('active_member_id');
        $hasValidActiveMember = $activeMemberId
            ? FamilyMember::where('user_id', $request->user()->id)->where('id', $activeMemberId)->exists()
            : false;

        if (! $hasValidActiveMember) {
            $firstMemberId = FamilyMember::where('user_id', $request->user()->id)
                ->orderBy('id')
                ->value('id');

            if ($firstMemberId) {
                $request->session()->put('active_member_id', $firstMemberId);
            }
        }

        return redirect('/dashboard')->with('success', 'Signed in successfully.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Signed out successfully.');
    }
}
