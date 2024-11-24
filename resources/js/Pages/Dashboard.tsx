import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState } from 'react';
import { useForm } from '@inertiajs/react';
import Modal from '@/Components/Modal';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import PrimaryButton from '@/Components/PrimaryButton';

interface Community {
    name: string;
    address: string;
    slug: string;
    status: string;
    logo?: {
        url: string;
    };
    banner?: {
        url: string;
    };
    view_only?: boolean;
}

export default function Dashboard({ isAdmin, userCommunities = [], otherCommunities = [] }: { isAdmin: boolean, userCommunities: Community[], otherCommunities: Community[] }) {
    const [editingCommunity, setEditingCommunity] = useState<Community | null>(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        slug: '',
        logo: null as File | null,
        banner: null as File | null,
    });

    const openEditModal = (community: Community) => {
        setEditingCommunity(community);
        setData({
            name: community.name,
            slug: community.slug,
            logo: null,
            banner: null,
        });
    };

    const closeModal = () => {
        setEditingCommunity(null);
        reset();
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(`/communities/${editingCommunity?.slug}`, {
            onSuccess: () => closeModal(),
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Comunidades
                </h2>
            }
        >
            <Head title="Mis Comunidades" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg mb-8">
                        <div className="p-6">
                            <h3 className="text-xl font-semibold mb-4">Mis Comunidades</h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {(userCommunities || []).map((community, index) => (
                                    <a
                                        href={`/communities/${community.slug}`}
                                        key={index}
                                        className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer block w-full"
                                    >
                                        <div className="h-32 w-full bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 flex items-center justify-center relative">
                                            {community.banner ? (
                                                <img
                                                    src={community.banner.url}
                                                    alt={`${community.name} banner`}
                                                    className="absolute inset-0 w-full h-full object-cover"
                                                />
                                            ) : (
                                                <div className="absolute inset-0 w-full h-full bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800" />
                                            )}
                                            {community.logo ? (
                                                <img
                                                    src={community.logo.url}
                                                    alt={community.name}
                                                    className="w-24 h-24 rounded-full object-cover z-10 border-2 border-white shadow-md"
                                                />
                                            ) : (
                                                <svg className="w-24 h-24 text-blue-500 dark:text-blue-400 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            )}
                                        </div>
                                        <div className="p-4">
                                            <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                                {community.name}
                                            </h3>
                                            <p className="text-sm text-gray-600 dark:text-gray-300 mb-2">
                                                {community.address}
                                            </p>
                                            {community.status === 'pending' && (
                                                <span
                                                    className="inline-block px-2 py-1 text-sm bg-yellow-100 text-yellow-800 rounded cursor-help"
                                                    title="Un verificador revisará pronto tu registro"
                                                >
                                                    Aprobación pendiente
                                                </span>
                                            )}

                                            {isAdmin && (
                                                <button
                                                    onClick={(e) => {
                                                        e.preventDefault();
                                                        openEditModal(community);
                                                    }}
                                                    className="mt-2 text-sm text-blue-600 hover:text-blue-800"
                                                >
                                                    Editar comunidad
                                                </button>
                                            )}
                                        </div>
                                    </a>
                                ))}

                            </div>
                        </div>
                    </div>

                    {otherCommunities && otherCommunities.length > 0 && (
                        <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div className="border-t-4 border-blue-500 p-6">
                                <h3 className="text-xl font-semibold mb-4">Otras Comunidades</h3>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    {otherCommunities.map((community, index) => (
                                        <a
                                            href={`/communities/${community.slug}`}
                                            key={index}
                                            className="bg-gray-50 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer block w-full"
                                        >
                                            <div className="h-32 w-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center relative">
                                                {community.banner ? (
                                                    <img
                                                        src={community.banner.url}
                                                        alt={`${community.name} banner`}
                                                        className="absolute inset-0 w-full h-full object-cover opacity-75"
                                                    />
                                                ) : (
                                                    <div className="absolute inset-0 w-full h-full bg-gradient-to-br from-gray-100 to-gray-200" />
                                                )}
                                                {community.logo ? (
                                                    <img
                                                        src={community.logo.url}
                                                        alt={community.name}
                                                        className="w-24 h-24 rounded-full object-cover z-10 border-2 border-white shadow-md"
                                                    />
                                                ) : (
                                                    <svg className="w-24 h-24 text-gray-400 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                )}
                                            </div>
                                            <div className="p-4">
                                                <h3 className="text-lg font-semibold text-gray-700 mb-1">
                                                    {community.name}
                                                </h3>
                                                <p className="text-sm text-gray-500 mb-2">
                                                    {community.address}
                                                </p>
                                            </div>
                                            {community.view_only && (
                                                <span
                                                    className="inline-block px-2 py-1 text-sm bg-gray-100 text-gray-800 rounded cursor-help"
                                                    title="Solo puedes ver esta comunidad"
                                                >
                                                    Solo lectura
                                                </span>
                                            )}
                                        </a>
                                    ))}
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            <Modal show={!!editingCommunity} onClose={closeModal}>
                <form onSubmit={handleSubmit} className="p-6">
                    <h2 className="text-lg font-medium mb-4">Editar Comunidad</h2>

                    <div className="mb-4">
                        <InputLabel htmlFor="name" value="Nombre" />
                        <TextInput
                            id="name"
                            type="text"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            className="mt-1 block w-full"
                        />
                        <InputError message={errors.name} className="mt-2" />
                    </div>

                    <div className="mb-4">
                        <InputLabel htmlFor="slug" value="Slug" />
                        <TextInput
                            id="slug"
                            type="text"
                            value={data.slug}
                            onChange={(e) => setData('slug', e.target.value)}
                            className="mt-1 block w-full"
                        />
                        <InputError message={errors.slug} className="mt-2" />
                    </div>

                    <div className="mb-4">
                        <InputLabel htmlFor="logo" value="Logo" />
                        <input
                            type="file"
                            id="logo"
                            onChange={(e) => setData('logo', e.target.files?.[0] || null)}
                            className="mt-1 block w-full"
                        />
                        <InputError message={errors.logo} className="mt-2" />
                    </div>

                    <div className="mb-4">
                        <InputLabel htmlFor="banner" value="Banner" />
                        <input
                            type="file"
                            id="banner"
                            onChange={(e) => setData('banner', e.target.files?.[0] || null)}
                            className="mt-1 block w-full"
                        />
                        <InputError message={errors.banner} className="mt-2" />
                    </div>

                    <div className="mt-6 flex justify-end">
                        <PrimaryButton disabled={processing}>
                            Guardar cambios
                        </PrimaryButton>
                    </div>
                </form>
            </Modal>
        </AuthenticatedLayout>
    );
}
