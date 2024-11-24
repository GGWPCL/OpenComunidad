import { useEffect, useState } from 'react';
import { useForm } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';
import { FaThumbsUp } from "react-icons/fa6";

interface UpVoteProps {
    postId: number;
    initialVotes: number;
    initialIsUpVoted: boolean;
}

export default function UpVote({ postId, initialVotes, initialIsUpVoted }: UpVoteProps) {
    const { data, setData, post: postForm } = useForm<{ shouldUpVote?: boolean }>({});
    const [votes, setVotes] = useState<number>(initialVotes);
    const [isUpVoted, setIsUpVoted] = useState<boolean>(initialIsUpVoted);

    useEffect(() => {
        if (data.shouldUpVote !== undefined) {
            handleUpVote(data.shouldUpVote);
        }
    }, [data]);

    const handleUpVote = (shouldUpVote: boolean) => {
        postForm(route('posts.up_vote', { post: postId }), {
            data: { shouldUpVote },
            preserveScroll: true,
            onSuccess: () => {
                let newVotes = votes;
                if (!isUpVoted && shouldUpVote) {
                    newVotes++;
                }
                if (isUpVoted && !shouldUpVote) {
                    newVotes--;
                }

                setVotes(newVotes);
                setIsUpVoted(shouldUpVote);
            }
        });
    };

    return (
        <Button
            variant="ghost"
            size="sm"
            onClick={() => setData({ shouldUpVote: !isUpVoted })}
        >
            <div className="flex items-center space-x-1">
                <FaThumbsUp
                    className={isUpVoted ? 'text-primary' : 'text-gray-500'}
                />
                <span>{votes}</span>
            </div>
        </Button>
    );
} 