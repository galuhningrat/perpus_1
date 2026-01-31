import AdminLayout from '@/Layouts/AdminLayout';
import Card from '@/Components/UI/Card';
import { Link } from '@inertiajs/react';

export default function Dashboard({ statistics, recentBorrowings, recentBookings, popularBooks }) {
    return (
        <AdminLayout>
            <div className="mb-6">
                <h1 className="text-2xl font-bold text-gray-900">Dashboard Admin</h1>
                <p className="text-gray-600">Selamat datang di panel administrasi</p>
            </div>

            {/* Statistics Cards */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <Card className="bg-gradient-to-br from-blue-500 to-blue-600 text-white">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-blue-100 text-sm">Total Buku</p>
                            <p className="text-3xl font-bold mt-2">{statistics.total_books}</p>
                        </div>
                        <div className="text-5xl opacity-20">📚</div>
                    </div>
                </Card>

                <Card className="bg-gradient-to-br from-green-500 to-green-600 text-white">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-green-100 text-sm">Total Anggota</p>
                            <p className="text-3xl font-bold mt-2">{statistics.total_members}</p>
                        </div>
                        <div className="text-5xl opacity-20">👥</div>
                    </div>
                </Card>

                <Card className="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-yellow-100 text-sm">Peminjaman Aktif</p>
                            <p className="text-3xl font-bold mt-2">{statistics.active_borrowings}</p>
                        </div>
                        <div className="text-5xl opacity-20">📋</div>
                    </div>
                </Card>

                <Card className="bg-gradient-to-br from-red-500 to-red-600 text-white">
                    <div className="flex items-center justify-between">
                        <div>
                            <p className="text-red-100 text-sm">Terlambat</p>
                            <p className="text-3xl font-bold mt-2">{statistics.overdue_borrowings}</p>
                        </div>
                        <div className="text-5xl opacity-20">⚠️</div>
                    </div>
                </Card>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Recent Borrowings */}
                <Card title="Peminjaman Terbaru">
                    <div className="space-y-3">
                        {recentBorrowings.slice(0, 5).map((borrowing) => (
                            <Link
                                key={borrowing.id}
                                href={`/admin/borrowings/${borrowing.id}`}
                                className="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg"
                            >
                                <div>
                                    <p className="font-medium text-sm">{borrowing.book.title}</p>
                                    <p className="text-xs text-gray-600">{borrowing.user.name}</p>
                                </div>
                                <span className="text-xs text-gray-500">
                                    {new Date(borrowing.borrow_date).toLocaleDateString('id-ID')}
                                </span>
                            </Link>
                        ))}
                    </div>
                    <Link
                        href="/admin/borrowings"
                        className="block text-center mt-4 text-sm text-blue-600 hover:text-blue-700"
                    >
                        Lihat Semua →
                    </Link>
                </Card>

                {/* Popular Books */}
                <Card title="Buku Populer">
                    <div className="space-y-3">
                        {popularBooks.slice(0, 5).map((book) => (
                            <Link
                                key={book.id}
                                href={`/admin/books/${book.id}`}
                                className="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg"
                            >
                                <div>
                                    <p className="font-medium text-sm">{book.title}</p>
                                    <p className="text-xs text-gray-600">{book.author}</p>
                                </div>
                                <span className="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                    {book.borrowings_count}x
                                </span>
                            </Link>
                        ))}
                    </div>
                    <Link
                        href="/admin/books"
                        className="block text-center mt-4 text-sm text-blue-600 hover:text-blue-700"
                    >
                        Lihat Semua →
                    </Link>
                </Card>
            </div>
        </AdminLayout>
    );
}
