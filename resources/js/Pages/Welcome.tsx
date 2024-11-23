import { Card, CardHeader, CardTitle } from '@/Components/ui/card';
import { PageProps } from '@/types';
import { Head, Link } from '@inertiajs/react';

export default function Welcome({
    auth
}: PageProps<{ laravelVersion: string; phpVersion: string }>) {
    return (
        <>
            <Head title="Open Comunidad - Switch 345" />
            <div className="min-h-screen bg-gradient-to-b from-blue-50 to-white dark:from-gray-900 dark:to-gray-800">
                {/* Navigation */}
                <nav className="fixed w-full bg-white/80 backdrop-blur-sm shadow-sm dark:bg-gray-900/80">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                        <div className="flex items-center space-x-2">
                            <svg className="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23,11H13V1c0-0.6-0.4-1-1-1s-1,0.4-1,1v10H1c-0.6,0-1,0.4-1,1s0.4,1,1,1h10v10c0,0.6,0.4,1,1,1s1-0.4,1-1V13h10c0.6,0,1-0.4,1-1S23.6,11,23,11z"/>
                            </svg>
                            <span className="text-xl font-bold">Open Comunidad</span>
                        </div>

                        <div className="flex space-x-4">
                            {auth.user ? (
                                <Link href={route('dashboard')} className="btn-primary">
                                    Dashboard
                                </Link>
                            ) : (
                                <>
                                    <Link href={route('login')} className="btn-secondary">
                                        Iniciar Sesión
                                    </Link>
                                    <Link href={route('register')} className="btn-primary">
                                        Registrarse
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
                            Gestiona tu comunidad de vecinos de forma fácil y eficiente. Organiza eventos,
                            reporta problemas y mantén una comunicación fluida entre todos.
                        </p>
                    </div>
                </div>

                {/* Features Grid */}
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {/* Feature 1 */}
                        <Card className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <svg className="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span>Eventos Comunitarios</span>
                                </CardTitle>
                                <p className="text-gray-600 dark:text-gray-300">
                                    Organiza asados, reuniones y eventos. Coordina fechas y participantes fácilmente.
                                </p>
                            </CardHeader>
                        </Card>

                        {/* Feature 2 */}
                        <Card className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <svg className="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                                    </svg>
                                    <span>Reportes y Mantenimiento</span>
                                </CardTitle>
                                <p className="text-gray-600 dark:text-gray-300">
                                    Reporta problemas de mantenimiento y sigue su estado hasta su resolución.
                                </p>
                            </CardHeader>
                        </Card>

                        {/* Feature 3 */}
                        <Card className="hover:shadow-lg transition-shadow">
                            <CardHeader>
                                <CardTitle className="flex items-center space-x-2">
                                    <svg className="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Gestión Transparente</span>
                                </CardTitle>
                                <p className="text-gray-600 dark:text-gray-300">
                                    Seguimiento de la comunicación entre vecinos y administración en tiempo real.
                                </p>
                            </CardHeader>
                        </Card>
                    </div>
                </div>
            </div>
        </>
    );
}
