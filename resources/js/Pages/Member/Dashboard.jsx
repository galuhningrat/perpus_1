// resources/js/Pages/Member/Dashboard.jsx
import MemberLayout from '@/Layouts/MemberLayout';
import Card from '@/Components/UI/Card';
import Badge from '@/Components/UI/Badge';
import { Link } from '@inertiajs/react';

export default function Dashboard({ statistics, borrowings, bookings, fines, recommendedBooks }) {
    return (
        <MemberLayout>
            <div className="mb-6">
                <h1 className="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p className="text-gray-600">Selamat datang di perpustakaan STTI</p>
            </div>

            {/* Statistics */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <Card>
                    <div className="text-center">
                        <p className="text-gray-600 text-sm">Sedang Dipinjam</p>
                        <p className="text-3xl font-bold text-blue-600 mt-2">{statistics.active_borrowings}</p>
                    </div>
                </Card>
                <Card>
                    <div className="text-center">
                        <p className="text-gray-600 text-sm">Terlambat</p>
                        <p className="text-3xl font-bold text-red-600 mt-2">{statistics.overdue_borrowings}</p>
                    </div>
                </Card>
                <Card>
                    <div className="text-center">
                        <p className="text-gray-600 text-sm">Booking Aktif</p>
                        <p className="text-3xl font-bold text-yellow-600 mt-2">{statistics.active_bookings}</p>
                    </div>
                </Card>
                <Card>
                    <div className="text-center">
                        <p className="text-gray-600 text-sm">Total Denda</p>
                        <p className="text-3xl font-bold text-orange-600 mt-2">
                            Rp {statistics.total_unpaid_fines?.toLocaleString('id-ID') || 0}
                        </p>
                    </div>
                </Card>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {/* Active Borrowings */}
                <Card title="Buku Yang Dipinjam">
                    {borrowings.length > 0 ? (
                        <div className="space-y-3">
                            {borrowings.map((borrowing) => (
                                <div key={borrowing.id} className="p-3 border border-gray-200 rounded-lg">
                                    <div className="flex justify-between items-start">
                                        <div>
                                            <p className="font-medium">{borrowing.book.title}</p>
                                            <p className="text-sm text-gray-600">{borrowing.book.author}</p>
                                            <p className="text-xs text-gray-500 mt-1">
                                                Jatuh tempo: {new Date(borrowing.due_date).toLocaleDateString('id-ID')}
                                            </p>
                                        </div>
                                        <Badge variant={borrowing.status === 'overdue' ? 'danger' : 'info'}>
                                            {borrowing.status === 'overdue' ? 'Terlambat' : 'Aktif'}
                                        </Badge>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="text-center text-gray-500 py-8">Tidak ada buku yang sedang dipinjam</p>
                    )}
                    <Link
                        href="/member/borrow-history"
                        className="block text-center mt-4 text-sm text-blue-600 hover:text-blue-700"
                    >
                        Lihat Riwayat →
                    </Link>
                </Card>

                {/* Recommended Books */}
                <Card title="Rekomendasi Buku">
                    <div className="grid grid-cols-2 gap-4">
                        {recommendedBooks.slice(0, 4).map((book) => (
                            <Link
                                key={book.id}
                                href={`/member/books/${book.id}`}
                                className="border border-gray-200 rounded-lg p-3 hover:shadow-md transition-shadow"
                            >
                                <div className="aspect-[3/4] bg-gray-200 rounded mb-2 flex items-center justify-center">
                                    <span className="text-4xl">📖</span>
                                </div>
                                <p className="font-medium text-sm line-clamp-2">{book.title}</p>
                                <p className="text-xs text-gray-600 mt-1">{book.author}</p>
                            </Link>
                        ))}
                    </div>
                    <Link
                        href="/member/books"
                        className="block text-center mt-4 text-sm text-blue-600 hover:text-blue-700"
                    >
                        Cari Lebih Banyak →
                    </Link>
                </Card>
            </div>
        </MemberLayout>
    );
}
