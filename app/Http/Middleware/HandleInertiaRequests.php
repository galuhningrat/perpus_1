<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            // Auth user data
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'nim_nidn' => $request->user()->nim_nidn,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                    'prodi' => $request->user()->prodi,
                    'phone' => $request->user()->phone,
                    'is_active' => $request->user()->is_active,
                    'has_admin_access' => $request->user()->hasAdminAccess(),
                ] : null,
            ],

            // Flash messages
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],

            // App settings
            'app' => [
                'name' => config('app.name'),
                'url' => config('app.url'),
            ],

            // Library settings
            'library' => [
                'borrow_days' => 7,
                'fine_per_day' => 1000,
                'max_borrowings' => 5,
                'max_bookings' => 3,
                'booking_expiry_hours' => 24,
            ],

            // Notifications count (if user is authenticated)
            'notifications' => $request->user() ? [
                'active_borrowings' => $request->user()->activeBorrowings()->count(),
                'overdue_borrowings' => $request->user()->borrowings()->overdue()->count(),
                'active_bookings' => $request->user()->activeBookings()->count(),
                'unpaid_fines' => $request->user()->hasUnpaidFines(),
                'total_unpaid_fines' => $request->user()->getTotalUnpaidFines(),
            ] : null,
        ]);
    }
}
