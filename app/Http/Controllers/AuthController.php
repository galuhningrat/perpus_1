<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check if user is active
            if (!$user->is_active) {
                Auth::logout();
                return back()->with('error', 'Your account is inactive. Please contact administrator.');
            }

            // Redirect based on role
            if ($user->hasAdminAccess()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('member.dashboard'));
        }

        return back()->with('error', 'Invalid credentials');
    }

    /**
     * Show register form
     */
    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nim_nidn' => 'required|string|max:20|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'prodi' => 'required|in:Informatika,Elektro,Mesin,Umum',
            'phone' => 'nullable|string|max:20',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'member';
        $validated['is_active'] = true;

        $user = \App\Models\User::create($validated);

        Auth::login($user);

        return redirect()->route('member.dashboard');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show profile page
     */
    public function profile()
    {
        $user = Auth::user();
        $user->load([
            'borrowings' => function ($query) {
                $query->active()->with('book.category');
            },
            'bookings' => function ($query) {
                $query->pending()->with('book.category');
            },
        ]);

        $statistics = [
            'total_borrowings' => $user->borrowings()->count(),
            'active_borrowings' => $user->activeBorrowings()->count(),
            'total_fines' => $user->getTotalUnpaidFines(),
        ];

        return Inertia::render('Profile', [
            'user' => $user,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'current_password' => 'required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Verify current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Current password is incorrect');
            }
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        unset($validated['current_password']);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully');
    }
}
