// resources/js/Pages/Member/Books/Search.jsx
import MemberLayout from '@/Layouts/MemberLayout';
import Card from '@/Components/UI/Card';
import Badge from '@/Components/UI/Badge';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Search({ books, categories, filters }) {
    const [search, setSearch] = useState(filters.search || '');
    const [categoryId, setCategoryId] = useState(filters.category_id || '');

    const handleSearch = () => {
        router.get('/member/books', { search, category_id: categoryId }, { preserveState: true });
    };

    return (
        <MemberLayout>
            <div className="mb-6">
                <h1 className="text-2xl font-bold text-gray-900">Cari Buku</h1>
                <p className="text-gray-600">Temukan buku yang Anda inginkan</p>
            </div>

            <Card>
                <div className="mb-6 flex gap-4">
                    <input
                        type="text"
                        placeholder="Cari judul, pengarang, atau ISBN..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        className="flex-1 px-4 py-2 border border-gray-300 rounded-lg"
                        onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
                    />
                    <select
                        value={categoryId}
                        onChange={(e) => setCategoryId(e.target.value)}
                        className="px-4 py-2 border border-gray-300 rounded-lg"
                    >
                        <option value="">Semua Kategori</option>
                        {categories.map((cat) => (
                            <option key={cat.id} value={cat.id}>
                                {cat.name} ({cat.books_count})
                            </option>
                        ))}
                    </select>
                    <button
                        onClick={handleSearch}
                        className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Cari
                    </button>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {books.data.map((book) => (
                        <Link
                            key={book.id}
                            href={`/member/books/${book.id}`}
                            className="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow"
                        >
                            <div className="aspect-[3/4] bg-gray-200 rounded-lg mb-4 flex items-center justify-center">
                                {book.cover_image ? (
                                    <img
                                        src={`/storage/${book.cover_image}`}
                                        alt={book.title}
                                        className="w-full h-full object-cover rounded-lg"
                                    />
                                ) : (
                                    <span className="text-6xl">📖</span>
                                )}
                            </div>
                            <h3 className="font-semibold text-lg line-clamp-2 mb-2">{book.title}</h3>
                            <p className="text-sm text-gray-600 mb-2">{book.author}</p>
                            <div className="flex items-center justify-between">
                                <Badge variant={book.available_stock > 0 ? 'success' : 'danger'}>
                                    {book.available_stock > 0
                                        ? `${book.available_stock} tersedia`
                                        : 'Tidak tersedia'}
                                </Badge>
                                <span className="text-xs text-gray-500">{book.category.name}</span>
                            </div>
                        </Link>
                    ))}
                </div>

                {books.data.length === 0 && (
                    <div className="text-center py-12">
                        <p className="text-gray-500">Tidak ada buku yang ditemukan</p>
                    </div>
                )}

                {/* Pagination */}
                {books.last_page > 1 && (
                    <div className="mt-6 flex justify-center gap-2">
                        {books.links.map((link, index) => (
                            <Link
                                key={index}
                                href={link.url}
                                className={`px-4 py-2 border rounded-lg ${
                                    link.active
                                        ? 'bg-blue-600 text-white border-blue-600'
                                        : 'bg-white hover:bg-gray-50'
                                }`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </Card>
        </MemberLayout>
    );
}
