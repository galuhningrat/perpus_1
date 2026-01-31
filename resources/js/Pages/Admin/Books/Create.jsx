// resources/js/Pages/Admin/Books/Create.jsx
import AdminLayout from '@/Layouts/AdminLayout';
import Card from '@/Components/UI/Card';
import Input from '@/Components/UI/Input';
import Button from '@/Components/UI/Button';
import { useForm } from '@inertiajs/react';

export default function Create({ categories }) {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        author: '',
        edition: '',
        publisher: '',
        publication_year: '',
        category_id: '',
        isbn: '',
        stock: '',
        shelf_location: '',
        description: '',
        cover_image: null,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/books');
    };

    return (
        <AdminLayout>
            <Card title="Tambah Buku Baru">
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Input
                            label="Judul Buku"
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                            error={errors.title}
                            required
                        />

                        <Input
                            label="Pengarang"
                            value={data.author}
                            onChange={(e) => setData('author', e.target.value)}
                            error={errors.author}
                            required
                        />

                        <Input
                            label="Edisi"
                            value={data.edition}
                            onChange={(e) => setData('edition', e.target.value)}
                            error={errors.edition}
                        />

                        <Input
                            label="Penerbit"
                            value={data.publisher}
                            onChange={(e) => setData('publisher', e.target.value)}
                            error={errors.publisher}
                        />

                        <Input
                            label="Tahun Terbit"
                            type="number"
                            value={data.publication_year}
                            onChange={(e) => setData('publication_year', e.target.value)}
                            error={errors.publication_year}
                        />

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Kategori
                            </label>
                            <select
                                value={data.category_id}
                                onChange={(e) => setData('category_id', e.target.value)}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                required
                            >
                                <option value="">Pilih Kategori</option>
                                {categories.map((cat) => (
                                    <option key={cat.id} value={cat.id}>
                                        {cat.name}
                                    </option>
                                ))}
                            </select>
                            {errors.category_id && (
                                <p className="mt-1 text-sm text-red-600">{errors.category_id}</p>
                            )}
                        </div>

                        <Input
                            label="ISBN"
                            value={data.isbn}
                            onChange={(e) => setData('isbn', e.target.value)}
                            error={errors.isbn}
                        />

                        <Input
                            label="Jumlah Stok"
                            type="number"
                            value={data.stock}
                            onChange={(e) => setData('stock', e.target.value)}
                            error={errors.stock}
                            required
                        />

                        <Input
                            label="Lokasi Rak"
                            value={data.shelf_location}
                            onChange={(e) => setData('shelf_location', e.target.value)}
                            error={errors.shelf_location}
                            placeholder="IF-001"
                        />

                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-2">
                                Cover Buku
                            </label>
                            <input
                                type="file"
                                onChange={(e) => setData('cover_image', e.target.files[0])}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                accept="image/*"
                            />
                            {errors.cover_image && (
                                <p className="mt-1 text-sm text-red-600">{errors.cover_image}</p>
                            )}
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                            Deskripsi
                        </label>
                        <textarea
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg"
                            rows="4"
                        />
                        {errors.description && (
                            <p className="mt-1 text-sm text-red-600">{errors.description}</p>
                        )}
                    </div>

                    <div className="flex gap-4">
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Menyimpan...' : 'Simpan'}
                        </Button>
                        <Button type="button" variant="secondary" onClick={() => window.history.back()}>
                            Batal
                        </Button>
                    </div>
                </form>
            </Card>
        </AdminLayout>
    );
}
