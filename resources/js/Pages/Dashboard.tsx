import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

interface Community {
    name: string;
    address: string;
    slug: string;
    status: string;
}

export default function Dashboard({ userCommunities = [] }: { userCommunities: Community[] }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Mis Comunidades
                </h2>
            }
        >
            <Head title="Mis Comunidades" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {(userCommunities || []).map((community, index) => (
                                    <a
                                        href={`/communities/${community.slug}`}
                                        key={index}
                                        className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer block w-full"
                                    >
                                        <div className="h-32 w-full bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900 dark:to-blue-800 flex items-center justify-center">
                                            <svg className="w-12 h-12 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
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
                                        </div>
                                    </a>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
