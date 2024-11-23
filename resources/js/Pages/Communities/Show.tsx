import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { Card, CardContent } from '@/Components/ui/card';
import { ScrollArea } from '@/Components/ui/scroll-area';
import { Button } from '@/Components/ui/button';

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

// Mock data - replace with real data from your backend
const mockPosts: Post[] = [
    {
        id: 1,
        title: "Propuesta: Instalación de cámaras de seguridad",
        content: "Propongo instalar cámaras de seguridad en los accesos principales...",
        category: "Propuestas",
        author: "Juan Pérez",
        votes: 15,
        comments: 23,
        createdAt: "2024-03-15"
    },
    {
        id: 2,
        title: "Encuesta: Horario de cierre de la piscina",
        content: "¿Cuál creen que debería ser el horario de cierre de la piscina?",
        category: "Encuestas",
        author: "María González",
        votes: 8,
        comments: 12,
        createdAt: "2024-03-14"
    },
    // Add more mock posts as needed
];


export default function Show({ community }: { community?: { name: string } }) {
    const categories = [
        { name: "Todo", icon: "📋" },
        { name: "Propuestas", icon: "💡" },
        { name: "Encuestas", icon: "📊" },
        { name: "Imagina", icon: "🎨" },
    ];

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
                                        {categories.map((category) => (
                                            <Button
                                                key={category.name}
                                                variant="ghost"
                                                className="w-full justify-start"
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
                                {mockPosts.map((post) => (
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
