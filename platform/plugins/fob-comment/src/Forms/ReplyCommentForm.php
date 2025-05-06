<?php

namespace FriendsOfBotble\Comment\Forms;

use Botble\Base\Forms\FieldOptions\EditorFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Theme\FormFront;
use FriendsOfBotble\Comment\Http\Requests\ReplyCommentRequest;

class ReplyCommentForm extends FormFront
{
    public function setup(): void
    {
        $this
            ->contentOnly()
            ->setFormOption('id', 'reply-comment-form')
            ->setValidatorClass(ReplyCommentRequest::class)
            ->add(
                'content',
                EditorField::class,
                EditorFieldOption::make()
                    ->addAttribute('without-buttons', true)
                    ->toArray()
            );
    }
}
