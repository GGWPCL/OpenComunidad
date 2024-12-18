import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, router, useForm, usePage } from '@inertiajs/react';
import { Card, CardContent } from '@/Components/ui/card';
import { ScrollArea } from '@/Components/ui/scroll-area';
import { Button } from '@/Components/ui/button';
import { FaThumbsUp } from "react-icons/fa6";
import { useState, useEffect } from 'react';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/Components/ui/dialog";
import { Sheet, SheetContent, SheetTrigger } from "@/Components/ui/sheet";
import { ChevronDown } from "lucide-react";

interface Post {
    id: number;
    title: string;
    content: string;
    category: string;
    author: number;
    votes: number;
    isUpVoted: boolean;
    comments: number;
    createdAt: string;
    isApproved: boolean;
}

interface Category {
    name: string;
    internal_name: string;
    description: string;
    icon: string;
}

interface Props {
    community: {
        name: string;
        isMember: boolean;
        banner: { url: string };
        logo: { url: string };
        address: string;
    };
    auth: { user: any };
    categories: Category[];
    posts: Post[];
}

export default function Show({ community, auth, categories, posts }: Props) {
    const { data, setData, post: postForm, processing } = useForm<{ post?: Post, shouldUpVote: boolean }>({
        post: undefined,
        shouldUpVote: false,
    });

    const [showCategoryModal, setShowCategoryModal] = useState(false);

    const [currentCategory, setCurrentCategory] = useState<string | undefined>(
        (usePage().props as { category?: string }).category
    );
    // posts state should be hydrated from props
    const [postsState, setPosts] = useState<Post[]>(
        posts
    );

    const isMember = (usePage().props as { community?: { isMember: boolean } }).community?.isMember;

    useEffect(() => {
        const category = new URLSearchParams(window.location.search).get('category');
        setCurrentCategory(category || undefined);
    }, []);

    useEffect(() => {
        if (data.post) {
            postForm(route('posts.up_vote', { post: data.post.id }), {
                data: { shouldUpVote: data.shouldUpVote },
                onSuccess: () => {
                    const updatedPosts = postsState.map(p => {
                        if (p.id === data.post?.id) {
                            let votes = p.votes;
                            if (!p.isUpVoted && data.shouldUpVote) {
                                votes++;
                            }
                            if (p.isUpVoted && !data.shouldUpVote) {
                                votes--;
                            }
                            return {
                                ...p,
                                votes,
                                isUpVoted: data.shouldUpVote
                            };
                        }
                        return p;
                    });
                    setPosts(updatedPosts);
                },
            });
        }
    }, [data]);

    const handleCategoryClick = (internal_name?: string) => {
        setCurrentCategory(internal_name);

        router.get(
            route(route().current() ?? '', { ...route().params }),
            { category: internal_name },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                onSuccess: (res: any) => {
                    setPosts((res.props as Props).posts);
                }
            }
        );
    };

    const Layout = auth.user ? AuthenticatedLayout : GuestLayout;

    const handleCreatePost = (category: string) => {
        router.visit(route('posts.create', {
            community: route().params.community,
            category: category
        }));
    };

    const CategoryList = () => (
        <div className="space-y-2 px-1">
            <Button
                key={undefined}
                variant="ghost"
                className={`w-full justify-start rounded-lg ${!currentCategory ? 'bg-primary/10 text-primary' : ''}`}
                onClick={() => handleCategoryClick()}
            >
                <span className="mr-2">📋</span>
                Todo
            </Button>
            {categories.map((category) => (
                <Button
                    key={category.internal_name}
                    variant="ghost"
                    className={`w-full justify-start rounded-lg ${category.internal_name === currentCategory ? 'bg-primary/10 text-primary' : ''}`}
                    onClick={() => handleCategoryClick(category.internal_name)}
                >
                    <span className="mr-2">{category.icon}</span>
                    {category.name}
                </Button>
            ))}
        </div>
    );

    return (
        <Layout
            header={
                !community.banner && !community.logo && (
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        {community?.name || "Comunidad"}
                    </h2>
                )
            }
        >
            <Head title={`${community?.name || 'Comunidad'} - OpenComunidad`} />

            {/* Guest warning banner */}
            {!auth.user && (
                <div className="bg-amber-50 border-b border-amber-100">
                    <div className="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
                        <p className="text-sm text-amber-700">
                            Estás viendo una versión limitada del contenido. Inicia sesión para ver todas las publicaciones y participar en la comunidad
                        </p>
                    </div>
                </div>
            )}

            {/* Non-member warning banner */}
            {auth.user && !isMember && (
                <div className="bg-amber-50 border-b border-amber-100">
                    <div className="mx-auto max-w-7xl px-4 py-3 sm:px-6 lg:px-8">
                        <p className="text-sm text-amber-700">
                            No eres miembro de esta comunidad. Estás viendo una versión limitada del contenido. Contacta con un administrador para unirte y participar en la comunidad.
                        </p>
                    </div>
                </div>
            )}

            {/* Community Banner */}
            <div className="relative">
                <div className="h-32 sm:h-48 w-full bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800 overflow-hidden">
                    {community.banner ? (
                        <>
                            {/* Desktop Banner */}
                            <img
                                src={community.banner.url}
                                alt={`${community.name} banner`}
                                className="hidden sm:block absolute inset-0 w-full h-full object-cover"
                            />
                            {/* Mobile Banner (optimized size) */}
                            <img
                                src={community.banner.url}
                                alt={`${community.name} banner`}
                                className="sm:hidden absolute inset-0 w-full h-full object-cover object-center"
                            />
                            {/* Overlay gradient for better text visibility */}
                            <div className="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent" />
                        </>
                    ) : (
                        <div className="absolute inset-0 w-full h-full bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900 dark:to-blue-800" />
                    )}

                    {/* Community Info */}
                    <div className="absolute bottom-0 left-0 right-0 p-4 sm:p-6 flex items-end">
                        <div className="flex items-center gap-4">
                            {community.logo ? (
                                <img
                                    src={community.logo.url}
                                    alt={community.name}
                                    className="w-16 h-16 sm:w-24 sm:h-24 rounded-full object-cover border-2 border-white shadow-md"
                                />
                            ) : (
                                <div className="w-16 h-16 sm:w-24 sm:h-24 rounded-full bg-white/10 backdrop-blur-sm border-2 border-white/50 shadow-md flex items-center justify-center">
                                    <svg className="w-8 h-8 sm:w-12 sm:h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            )}
                            <div>
                                <h1 className="text-2xl sm:text-3xl font-bold text-white shadow-sm">
                                    {community.name}
                                </h1>
                                {community.address && (
                                    <p className="text-sm sm:text-base text-white/90 shadow-sm">
                                        {community.address}
                                    </p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="py-6 lg:py-12">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="flex flex-col lg:flex-row gap-6">
                        {/* Mobile Categories Dropdown */}
                        <div className="lg:hidden w-full">
                            <Sheet>
                                <SheetTrigger asChild>
                                    <Button
                                        variant="outline"
                                        className="w-full rounded-lg shadow-sm hover:shadow-md transition-shadow"
                                    >
                                        <span className="flex-1">Categorías</span>
                                        <ChevronDown className="ml-2 h-4 w-4 opacity-50" />
                                    </Button>
                                </SheetTrigger>
                                <SheetContent
                                    side="top"
                                    className="w-full px-4 sm:px-6"
                                >
                                    <div className="py-6">
                                        <h3 className="text-lg font-semibold mb-4 px-1">Categorías</h3>
                                        <CategoryList />
                                    </div>
                                </SheetContent>
                            </Sheet>
                        </div>

                        {/* Desktop Sidebar */}
                        <div className="hidden lg:block w-72 shrink-0">
                            <Card className="sticky top-6">
                                <ScrollArea className="h-[calc(100vh-12rem)] rounded-lg">
                                    <div className="p-4">
                                        <h3 className="text-lg font-semibold mb-4 px-1">Categorías</h3>
                                        <CategoryList />
                                    </div>
                                </ScrollArea>
                            </Card>
                        </div>

                        {/* Main Content */}
                        <div className="flex-1 space-y-4">
                            <Dialog open={showCategoryModal} onOpenChange={setShowCategoryModal}>
                                <DialogContent className="sm:max-w-[600px] mx-4">
                                    <DialogHeader>
                                        <DialogTitle>Elige un tipo de publicación</DialogTitle>
                                    </DialogHeader>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">
                                        {categories.filter(cat => cat.internal_name !== 'all').map((category) => (
                                            <Button
                                                key={category.internal_name}
                                                variant="outline"
                                                className="h-auto p-4 flex flex-col items-center text-center space-y-2 w-full"
                                                onClick={() => handleCreatePost(category.internal_name)}
                                            >
                                                <div className="font-semibold">{category.icon}</div>
                                                <div className="font-semibold">{category.name}</div>
                                                <div className="text-sm text-gray-500 whitespace-normal w-full">{category.description}</div>
                                            </Button>
                                        ))}
                                    </div>
                                </DialogContent>
                            </Dialog>

                            <Button
                                className="w-full rounded-lg shadow-sm hover:shadow-md transition-shadow"
                                onClick={() => setShowCategoryModal(true)}
                            >
                                Crear nueva publicación
                            </Button>

                            {/* Posts List */}
                            <div className="space-y-4">
                                {postsState.map((post) => (
                                    <Card
                                        key={post.id}
                                        className="hover:shadow-md transition-shadow cursor-pointer rounded-lg"
                                        onClick={() => router.visit(route('posts.show', { post: post.id }))}
                                    >
                                        <CardContent className="p-6">
                                            <div className="flex gap-4">
                                                {/* Votes */}
                                                <div className="flex flex-col items-center justify-center space-y-1">
                                                    <Button variant="ghost" size="sm" onClick={() => setData({ post: post, shouldUpVote: !post.isUpVoted })} style={{ height: 'fit-content' }}>
                                                        <div className={'flex flex-col items-center justify-center py-3'}>
                                                            <FaThumbsUp
                                                                style={{ width: '24px', height: '24px' }}
                                                                className={post.isUpVoted ? 'text-primary' : 'text-gray-500'}
                                                            />
                                                            <div className="text-sm font-medium" >{post.votes}</div>
                                                        </div>
                                                    </Button>
                                                </div>

                                                <div className="flex-1">
                                                    <div className="flex flex-wrap items-center gap-2 text-sm text-gray-500 mb-2">
                                                        <span className="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs">
                                                            {post.category}
                                                        </span>
                                                        {!post.isApproved && (
                                                            <span className="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full text-xs">
                                                                Pendiente de aprobación
                                                            </span>
                                                        )}
                                                        <span className="hidden sm:inline">•</span>
                                                        <span>{post.createdAt.charAt(0).toUpperCase() + post.createdAt.slice(1)}</span>
                                                        <span className="hidden sm:inline">•</span>
                                                        <span className="w-full sm:w-auto">Por un vecino de la comunidad</span>
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
        </Layout >
    );
}
