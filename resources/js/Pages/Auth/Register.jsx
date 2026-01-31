import { useForm, Link } from '@inertiajs/react';
import Input from '@/Components/UI/Input';
import Button from '@/Components/UI/Button';

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({
        nim_nidn: '',
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        prodi: '',
        phone: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/register');
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center p-4">
            <div className="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
                <div className="text-center mb-8">
                    <h1 className="text-3xl font-bold text-gray-900">📚 Daftar Akun</h1>
                    <p className="text-gray-600 mt-2">STTI Perpustakaan</p>
                </div>

                <form onSubmit={handleSubmit} className="space-y-4">
                    <Input
                        label="NIM/NIDN"
                        value={data.nim_nidn}
                        onChange={(e) => setData('nim_nidn', e.target.value)}
                        error={errors.nim_nidn}
                        required
                    />

                    <Input
                        label="Nama Lengkap"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        error={errors.name}
                        required
                    />

                    <Input
                        label="Email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        error={errors.email}
                        required
                    />

                    <div className="mb-4">
                        <label className="block text-sm font-medium text-gray-700 mb-2">
                            Program Studi
                        </label>
                        <select
                            value={data.prodi}
                            onChange={(e) => setData('prodi', e.target.value)}
                            className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            required
                        >
                            <option value="">Pilih Prodi</option>
                            <option value="Informatika">Informatika</option>
                            <option value="Elektro">Elektro</option>
                            <option value="Mesin">Mesin</option>
                        </select>
                        {errors.prodi && <p className="mt-1 text-sm text-red-600">{errors.prodi}</p>}
                    </div>

                    <Input
                        label="No. Telepon (Opsional)"
                        value={data.phone}
                        onChange={(e) => setData('phone', e.target.value)}
                        error={errors.phone}
                    />

                    <Input
                        label="Password"
                        type="password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        error={errors.password}
                        required
                    />

                    <Input
                        label="Konfirmasi Password"
                        type="password"
                        value={data.password_confirmation}
                        onChange={(e) => setData('password_confirmation', e.target.value)}
                        error={errors.password_confirmation}
                        required
                    />

                    <Button type="submit" className="w-full" disabled={processing}>
                        {processing ? 'Loading...' : 'Daftar'}
                    </Button>
                </form>

                <div className="mt-6 text-center">
                    <p className="text-sm text-gray-600">
                        Sudah punya akun?{' '}
                        <Link href="/login" className="text-blue-600 hover:text-blue-700 font-medium">
                            Login di sini
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    );
}
