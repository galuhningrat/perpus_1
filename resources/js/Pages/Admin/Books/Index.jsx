// resources/js/Pages/Admin/Books/Index.jsx
import AdminLayout from '@/Layouts/AdminLayout';
import Card from '@/Components/UI/Card';
import Table from '@/Components/UI/Table';
import Button from '@/Components/UI/Button';
import Badge from '@/Components/UI/Badge';
import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Index({ books, categories, filters }) {
    const [search, setSearch] = useState(filters.search || '');
    const [categoryId, setCategoryId] = useState(filters.category_id || '');

    const handleSearch = () => {
        router.get('/admin/books', { search, category_id: categoryId }, { preserveState: true });
    };

    const columns = [
        {
            header: 'Judul',
            accessor: 'title',
            render: (book) => (
                <div>
                    <p className="font-medium">{book.title}</p>
                    <p className="text-xs text-gray-500">{book.author}</p>
                </div>
            ),
        },
        { header: 'Kategori', accessor: 'category.name' },
        { header: 'ISBN', accessor: 'isbn' },
        {
            header: 'Stok',
            render: (book) => `${book.available_stock}/${book.stock}`,
        },
        {
            header: 'Status',
            render: (book) => (
                <Badge variant={book.status === 'available' ? 'success' : 'warning'}>
                    {book.status === 'available' ? 'Tersedia' : 'Dipinjam'}
                </Badge>
            ),
        },
        {
            header: 'Aksi',
            render: (book) => (
                <div className="flex gap-2">
                    <Link
                        href={`/admin/books/${book.id}`}
                        className="text-blue-600 hover:text-blue-700 text-sm"
                    >
                        Detail
                    </Link>
                    <Link
                        href={`/admin/books/${book.id}/edit`}
                        className="text-green-600 hover:text-green-700 text-sm"
                    >
                        Edit
                    </Link>
                </div>
            ),
        },
    ];

    return (
        <AdminLayout>
            <Card
                title="Manajemen Buku"
                action={
                    <Link href="/admin/books/create">
                        <Button>+ Tambah Buku</Button>
                    </Link>
                }
            >
                <div className="mb-6 flex gap-4">
                    <input
                        type="text"
                        placeholder="Cari buku..."
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
                                {cat.name}
                            </option>
                        ))}
                    </select>
                    <Button onClick={handleSearch}>Cari</Button>
                </div>

                <Table columns={columns} data={books.data} />

                {/* Pagination */}
                <div className="mt-4 flex justify-between items-center">
                    <p className="text-sm text-gray-600">
                        Showing {books.from} to {books.to} of {books.total} results
                    </p>
                    <div className="flex gap-2">
                        {books.links.map((link, index) => (
                            <Link
                                key={index}
                                href={link.url}
                                className={`px-3 py-1 border rounded ${
                                    link.active ? 'bg-blue-600 text-white' : 'bg-white'
                                }`}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                </div>
            </Card>
        </AdminLayout>
    );
}
