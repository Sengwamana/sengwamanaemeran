<?php

namespace FriendsOfBotble\Comment\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use FriendsOfBotble\Comment\Actions\CreateNewComment;
use FriendsOfBotble\Comment\Enums\CommentStatus;
use FriendsOfBotble\Comment\Http\Requests\ReplyCommentRequest;
use FriendsOfBotble\Comment\Models\Comment;

class ReplyCommentController extends BaseController
{
    public function __invoke(Comment $comment, CreateNewComment $createNewComment, ReplyCommentRequest $request)
    {
        $comment->loadMissing('reference');
        $user = $request->user();

        $createNewComment($comment->reference, [
            ...$request->validated(),
            'status' => CommentStatus::APPROVED,
            'author_type' => $user::class,
            'author_id' => $user->getKey(),
            'name' => $user->name,
            'email' => $user->email,
        ], $comment);

        return $this->httpResponse();
    }
}
