<?php

namespace FriendsOfBotble\Comment\Actions;

use Botble\Base\Contracts\BaseModel;
use Botble\Base\Facades\AdminHelper;
use Botble\Base\Supports\Helper;
use FriendsOfBotble\Comment\Enums\CommentStatus;
use FriendsOfBotble\Comment\Models\Comment;
use FriendsOfBotble\Comment\Support\CommentHelper;
use Illuminate\Http\Request;

class CreateNewComment
{
    public function __construct(protected Request $request)
    {

    }

    public function __invoke(BaseModel $reference, array $data, ?Comment $replyTo = null)
    {
        $data = [
            ...$data,
            'ip_address' => Helper::getIpFromThirdParty(),
            'user_agent' => $this->request->userAgent(),
            'status' => $this->getStatus(),
            'reference_id' => $reference->getKey(),
            'reference_type' => $reference::class,
        ];

        if ($author = CommentHelper::getAuthorizedUser()) {
            $data['author_id'] ??= $author->getKey();
            $data['author_type'] ??= $author::class;
        }

        Comment::query()->create([
            ...$data,
            'reply_to' => $replyTo ? ($replyTo->reply_to ?: $replyTo->getKey()) : null,
        ]);
    }

    protected function getStatus(): string
    {
        if (AdminHelper::isInAdmin() && auth()->check()) {
            return CommentStatus::APPROVED;
        }

        return CommentHelper::commentMustBeModerated() ? CommentStatus::PENDING : CommentStatus::APPROVED;
    }
}
