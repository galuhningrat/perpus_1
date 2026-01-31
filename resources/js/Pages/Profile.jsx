// resources/js/Pages/Profile.jsx
import { useForm, usePage } from '@inertiajs/react';
import MemberLayout from '@/Layouts/MemberLayout';
import AdminLayout from '@/Layouts/AdminLayout';
import Card from '@/Components/UI/Card';
import Input from '@/Components/UI/Input';
import Button from '@/Components/UI/Button';

export default function Profile() {
    const { auth } = usePage().props;
    const Layout = auth.user.has_admin_access ? AdminLayout : MemberLayout;

    const { data, setData, put, processing, errors } = useForm({
        name: auth.user.name,
        email: auth.user.email,
        phone: auth.user.phone || '',
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put('/profile');
    };

    return (
        <Layout>
            <div className="max-w-2xl mx-auto">
                <Card title="Profile Saya">
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div className="flex items-center gap-4">
                                <div className="w-16 h-16 rounded-full bg-blue-600 text-white flex items-center justify-center text-2xl font-bold">
                                    {auth.user.name.charAt(0)}
                                </div>
                                <div>
                                    <p className="font-semibold text-lg">{auth.user.name}</p>
                                    <p className="text-sm text-gray-600">{auth.user.nim_nidn}</p>
                                    <p className="text-sm text-gray-600">
                                        {auth.user.role === 'super_admin' && 'Super Admin'}
                                        {auth.user.role === 'pustakawan' && 'Pustakawan'}
                                        {auth.user.role === 'member' && `Mahasiswa - ${auth.user.prodi}`}
                                    </p>
                                </div>
                            </div>
                        </div>

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

                        <Input
                            label="No. Telepon"
                            value={data.phone}
                            onChange={(e) => setData('phone', e.target.value)}
                            error={errors.phone}
                        />

                        <hr className="my-6" />

                        <h3 className="font-semibold text-lg mb-4">Ubah Password</h3>

                        <Input
                            label="Password Saat Ini"
                            type="password"
                            value={data.current_password}
                            onChange={(e) => setData('current_password', e.target.value)}
                            error={errors.current_password}
                        />

                        <Input
                            label="Password Baru"
                            type="password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            error={errors.password}
                        />

                        <Input
                            label="Konfirmasi Password Baru"
                            type="password"
                            value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                            error={errors.password_confirmation}
                        />

                        <Button type="submit" disabled={processing}>
                            {processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                        </Button>
                    </form>
                </Card>
            </div>
        </Layout>
    );
}
