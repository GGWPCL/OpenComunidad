import { useEffect, FormEventHandler } from 'react';
import { Head, useForm } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Alert, AlertDescription } from '@/Components/ui/alert';

export default function MagicLogin() {
    const { data, setData, post, processing, errors, reset, recentlySuccessful } = useForm({
        email: '',
    });

    useEffect(() => {
        return () => {
            reset('email');
        };
    }, []);

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('magic-link.send'));
    };

    return (
        <>
            <Head title="Magic Login" />
            <div className="min-h-screen flex items-center justify-center bg-gradient-to-b from-blue-50 to-white dark:from-gray-900 dark:to-gray-800">
                <Card className="w-[350px] sm:w-[450px]">
                    <CardHeader>
                        <CardTitle>Iniciar Sesión</CardTitle>
                        <CardDescription>
                            Ingresa tu correo electrónico y te enviaremos un enlace mágico para iniciar sesión.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="space-y-6">
                            <div className="space-y-2">
                                <Label htmlFor="email">Correo Electrónico</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value={data.email}
                                    className="block w-full"
                                    onChange={(e) => setData('email', e.target.value)}
                                    required
                                />
                                {errors.email && (
                                    <p className="text-sm text-red-600 dark:text-red-400">
                                        {errors.email}
                                    </p>
                                )}
                            </div>

                            {recentlySuccessful && (
                                <Alert>
                                    <AlertDescription>
                                        ¡Hemos enviado un enlace mágico a tu correo electrónico!
                                    </AlertDescription>
                                </Alert>
                            )}

                            <Button
                                className="w-full"
                                disabled={processing}
                            >
                                {processing ? 'Enviando...' : 'Enviar enlace mágico'}
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
