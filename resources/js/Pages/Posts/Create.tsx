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
        category_id: '',
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
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <Card className="p-6">
                        <div className="space-y-6">
                            <div>
                                <Label htmlFor="original_title">Title</Label>
                                <Input
                                    id="original_title"
                                    value={data.original_title}
                                    onChange={e => setData('original_title', e.target.value)}
                                    className="mt-1"
                                />
                                {errors.original_title && (
                                    <p className="text-sm text-red-600 mt-1">{errors.original_title}</p>
                                )}
                            </div>

                            <div>
                                <Label htmlFor="category">Category</Label>
                                <Select onValueChange={(value) => setData('category_id', value)}>
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a category" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {categories.map((category) => (
                                            <SelectItem key={category.id} value={category.id.toString()}>
                                                {category.display_name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                {errors.category_id && (
                                    <p className="text-sm text-red-600 mt-1">{errors.category_id}</p>
                                )}
                            </div>

                            <div>
                                <Label>Content</Label>
                                <div className="mt-1 border rounded-md">
                                    <Toolbar editor={editor} />
                                    <EditorContent 
                                        editor={editor} 
                                        className="prose max-w-none p-4"

                                    />
                                </div>
                                {errors.original_content && (
                                    <p className="text-sm text-red-600 mt-1">{errors.original_content}</p>
                                )}
                            </div>

                            <Button type="button" disabled={processing} onClick={handleSubmit}>
                                Publish
                            </Button>
                        </div>
                    </Card>
                </div>
            </div>
        </AuthenticatedLayout>
    );
} 