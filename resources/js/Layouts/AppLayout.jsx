import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function AppLayout({ children }) {
    const { auth, flash } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Flash Messages */}
            {flash?.success && (
                <div className="fixed top-4 right-4 z-50 max-w-md">
                    <div className="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-lg">
                        <p>{flash.success}</p>
                    </div>
                </div>
            )}

            {flash?.error && (
                <div className="fixed top-4 right-4 z-50 max-w-md">
                    <div className="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-lg">
                        <p>{flash.error}</p>
                    </div>
                </div>
            )}

            {/* Navbar */}
            <nav className="bg-blue-600 text-white shadow-lg">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex items-center">
                            <Link href="/" className="text-xl font-bold">
                                📚 STTI Perpustakaan
                            </Link>
                        </div>

                        <div className="flex items-center gap-4">
                            {auth.user ? (
                                <>
                                    <span className="text-sm">{auth.user.name}</span>
                                    <Link
                                        href="/profile"
                                        className="px-3 py-2 rounded-md text-sm hover:bg-blue-700"
                                    >
                                        Profile
                                    </Link>
                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        className="px-3 py-2 rounded-md text-sm hover:bg-blue-700"
                                    >
                                        Logout
                                    </Link>
                                </>
                            ) : (
                                <>
                                    <Link href="/login" className="px-3 py-2 rounded-md text-sm hover:bg-blue-700">
                                        Login
                                    </Link>
                                    <Link href="/register" className="px-3 py-2 rounded-md text-sm hover:bg-blue-700">
                                        Register
                                    </Link>
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </nav>

            <main>{children}</main>
        </div>
    );
}
