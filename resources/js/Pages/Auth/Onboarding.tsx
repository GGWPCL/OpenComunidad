import { Head, useForm } from '@inertiajs/react';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { PageProps } from '@/types';

export default function Onboarding({ auth }: PageProps) {
    const { data, setData, post, processing, errors } = useForm({
        name: auth.user?.name || '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('onboarding.complete'));
    };

    return (
        <>
            <Head title="Complete tu perfil" />
            <div className="min-h-screen flex items-center justify-center bg-gradient-to-b from-blue-50 to-white dark:from-gray-900 dark:to-gray-800">
                <Card className="w-[350px] sm:w-[450px]">
                    <CardHeader>
                        <CardTitle>¡Bienvenido!</CardTitle>
                        <CardDescription>
                            Para empezar, necesitamos algunos datos básicos.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="space-y-6">
                            <div className="space-y-2">
                                <Label htmlFor="name">¿Cómo te llamas?</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    required
                                    autoFocus
                                />
                                {errors.name && (
                                    <p className="text-sm text-red-600 dark:text-red-400">
                                        {errors.name}
                                    </p>
                                )}
                            </div>

                            <Button
                                className="w-full"
                                disabled={processing}
                            >
                                Continuar
                            </Button>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}
