import { Head, router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import GuestLayout from '@/Layouts/GuestLayout';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardTitle } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';
import { useState } from 'react';
import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Toolbar from './Toolbar';
import { Markdown } from 'tiptap-markdown';
import UpVote from '@/Components/UpVote';
import FollowPost from '@/Components/FollowPost';

interface Post {
    id: number;
    title: string;
    content: string;
    category: string;
    author: number;
    votes: number;
    isUpVoted: boolean;
    isFollowed: boolean;
    comments: Comment[];
    createdAt: string;
}

interface Comment {
    id: number;
    content: string;
    author: number;
    createdAt: string;
}

interface Props {
    auth: {
        user: any;
        roles: {
            is_admin: boolean;
            is_manager: boolean;
            is_neighbor: boolean;
        };
    };
    post: Post;
    comments: Comment[];
    community: {
        name: string;
        slug: string;
    };
}

export default function Show({ auth, post, comments, community }: Props) {
    const [postState, setPostState] = useState<Post>(post);

    const commentEditor = useEditor({
        extensions: [
            StarterKit,
            Markdown,
        ],
        onUpdate: ({ editor }) => {
            const markdown = editor.storage.markdown.getMarkdown();
            setCommentData('original_content', markdown);
        },
    });

    const {
        data: commentData,
        setData: setCommentData,
        post: postComment,
        processing: commentProcessing,
        errors,
        reset: resetComment
    } = useForm({
        original_content: '',
    });

    const handleCommentSubmit = () => {
        postComment(route('comments.store', { post: post.id }), {
            onSuccess: () => {
                commentEditor?.commands.setContent('');
                resetComment();
                // Optionally refresh the page or update comments list
                router.reload();
            },
        });
    };

    const Layout = auth.user ? AuthenticatedLayout : GuestLayout;

    return (
        <Layout
            header={
                <div className="flex items-center justify-between space-x-4 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <Button
                        variant="ghost"
                        onClick={() => router.visit(route('communities.show', community.slug))}
                    >
                        ← Volver a {community.name}
                    </Button>

                    <div className="flex space-x-2">
                        <FollowPost
                            postId={post.id}
                            initialIsFollowed={post.isFollowed}
                        />
                        <UpVote
                            postId={post.id}
                            initialVotes={post.votes}
                            initialIsUpVoted={post.isUpVoted}
                        />
                    </div>
                </div>
            }
        >
            <Head title={`${post.title} - ${community.name}`} />

            <div className="py-12">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <Card className="mb-8">
                        <CardTitle className="p-6 text-2xl font-bold">{post.title}</CardTitle>
                        <CardContent className="p-6 pt-0">
                            <div className="prose prose-lg max-w-none mb-6">
                                {post.content}
                            </div>

                            <div className="flex flex-wrap items-center gap-2 text-gray-500">
                                <div className="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {post.category}
                                </div>
                                <div>•</div>
                                <div>{post.createdAt.charAt(0).toUpperCase() + post.createdAt.slice(1)}</div>
                                <div>•</div>
                                <div>Por un vecino de la comunidad</div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Comments Section */}
                    <div className="space-y-6 mt-12">
                        <h3 className="text-xl font-bold text-gray-900 text-center mb-8">
                            Comentarios ({Array.isArray(comments) ? comments.length : 0})
                        </h3>

                        {Array.isArray(comments) && comments.length > 0 ? (
                            <div className="space-y-6 max-w-3xl mx-auto">
                                {comments.map((comment) => (
                                    <Card key={comment.id} className="transform transition-all duration-200 hover:shadow-lg">
                                        <CardContent className="p-6">
                                            <div className="prose prose-sm max-w-none mb-6">
                                                {comment.content}
                                            </div>

                                            <div className="flex flex-wrap items-center justify-center gap-2 text-gray-500 text-sm border-t pt-4">
                                                <div className="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">
                                                    Comentario
                                                </div>
                                                <div>•</div>
                                                <div>{comment.createdAt.charAt(0).toUpperCase() + comment.createdAt.slice(1)}</div>
                                                <div>•</div>
                                                <div>Por un administrador de la comunidad</div>
                                            </div>
                                        </CardContent>
                                    </Card>
                                ))}
                            </div>
                        ) : (
                            <div className="text-center py-12 bg-gray-50 rounded-lg">
                                <p className="text-gray-500 text-lg">No hay comentarios aún</p>
                            </div>
                        )}
                    </div>

                    {/* Comment Form */}
                    {auth.user && (auth.roles.is_manager || auth.roles.is_admin) && (
                        <div className="mt-16 max-w-3xl mx-auto">
                            <h3 className="text-xl font-bold text-gray-900 text-center mb-8">
                                Añadir un comentario
                            </h3>
                            <Card className="transform transition-all duration-200 hover:shadow-lg">
                                <CardContent className="p-8">
                                    <div className="space-y-6">
                                        <div>
                                            <Label className="text-lg mb-2">Contenido</Label>
                                            <div className="mt-2 border rounded-lg overflow-hidden">
                                                <Toolbar editor={commentEditor} />
                                                <EditorContent
                                                    editor={commentEditor}
                                                    className="prose max-w-none p-4"
                                                />
                                            </div>
                                            {errors.original_content && (
                                                <p className="text-sm text-red-600 mt-2">{errors.original_content}</p>
                                            )}
                                        </div>

                                        <div className="flex justify-center">
                                            <Button
                                                type="button"
                                                disabled={commentProcessing}
                                                onClick={handleCommentSubmit}
                                                className="px-8 py-2"
                                            >
                                                Publicar comentario
                                            </Button>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    )}
                </div>
            </div>
        </Layout>
    );
}
