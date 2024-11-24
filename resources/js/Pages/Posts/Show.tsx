import { Head, router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import GuestLayout from '@/Layouts/GuestLayout';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardTitle } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';
import { FaThumbsUp } from "react-icons/fa6";
import { RiChatFollowUpFill } from "react-icons/ri";
import { useEffect, useState } from 'react';
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
                <div className="flex items-center justify-between space-x-4">
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
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <Card>
                        <CardTitle className="px-6 pt-6 text-xl">{post.title}</CardTitle>
                        <CardContent className="p-6">
                            <div className="prose max-w-none mb-4">
                                {post.content}
                            </div>

                            <div className="flex items-center gap-4 text-gray-500">
                                <div className="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs">
                                    {post.category}
                                </div>
                                <div>• {post.createdAt.charAt(0).toUpperCase() + post.createdAt.slice(1)}</div>
                                <div>• Por un vecino de la comunidad</div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Comments Section */}
                    <div className="mt-8 space-y-4">
                        <h3 className="text-lg font-semibold text-gray-900 mb-4">
                            Comentarios ({Array.isArray(comments) ? comments.length : 0})
                        </h3>

                        {Array.isArray(comments) && comments.length > 0 ? (
                            comments.map((comment) => (
                                <Card key={comment.id} className="ml-8">
                                    <CardContent className="p-4">
                                        <div className="prose max-w-none mb-4">
                                            {comment.content}
                                        </div>

                                        <div className="flex items-center gap-4 text-gray-500 text-sm">
                                            <div className="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs">
                                                Comentario
                                            </div>
                                            <div>• {comment.createdAt.charAt(0).toUpperCase() + comment.createdAt.slice(1)}</div>
                                            <div>• Por un administrador de la comunidad</div>
                                        </div>
                                    </CardContent>
                                </Card>
                            ))
                        ) : (
                            <p className="text-gray-500 text-center">No hay comentarios aún</p>
                        )}
                    </div>

                    {auth.user && (auth.roles.is_manager || auth.roles.is_admin) && (
                        <Card className="mt-8 space-y-4">
                            <CardContent className="p-6">
                                <h3 className="text-lg font-semibold mb-4">Añadir un comentario</h3>
                                <div className="space-y-4">
                                    <div>
                                        <Label>Contenido</Label>
                                        <div className="mt-1 border rounded-md">
                                            <Toolbar editor={commentEditor} />
                                            <EditorContent
                                                editor={commentEditor}
                                                className="prose max-w-none p-4"
                                            />
                                        </div>
                                        {errors.original_content && (
                                            <p className="text-sm text-red-600 mt-1">{errors.original_content}</p>
                                        )}
                                    </div>

                                    <Button
                                        type="button"
                                        disabled={commentProcessing}
                                        onClick={handleCommentSubmit}
                                    >
                                        Publicar comentario
                                    </Button>
                                </div>
                            </CardContent>
                        </Card>
                    )}
                </div>
            </div>
        </Layout >
    );
} 