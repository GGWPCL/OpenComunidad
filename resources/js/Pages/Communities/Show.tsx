import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, usePage } from '@inertiajs/react';
import { Card, CardContent } from '@/Components/ui/card';
import { ScrollArea } from '@/Components/ui/scroll-area';
import { Button } from '@/Components/ui/button';
import { useState, useEffect } from 'react';

interface Post {
    id: number;
    title: string;
    content: string;
    category: string;
    author: string;
    votes: number;
    comments: number;
    createdAt: string;
}

interface Category {
    name: string;
    internal_name: string;
    icon: string;
}

interface Props {
    community?: {
        name: string
    };
    categories: Category[];
    posts: Post[];
}

export default function Show({ community, categories, posts }: Props) {
    const [currentCategory, setCurrentCategory] = useState<string | undefined>(
        (usePage().props as { category?: string }).category
    );

    useEffect(() => {
        const category = new URLSearchParams(window.location.search).get('category');
        setCurrentCategory(category || undefined);
    }, []);

    const handleCategoryClick = (internal_name?: string) => {
        setCurrentCategory(internal_name);

        router.get(
            route(route().current() ?? '', { ...route().params }),
            { category: internal_name },
            { preserveState: true, preserveScroll: true, replace: true }
        );
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    {community?.name || "Comunidad"}
                </h2>
            }
        >
            <Head title={`${community?.name || 'Comunidad'} - Open Comunidad`} />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="flex gap-6">
                        {/* Sidebar */}
                        <div className="w-64 shrink-0">
                            <Card>
                                <ScrollArea className="h-[calc(100vh-12rem)]">
                                    <div className="p-4 space-y-2">
                                        <Button
                                            key={undefined}
                                            variant="ghost"
                                            className={`w-full justify-start ${!currentCategory
                                                ? 'bg-primary/10 text-primary'
                                                : ''
                                                }`}
                                            onClick={() => handleCategoryClick()}
                                        >
                                            <span className="mr-2">📋</span>
                                            Todo
                                        </Button>
                                        {categories.map((category) => (
                                            <Button
                                                key={category.internal_name}
                                                variant="ghost"
                                                className={`w-full justify-start ${category.internal_name === currentCategory
                                                    ? 'bg-primary/10 text-primary'
                                                    : ''
                                                    }`}
                                                onClick={() => handleCategoryClick(category.internal_name)}
                                            >
                                                <span className="mr-2">{category.icon}</span>
                                                {category.name}
                                            </Button>
                                        ))}
                                    </div>
                                </ScrollArea>
                            </Card>
                        </div>

                        {/* Main Content */}
                        <div className="flex-1 space-y-4">
                            <Button className="w-full">Crear nueva publicación</Button>

                            {/* Posts List */}
                            <div className="space-y-4">
                                {posts.map((post) => (
                                    <Card key={post.id} className="hover:shadow-md transition-shadow">
                                        <CardContent className="p-6">
                                            <div className="flex gap-4">
                                                {/* Votes */}
                                                <div className="flex flex-col items-center space-y-1">
                                                    <Button variant="ghost" size="sm">▲</Button>
                                                    <span className="text-sm font-medium">{post.votes}</span>
                                                    <Button variant="ghost" size="sm">▼</Button>
                                                </div>

                                                {/* Post Content */}
                                                <div className="flex-1">
                                                    <div className="flex items-center space-x-2 text-sm text-gray-500 mb-2">
                                                        <span className="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs">
                                                            {post.category}
                                                        </span>
                                                        <span>• Posted by {post.author}</span>
                                                        <span>• {post.createdAt}</span>
                                                    </div>
                                                    <h3 className="text-lg font-semibold mb-2">{post.title}</h3>
                                                    <p className="text-gray-600 mb-4">{post.content}</p>
                                                    <div className="flex items-center space-x-4">
                                                        <Button variant="ghost" size="sm">
                                                            💬 {post.comments} Comments
                                                        </Button>
                                                        <Button variant="ghost" size="sm">
                                                            Share
                                                        </Button>
                                                    </div>
                                                </div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
