import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
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

    const { data, setData, post, processing, errors } = useForm({
        original_title: '',
        original_content: '',
        category_id: selectedCategory,
    });

    const handleSubmit = () => {
        const community = route().params.community;
        post(route('posts.store', { community }));
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
                                disabled={processing}
                                onClick={handleSubmit}
                                className="w-full text-lg py-6"
                            >
                                Publicar
                            </Button>
                        </div>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
