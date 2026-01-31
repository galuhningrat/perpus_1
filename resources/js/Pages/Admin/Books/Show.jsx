// resources/js/Pages/Member/Books/Show.jsx
import MemberLayout from '@/Layouts/MemberLayout';
import Card from '@/Components/UI/Card';
import Button from '@/Components/UI/Button';
import Badge from '@/Components/UI/Badge';
import { useForm } from '@inertiajs/react';

export default function Show({ book, canBook, canBookReasons }) {
    const { post, processing } = useForm();

    const handleBooking = () => {
        post(`/member/bookings`, { book_id: book.id });
    };

    return (
        <MemberLayout>
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="lg:col-span-1">
                    <Card>
                        <div className="aspect-[3/4] bg-gray-200 rounded-lg flex items-center justify-center mb-4">
                            {book.cover_image ? (
                                <img
                                    src={`/storage/${book.cover_image}`}
                                    alt={book.title}
                                    className="w-full h-full object-cover rounded-lg"
                                />
                            ) : (
                                <span className="text-8xl">📖</span>
                            )}
                        </div>
                        {book.qr_code && (
                            <img
                                src={`/storage/${book.qr_code}`}
                                alt="QR Code"
                                className="w-full max-w-[200px] mx-auto"
                            />
                        )}
                    </Card>
                </div>

                <div className="lg:col-span-2">
                    <Card>
                        <div className="mb-6">
                            <div className="flex items-start justify-between mb-4">
                                <div>
                                    <h1 className="text-3xl font-bold text-gray-900 mb-2">{book.title}</h1>
                                    <p className="text-lg text-gray-600">{book.author}</p>
                                </div>
                                <Badge variant={book.available_stock > 0 ? 'success' : 'danger'}>
                                    {book.available_stock > 0 ? 'Tersedia' : 'Tidak Tersedia'}
                                </Badge>
                            </div>

                            <div className="grid grid-cols-2 gap-4 mb-6">
                                <div>
                                    <p className="text-sm text-gray-600">Kategori</p>
                                    <p className="font-medium">{book.category.name}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-600">Penerbit</p>
                                    <p className="font-medium">{book.publisher || '-'}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-600">Tahun Terbit</p>
                                    <p className="font-medium">{book.publication_year || '-'}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-600">ISBN</p>
                                    <p className="font-medium">{book.isbn || '-'}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-600">Lokasi Rak</p>
                                    <p className="font-medium">{book.shelf_location}</p>
                                </div>
                                <div>
                                    <p className="text-sm text-gray-600">Stok Tersedia</p>
                                    <p className="font-medium">
                                        {book.available_stock} dari {book.stock}
                                    </p>
                                </div>
                            </div>

                            {book.description && (
                                <div className="mb-6">
                                    <h3 className="font-semibold mb-2">Deskripsi</h3>
                                    <p className="text-gray-600">{book.description}</p>
                                </div>
                            )}

                            <div className="flex gap-4">
                                {canBook ? (
                                    <Button onClick={handleBooking} disabled={processing}>
                                        {processing ? 'Memproses...' : '📅 Booking Buku'}
                                    </Button>
                                ) : (
                                    <div className="bg-red-50 border border-red-200 rounded-lg p-4 flex-1">
                                        <p className="text-sm font-medium text-red-800 mb-2">
                                            Tidak dapat melakukan booking:
                                        </p>
                                        <ul className="text-sm text-red-700 list-disc list-inside">
                                            {canBookReasons.map((reason, index) => (
                                                <li key={index}>{reason}</li>
                                            ))}
                                        </ul>
                                    </div>
                                )}
                            </div>
                        </div>
                    </Card>
                </div>
            </div>
        </MemberLayout>
    );
}
