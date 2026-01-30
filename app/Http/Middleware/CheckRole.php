<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Tambahkan ini agar check() dan logout() tidak merah
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated menggunakan Facade Auth
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            Auth::logout(); // Sekarang logout() tidak akan merah lagi
            return redirect()->route('login')->with('error', 'Your account is inactive.');
        }

        // Check if user has required role
        if (!in_array($user->role, $roles)) {
            // Redirect berdasarkan role asli user
            if ($user->hasAdminAccess()) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'You do not have permission to access that page.');
            }

            return redirect()->route('member.dashboard')
                ->with('error', 'You do not have permission to access that page.');
        }

        return $next($request);
    }
}
