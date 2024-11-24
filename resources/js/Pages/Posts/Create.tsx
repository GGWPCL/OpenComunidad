import { useState } from 'react';
import { Head, useForm, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Toolbar from './Toolbar';
import { Markdown } from 'tiptap-markdown';
import { useToast } from "@/hooks/use-toast"
import axios from 'axios';

interface Category {
    id: number;
    display_name: string;
    internal_name: string;
}

export default function Create({ categories }: { categories: Category[] }) {

    const [selectedCategory] = useState(() => {
        const params = new URLSearchParams(window.location.search);
        return params.get('category') || '';
    });

    const editor = useEditor({
        extensions: [
            StarterKit,
            Markdown,
        ],
        onUpdate: ({ editor }) => {
            // Convert the HTML content to markdown when updating
            const markdown = editor.storage.markdown.getMarkdown();
            setData('original_content', markdown);
        },
    });

    const { toast } = useToast();

    const { data, setData, post, processing, errors } = useForm({
        original_title: '',
        original_content: '',
        category_id: selectedCategory,
    });

    // Add new state for rate limiting
    const [isRateLimited, setIsRateLimited] = useState(false);
    const [countdown, setCountdown] = useState(0);

    // Add new state for verification
    const [isVerifying, setIsVerifying] = useState(false);

    // Modified handleSubmit function
    const handleSubmit = async () => {
        if (isRateLimited) return;

        setIsVerifying(true);
        const community = route().params.community;

        try {
            const response = await axios.post(route('posts.preflight'), {
                title: data.original_title,
                content: data.original_content,
            });

            const result = response.data;
            setIsVerifying(false);

            if (!result.ready) {
                toast({
                    title: "Sugerencia de mejora",
                    description: result.message,
                    variant: "destructive",
                });
                setIsRateLimited(true);
                startRateLimit();
                return;
            }

            post(route('posts.store', { community }));
        } catch (error) {
            setIsVerifying(false);
            console.error('Preflight check failed:', error);
            toast({
                title: "Error",
                description: "No se pudo validar el contenido. Por favor, inténtalo de nuevo.",
                variant: "destructive",
            });
            setIsRateLimited(true);
            startRateLimit();
        }
    };

    // Add rate limit function
    const startRateLimit = () => {
        setCountdown(30); // 30 second cooldown
        const timer = setInterval(() => {
            setCountdown((prev) => {
                if (prev <= 1) {
                    clearInterval(timer);
                    setIsRateLimited(false);
                    return 0;
                }
                return prev - 1;
            });
        }, 1000);
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800">Create new post</h2>}
        >
            <Head title="Create Post" />

            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <Card className="p-6 shadow-lg">
                        <div className="space-y-8">
                            <div className="space-y-2">
                                <Label htmlFor="original_title" className="text-lg font-medium">
                                    Título
                                </Label>
                                <Input
                                    id="original_title"
                                    value={data.original_title}
                                    onChange={e => setData('original_title', e.target.value)}
                                    className="mt-1 text-lg"
                                    placeholder="Enter your post title"
                                />
                                {errors.original_title && (
                                    <p className="text-sm text-red-600">{errors.original_title}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="category" className="text-lg font-medium">
                                    Categoría
                                </Label>
                                <Select
                                    defaultValue={selectedCategory}
                                    onValueChange={(value) => setData('category_id', value)}
                                >
                                    <SelectTrigger className="text-lg">
                                        <SelectValue placeholder="Select a category" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {categories.map((category) => (
                                            <SelectItem
                                                key={category.id}
                                                value={category.id.toString()}
                                                className="text-lg"
                                            >
                                                {category.display_name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.category_id && (
                                    <p className="text-sm text-red-600">{errors.category_id}</p>
                                )}
                            </div>

                            <div className="space-y-2">
                                <Label className="text-lg font-medium">
                                    Contenido
                                </Label>
                                <div className="mt-1 border rounded-md overflow-hidden">
                                    <Toolbar editor={editor} />
                                    <EditorContent
                                        editor={editor}
                                        className="prose prose-lg max-w-none p-6"
                                    />
                                </div>
                                {errors.original_content && (
                                    <p className="text-sm text-red-600">{errors.original_content}</p>
                                )}
                            </div>

                            <Button
                                type="button"
                                disabled={processing || isRateLimited || isVerifying}
                                onClick={handleSubmit}
                                className="w-full text-lg py-6"
                            >
                                {processing ? (
                                    <span className="flex items-center gap-2">
                                        <svg className="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" fill="none" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                        </svg>
                                        Publicando...
                                    </span>
                                ) : isVerifying ? (
                                    <span className="flex items-center gap-2">
                                        <svg className="animate-spin h-5 w-5" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" fill="none" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                        </svg>
                                        Verificando...
                                    </span>
                                ) : isRateLimited ? (
                                    `Espera ${countdown} segundos...`
                                ) : (
                                    'Publicar'
                                )}
                            </Button>
                        </div>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
