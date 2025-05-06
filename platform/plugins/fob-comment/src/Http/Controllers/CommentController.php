<?php

namespace FriendsOfBotble\Comment\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use FriendsOfBotble\Comment\Forms\CommentForm;
use FriendsOfBotble\Comment\Http\Requests\CommentRequest;
use FriendsOfBotble\Comment\Models\Comment;
use FriendsOfBotble\Comment\Tables\CommentTable;

class CommentController extends BaseController
{
    public function index(CommentTable $commentTable)
    {
        $this->pageTitle(trans('plugins/fob-comment::comment.title'));

        return $commentTable->renderTable();
    }

    public function edit(Comment $comment)
    {
        $this->pageTitle(trans('plugins/fob-comment::comment.edit_comment'));

        return CommentForm::createFromModel($comment)->renderForm();
    }

    public function update(Comment $comment, CommentRequest $request)
    {
        CommentForm::createFromModel($comment)
            ->onlyValidatedData()
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousRoute('fob-comment.comments.index')
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Comment $comment)
    {
        return DeleteResourceAction::make($comment);
    }
}
