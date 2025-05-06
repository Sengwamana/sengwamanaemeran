<?php

namespace FriendsOfBotble\Comment\Http\Controllers\Fronts;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\Helper;
use FriendsOfBotble\Comment\Actions\CreateNewComment;
use FriendsOfBotble\Comment\Actions\GetCommentReference;
use FriendsOfBotble\Comment\Enums\CommentStatus;
use FriendsOfBotble\Comment\Http\Requests\Fronts\CommentReferenceRequest;
use FriendsOfBotble\Comment\Http\Requests\Fronts\CommentRequest;
use FriendsOfBotble\Comment\Models\Comment;
use FriendsOfBotble\Comment\Support\CommentHelper;
use Illuminate\Database\Eloquent\Builder;

class CommentController extends BaseController
{
    public function index(CommentReferenceRequest $request, GetCommentReference $getCommentReference)
    {
        $reference = new BaseModel();

        if ($request->input('reference_type')) {
            $reference = $getCommentReference($request->input('reference_type'), $request->input('reference_id'));

            $query = Comment::query()
                ->where('reference_id', $reference->getKey())
                ->where('reference_type', $reference::class);
        } else {
            $query = Comment::query()
                ->where('reference_url', $request->input('reference_url'));
        }

        $query
            ->where(function (Builder $query): void {
                $query
                    ->where('status', CommentStatus::APPROVED)
                    ->orWhere(function (Builder $query): void {
                        $query->where('status', CommentStatus::PENDING)
                            ->where('ip_address', Helper::getIpFromThirdParty());
                    });
            })
            ->where('reply_to', null)
            ->with(['replies'])
            ->orderBy('created_at', CommentHelper::getCommentOrder());

        $comments = apply_filters('fob_comment_list_query', $query, $request)->paginate(10);

        $count = CommentHelper::getCommentsCount($reference);

        $view = apply_filters('fob_comment_list_view_path', 'plugins/fob-comment::partials.list');

        return $this
            ->httpResponse()
            ->setData([
                'title' => trans_choice('plugins/fob-comment::comment.front.list.title', $count, ['count' => $count]),
                'html' => view($view, compact('comments'))->render(),
                'comments' => $comments,
            ]);
    }

    public function store(
        CommentRequest $request,
        CreateNewComment $createNewComment,
        GetCommentReference $getCommentReference
    ) {
        $data = [
            ...$request->validated(),
            'reference_url' => $request->input('reference_url') ?? url()->previous(),
        ];

        $reference = new BaseModel();

        if ($request->input('reference_type')) {
            $reference = $getCommentReference($request->input('reference_type'), $request->input('reference_id'));

            abort_if($reference->getMetaData('allow_comments', true) == '0', 404);
        }

        $createNewComment($reference, $data);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/fob-comment::comment.front.comment_success_message'));
    }
}
