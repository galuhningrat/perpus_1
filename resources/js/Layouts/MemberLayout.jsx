import { Link, usePage } from '@inertiajs/react';

export default function MemberLayout({ children }) {
    const { auth, notifications } = usePage().props;

    const navigation = [
        { name: 'Dashboard', href: '/member/dashboard', icon: '🏠' },
        { name: 'Cari Buku', href: '/member/books', icon: '🔍' },
        { name: 'Booking Saya', href: '/member/bookings', icon: '📅' },
        { name: 'Riwayat Pinjam', href: '/member/borrow-history', icon: '📖' },
        { name: 'Denda', href: '/member/fines', icon: '💰' },
    ];

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Navbar */}
            <nav className="bg-blue-600 text-white shadow-lg">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16">
                        <div className="flex items-center gap-8">
                            <Link href="/member/dashboard" className="text-xl font-bold">
                                📚 STTI Perpustakaan
                            </Link>

                            <div className="hidden md:flex items-center gap-4">
                                {navigation.map((item) => (
                                    <Link
                                        key={item.name}
                                        href={item.href}
                                        className="flex items-center gap-2 px-3 py-2 rounded-md text-sm hover:bg-blue-700"
                                    >
                                        <span>{item.icon}</span>
                                        <span>{item.name}</span>
                                    </Link>
                                ))}
                            </div>
                        </div>

                        <div className="flex items-center gap-4">
                            {/* Notifications Badge */}
                            {notifications && (
                                <div className="flex items-center gap-2">
                                    {notifications.overdue_borrowings > 0 && (
                                        <span className="px-2 py-1 bg-red-500 text-white text-xs rounded-full">
                                            {notifications.overdue_borrowings} Terlambat
                                        </span>
                                    )}
                                    {notifications.active_bookings > 0 && (
                                        <span className="px-2 py-1 bg-yellow-500 text-white text-xs rounded-full">
                                            {notifications.active_bookings} Booking
                                        </span>
                                    )}
                                </div>
                            )}

                            <div className="flex items-center gap-2">
                                <span className="text-sm hidden md:block">{auth.user.name}</span>
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
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            {/* Alerts */}
            {notifications?.unpaid_fines && (
                <div className="bg-yellow-50 border-b border-yellow-200">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                        <p className="text-yellow-800 text-sm">
                            ⚠️ Anda memiliki denda yang belum dibayar sebesar Rp{' '}
                            {notifications.total_unpaid_fines?.toLocaleString('id-ID')}
                        </p>
                    </div>
                </div>
            )}

            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">{children}</main>
        </div>
    );
}
