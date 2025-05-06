<?php

namespace FriendsOfBotble\Comment\Forms;

use Botble\Base\Forms\FieldOptions\EditorFieldOption;
use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use FriendsOfBotble\Comment\Enums\CommentStatus;
use FriendsOfBotble\Comment\Http\Requests\CommentRequest;
use FriendsOfBotble\Comment\Models\Comment;

class CommentForm extends FormAbstract
{
    public function setup(): void
    {
        $model = $this->getModel();

        $this
            ->model(Comment::class)
            ->setValidatorClass(CommentRequest::class)
            ->setBreakFieldPoint('status')
            ->add(
                'permalink',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(view('plugins/fob-comment::partials.permalink', compact('model')))
                    ->toArray()
            )
            ->add(
                'name',
                TextField::class,
                TextFieldOption::make()->label(trans('plugins/fob-comment::comment.common.name'))->toArray()
            )
            ->add(
                'email',
                EmailField::class,
                EmailFieldOption::make()->label(trans('plugins/fob-comment::comment.common.email'))->toArray()
            )
            ->add(
                'website',
                TextField::class,
                TextFieldOption::make()->label(trans('plugins/fob-comment::comment.url'))->toArray()
            )
            ->add(
                'content',
                EditorField::class,
                EditorFieldOption::make()
                    ->label(trans('plugins/fob-comment::comment.common.comment'))
                    ->rows(5)
                    ->addAttribute('without-buttons', true)
                    ->toArray()
            )
            ->add(
                'status',
                SelectField::class,
                StatusFieldOption::make()->choices(CommentStatus::labels())->toArray()
            )
            ->add(
                'created_at',
                TextField::class,
                TextFieldOption::make()->label(trans('plugins/fob-comment::comment.submitted_on'))->disabled()->toArray()
            );
    }
}
