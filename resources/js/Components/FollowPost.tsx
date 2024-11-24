import { useEffect, useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { RiChatFollowUpFill } from "react-icons/ri";

interface FollowPostProps {
    postId: number;
    initialIsFollowed: boolean;
}

export default function FollowPost({ postId, initialIsFollowed }: FollowPostProps) {
    const { data, setData, post: postForm } = useForm<{ shouldFollow?: boolean }>({});
    const [isFollowed, setIsFollowed] = useState<boolean>(initialIsFollowed);

    useEffect(() => {
        if (data.shouldFollow !== undefined) {
            handleFollow(data.shouldFollow);
        }
    }, [data]);

    const handleFollow = (shouldFollow: boolean) => {
        postForm(route('posts.follow', { post: postId }), {
            data: { shouldFollow },
            preserveScroll: true,
            onSuccess: () => {
                setIsFollowed(shouldFollow);
            }
        });
    };

    return (
        <Button
            variant="ghost"
            size="sm"
            onClick={() => setData({ shouldFollow: !isFollowed })}
        >
            <div className="flex items-center space-x-1">
                <RiChatFollowUpFill
                    className={isFollowed ? 'text-primary' : 'text-gray-500'}
                />
                <span>{isFollowed ? 'Siguiendo' : 'Seguir'}</span>
            </div>
        </Button>
    );
} 