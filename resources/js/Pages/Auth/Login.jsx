import { useForm } from '@inertiajs/react';
import Input from '@/Components/UI/Input';
import Button from '@/Components/UI/Button';
import { Link } from '@inertiajs/react';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center p-4">
            <div className="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
                <div className="text-center mb-8">
                    <h1 className="text-3xl font-bold text-gray-900">📚 STTI Perpustakaan</h1>
                    <p className="text-gray-600 mt-2">Silakan login untuk melanjutkan</p>
                </div>

                <form onSubmit={handleSubmit}>
                    <Input
                        label="Email"
                        type="email"
                        value={data.email}
                        onChange={(e) => setData('email', e.target.value)}
                        error={errors.email}
                        required
                        autoFocus
                    />

                    <Input
                        label="Password"
                        type="password"
                        value={data.password}
                        onChange={(e) => setData('password', e.target.value)}
                        error={errors.password}
                        required
                    />

                    <div className="flex items-center mb-6">
                        <input
                            type="checkbox"
                            id="remember"
                            checked={data.remember}
                            onChange={(e) => setData('remember', e.target.checked)}
                            className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        />
                        <label htmlFor="remember" className="ml-2 text-sm text-gray-600">
                            Ingat saya
                        </label>
                    </div>

                    <Button type="submit" className="w-full" disabled={processing}>
                        {processing ? 'Loading...' : 'Login'}
                    </Button>
                </form>

                <div className="mt-6 text-center">
                    <p className="text-sm text-gray-600">
                        Belum punya akun?{' '}
                        <Link href="/register" className="text-blue-600 hover:text-blue-700 font-medium">
                            Daftar di sini
                        </Link>
                    </p>
                </div>

                <div className="mt-8 p-4 bg-blue-50 rounded-lg">
                    <p className="text-xs text-gray-600 font-semibold mb-2">Demo Credentials:</p>
                    <p className="text-xs text-gray-600">Admin: admin@stti.ac.id / admin123</p>
                    <p className="text-xs text-gray-600">Pustakawan: pustaka1@stti.ac.id / pustaka123</p>
                    <p className="text-xs text-gray-600">Mahasiswa: rizki.if23@student.stti.ac.id / mahasiswa123</p>
                </div>
            </div>
        </div>
    );
}
