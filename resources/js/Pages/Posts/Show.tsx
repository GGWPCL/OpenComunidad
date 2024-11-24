import { Head, router, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import GuestLayout from '@/Layouts/GuestLayout';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardTitle } from '@/Components/ui/card';
import { Label } from '@/Components/ui/label';
import { useState, useEffect } from 'react';
import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Toolbar from './Toolbar';
import { Markdown } from 'tiptap-markdown';
import UpVote from '@/Components/UpVote';
import FollowPost from '@/Components/FollowPost';
import { motion } from 'framer-motion';
import { cn } from '@/lib/utils';

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
    isApproved: boolean;
}

interface Comment {
    id: number;
    content: string;
    author: number;
    createdAt: string;
}

interface PollOption {
    id: number;
    text: string;
    votes: number;
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
    poll?: {
        question: string;
        options: PollOption[];
        total_votes: number;
        closed: boolean;
        deadline: string;
    };
    comments: Comment[];
    community: {
        name: string;
        slug: string;
        isAdmin: boolean;
    };
}

const calculateTimeLeft = (endDate?: string) => {
    if (!endDate) {
        return null;
    }

    const difference = new Date(endDate).getTime() - new Date().getTime();

    if (difference <= 0) {
        return null;
    }

    return {
        days: Math.floor(difference / (1000 * 60 * 60 * 24)),
        hours: Math.floor((difference / (1000 * 60 * 60)) % 24),
        minutes: Math.floor((difference / 1000 / 60) % 60),
        seconds: Math.floor((difference / 1000) % 60)
    };
};

export default function Show({ auth, post, poll, comments, community }: Props) {

    const [postState, setPostState] = useState<Post>(post);
    const [selectedOption, setSelectedOption] = useState<number | null>(null);
    const [hasVoted, setHasVoted] = useState(false);
    const [timeLeft, setTimeLeft] = useState(calculateTimeLeft(poll?.deadline));

    const isAdmin = (usePage().props as { community?: { isAdmin: boolean } }).community?.isAdmin;

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
                router.reload();
            },
        });
    };

    const Layout = auth.user ? AuthenticatedLayout : GuestLayout;


    const handleVote = (optionId: number) => {
        if (!hasVoted) {
            setSelectedOption(optionId);
            setHasVoted(true);
        }
    };

    useEffect(() => {
        if (!poll?.closed) {
            const timer = setInterval(() => {
                const remainingTime = calculateTimeLeft(poll?.deadline);
                setTimeLeft(remainingTime);

                if (!remainingTime) {
                    clearInterval(timer);
                }
            }, 1000);

            return () => clearInterval(timer);
        }
    }, [poll?.deadline, poll?.closed]);

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
                                {!post.isApproved && (
                                    <div className="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                                        Pendiente de aprobación
                                    </div>
                                )}
                                <div>•</div>
                                <div>{post.createdAt.charAt(0).toUpperCase() + post.createdAt.slice(1)}</div>
                                <div>•</div>
                                <div>Por un vecino de la comunidad</div>
                            </div>

                            {poll && (
                                <Card className="mb-8 mt-8">
                                    <CardContent className="p-6">
                                        <h3 className="text-xl font-bold mb-4">{poll?.question}</h3>
                                        <div className="space-y-3">
                                            {poll?.options.map((option) => {
                                                const percentage = (option.votes / poll!.total_votes) * 100;
                                                const showResults = poll?.closed || (auth.user && auth.roles.is_admin);

                                                return (
                                                    <div
                                                        key={option.id}
                                                        className={cn(
                                                            "relative cursor-pointer rounded-lg border p-4 transition-colors",
                                                            !poll?.closed && !hasVoted ? "hover:bg-gray-50" : "cursor-default",
                                                            selectedOption === option.id && "border-blue-500 bg-blue-50"
                                                        )}
                                                        onClick={() => !poll?.closed && handleVote(option.id)}
                                                    >
                                                        <div className="relative z-10 flex justify-between">
                                                            <span>{option.text}</span>
                                                            {showResults && (
                                                                <span className="font-medium">
                                                                    {percentage.toFixed(1)}%
                                                                </span>
                                                            )}
                                                        </div>
                                                        {showResults && (
                                                            <motion.div
                                                                initial={{ width: 0 }}
                                                                animate={{ width: `${percentage}%` }}
                                                                transition={{ duration: 0.5, ease: "easeOut" }}
                                                                className="absolute left-0 top-0 h-full bg-blue-100 rounded-lg"
                                                                style={{ zIndex: 0 }}
                                                            />
                                                        )}
                                                    </div>
                                                );
                                            })}
                                        </div>
                                        <div className="mt-4 text-sm text-gray-500 text-center">
                                            {poll?.closed ? (
                                                "Esta encuesta está cerrada"
                                            ) : hasVoted ? (
                                                <>
                                                    <div>{`${poll?.total_votes} votos totales`}</div>
                                                    <div className="mt-1">
                                                        {timeLeft ? (
                                                            <div className="font-medium">
                                                                Tiempo restante: {timeLeft.days > 0 ? `${timeLeft.days}d ` : ''}
                                                                {String(timeLeft.hours).padStart(2, '0')}:
                                                                {String(timeLeft.minutes).padStart(2, '0')}:
                                                                {String(timeLeft.seconds).padStart(2, '0')}
                                                            </div>
                                                        ) : (
                                                            "La votación ha terminado"
                                                        )}
                                                    </div>
                                                </>
                                            ) : (
                                                <>
                                                    <div>Haga clic en una opción para votar</div>
                                                    <div className="mt-1">
                                                        {timeLeft ? (
                                                            <div className="font-medium">
                                                                Tiempo restante: {timeLeft.days > 0 ? `${timeLeft.days}d ` : ''}
                                                                {String(timeLeft.hours).padStart(2, '0')}:
                                                                {String(timeLeft.minutes).padStart(2, '0')}:
                                                                {String(timeLeft.seconds).padStart(2, '0')}
                                                            </div>
                                                        ) : (
                                                            "La votación ha terminado"
                                                        )}
                                                    </div>
                                                </>
                                            )}
                                        </div>
                                    </CardContent>
                                </Card>
                            )}
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
