<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        } else {
            // Default: show members only
            $query->where('role', 'member');
        }

        // Filter by prodi
        if ($request->filled('prodi')) {
            $query->where('prodi', $request->prodi);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'ILIKE', "%{$request->search}%")
                    ->orWhere('email', 'ILIKE', "%{$request->search}%")
                    ->orWhere('nim_nidn', 'ILIKE', "%{$request->search}%");
            });
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        $users = $query->orderBy('name')
            ->paginate($request->per_page ?? 15);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['role', 'prodi', 'search', 'is_active']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nim_nidn' => 'required|string|max:20|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:super_admin,pustakawan,member',
            'prodi' => 'nullable|in:Informatika,Elektro,Mesin,Umum',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return redirect()
            ->route('admin.users.show', $user->id)
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load([
            'borrowings' => function ($query) {
                $query->with('book')->latest()->limit(10);
            },
            'bookings' => function ($query) {
                $query->with('book')->latest()->limit(10);
            },
        ]);

        $statistics = [
            'total_borrowings' => $user->borrowings()->count(),
            'active_borrowings' => $user->activeBorrowings()->count(),
            'overdue_borrowings' => $user->borrowings()->overdue()->count(),
            'total_bookings' => $user->bookings()->count(),
            'active_bookings' => $user->activeBookings()->count(),
            'total_unpaid_fines' => $user->getTotalUnpaidFines(),
        ];

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nim_nidn' => 'required|string|max:20|unique:users,nim_nidn,' . $user->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:super_admin,pustakawan,member',
            'prodi' => 'nullable|in:Informatika,Elektro,Mesin,Umum',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.show', $user->id)
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting user with active borrowings
        if ($user->activeBorrowings()->count() > 0) {
            return back()->with('error', 'Cannot delete user with active borrowings');
        }

        // Prevent deleting user with unpaid fines
        if ($user->hasUnpaidFines()) {
            return back()->with('error', 'Cannot delete user with unpaid fines');
        }

        $user->update(['is_active' => false]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deactivated successfully');
    }

    /**
     * Restore deactivated user
     */
    public function restore($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => true]);

        return redirect()
            ->route('admin.users.show', $user->id)
            ->with('success', 'User restored successfully');
    }
}
