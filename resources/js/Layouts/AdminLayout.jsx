import { Link, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function AdminLayout({ children }) {
    const { auth, notifications } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(true);

    const navigation = [
        { name: 'Dashboard', href: '/admin/dashboard', icon: '📊' },
        { name: 'Buku', href: '/admin/books', icon: '📚' },
        { name: 'Kategori', href: '/admin/categories', icon: '📑' },
        { name: 'Peminjaman', href: '/admin/borrowings', icon: '📋' },
        { name: 'Booking', href: '/admin/bookings', icon: '📅' },
        { name: 'Denda', href: '/admin/fines', icon: '💰' },
        { name: 'Anggota', href: '/admin/users', icon: '👥' },
        { name: 'Laporan', href: '/admin/reports', icon: '📈' },
    ];

    return (
        <div className="min-h-screen bg-gray-100">
            {/* Sidebar */}
            <div className={`fixed inset-y-0 left-0 z-50 w-64 bg-blue-900 text-white transform transition-transform duration-300 ${sidebarOpen ? 'translate-x-0' : '-translate-x-full'}`}>
                <div className="flex items-center justify-between h-16 px-4 bg-blue-950">
                    <h1 className="text-xl font-bold">Admin Panel</h1>
                    <button onClick={() => setSidebarOpen(!sidebarOpen)} className="lg:hidden">
                        ✕
                    </button>
                </div>

                <nav className="mt-8 px-4 space-y-2">
                    {navigation.map((item) => (
                        <Link
                            key={item.name}
                            href={item.href}
                            className="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-blue-800 transition-colors"
                        >
                            <span className="text-xl">{item.icon}</span>
                            <span>{item.name}</span>
                        </Link>
                    ))}
                </nav>

                <div className="absolute bottom-0 w-full p-4 border-t border-blue-800">
                    <div className="flex items-center gap-3">
                        <div className="w-10 h-10 rounded-full bg-blue-700 flex items-center justify-center">
                            {auth.user.name.charAt(0)}
                        </div>
                        <div>
                            <p className="font-medium text-sm">{auth.user.name}</p>
                            <p className="text-xs text-blue-300">{auth.user.role}</p>
                        </div>
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <div className={`transition-all duration-300 ${sidebarOpen ? 'lg:ml-64' : ''}`}>
                {/* Top Bar */}
                <div className="bg-white shadow-sm">
                    <div className="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                        <button
                            onClick={() => setSidebarOpen(!sidebarOpen)}
                            className="text-gray-600 hover:text-gray-900"
                        >
                            ☰
                        </button>

                        <div className="flex items-center gap-4">
                            {/* Notifications */}
                            {notifications?.overdue_borrowings > 0 && (
                                <Link
                                    href="/admin/borrowings-overdue"
                                    className="flex items-center gap-2 px-3 py-2 bg-red-100 text-red-700 rounded-lg text-sm"
                                >
                                    <span>⚠️</span>
                                    <span>{notifications.overdue_borrowings} Terlambat</span>
                                </Link>
                            )}

                            <Link
                                href="/profile"
                                className="px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg"
                            >
                                Profile
                            </Link>

                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                className="px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg"
                            >
                                Logout
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Page Content */}
                <main className="p-6">{children}</main>
            </div>
        </div>
    );
}
