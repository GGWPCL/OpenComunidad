import { Card, CardHeader, CardTitle } from '@/Components/ui/card';
import { PageProps } from '@/types';
import { Head, Link } from '@inertiajs/react';
import ApplicationLogo from '@/Components/ApplicationLogo';

interface Community {
    name: string;
    address: string;
    slug: string;
}

export default function Welcome({
    auth,
    communities = [],
}: PageProps<{ communities: Community[] }>) {
    return (
        <>
            <Head title="Open Comunidad - Switch 345" />
            <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white dark:from-gray-900 dark:to-gray-800">
                {/* Navigation */}
                <nav className="fixed w-full bg-white/80 backdrop-blur-sm shadow-sm dark:bg-gray-900/80">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                        <ApplicationLogo />

                        <div className="flex space-x-4">
                            {auth.user ? (
                                <Link href={route('dashboard')} className="btn-primary">
                                    Dashboard
                                </Link>
                            ) : (
                                <>
                                    <Link href={route('login')} className="btn-primary">
                                        Ingresar
                                    </Link>
                                </>
                            )}
                        </div>
                    </div>
                </nav>

                {/* Hero Section */}
                <div className="pt-24 pb-16 px-4">
                    <div className="max-w-7xl mx-auto text-center">
                        <h1 className="text-4xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                            Tu comunidad conectada
                        </h1>
                        <p className="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-3xl mx-auto">
                            Mantente al día con los temas relevantes de tu comunidad. Mejora la comunicación
                            entre propietarios y administradores, creando una comunidad más conectada y eficiente.
                        </p>
                    </div>
                </div>

                {/* User Types Grid */}
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {/* Neighbors */}
                        <Card className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <svg className="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>Para Vecinos</span>
                                </CardTitle>
                                <p className="text-gray-600 dark:text-gray-300">
                                    Haz oír tu voz y participa activamente en tu comunidad. Propón iniciativas y encuentra apoyo para mejorar tu entorno residencial.
                                </p>
                            </CardHeader>
                        </Card>

                        {/* Facility Managers */}
                        <Card className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <svg className="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span>Para Administradores</span>
                                </CardTitle>
                                <p className="text-gray-600 dark:text-gray-300">
                                    Comprende mejor las necesidades de tu comunidad. Gestiona la comunicación de manera efectiva y toma decisiones informadas en un ambiente seguro.
                                </p>
                            </CardHeader>
                        </Card>

                        {/* Owners */}
                        <Card className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <svg className="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Para Propietarios</span>
                                </CardTitle>
                                <p className="text-gray-600 dark:text-gray-300">
                                    Protege tu inversión fomentando una comunidad saludable. Reduce la rotación de inquilinos y aumenta el valor de tu propiedad a largo plazo.
                                </p>
                            </CardHeader>
                        </Card>
                    </div>
                </div>

                {/* Comunidades */}
                <div className="pt-12 pb-16 px-4">
                    <div className="max-w-7xl mx-auto">
                        <h2 className="text-4xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6 text-center">
                            Nuestras Comunidades
                        </h2>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-12">
                            {communities.map((community, index) => (
                                <a
                                    href={`/communities/${community.slug}`}
                                    key={index}
                                    className="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow cursor-pointer block w-full"
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
                                        <p className="text-sm text-gray-600 dark:text-gray-300">
                                            {community.address}
                                        </p>
                                    </div>
                                </a>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Footer */}
                <footer className="py-8 text-center text-gray-600 dark:text-gray-400">
                    Made with ❤️ at Platanus Hack 24
                </footer>
            </div>
        </>
    );
}
