<?php

namespace FriendsOfBotble\Comment\Http\Controllers\Fronts;

use Botble\Base\Http\Controllers\BaseController;
use FriendsOfBotble\Comment\Actions\CreateNewComment;
use FriendsOfBotble\Comment\Enums\CommentStatus;
use FriendsOfBotble\Comment\Http\Requests\Fronts\ReplyCommentRequest;
use FriendsOfBotble\Comment\Models\Comment;

class ReplyCommentController extends BaseController
{
    public function __invoke(string|int $comment, ReplyCommentRequest $request, CreateNewComment $createNewComment)
    {
        $comment = Comment::query()
            ->where('status', CommentStatus::APPROVED)
            ->with('reference')
            ->findOrFail($comment);

        $createNewComment($comment->reference, $request->validated(), $comment);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/fob-comment::comment.front.comment_success_message'));
    }
}
