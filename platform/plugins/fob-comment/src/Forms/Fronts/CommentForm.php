<?php

namespace FriendsOfBotble\Comment\Forms\Fronts;

use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Models\BaseModel;
use Botble\Captcha\Forms\Fields\ReCaptchaField;
use Botble\Theme\FormFront;
use FriendsOfBotble\Comment\Http\Requests\Fronts\CommentRequest;
use FriendsOfBotble\Comment\Support\CommentHelper;
use Illuminate\Support\Arr;

class CommentForm extends FormFront
{
    protected static ?BaseModel $reference = null;

    public function setup(): void
    {
        $preparedData = CommentHelper::preparedDataForFill();

        $this
            ->contentOnly()
            ->setFormOption('class', 'fob-comment-form')
            ->setUrl(route('fob-comment.public.comments.store'))
            ->setValidatorClass(CommentRequest::class)
            ->columns()
            ->when(
                $this->getReference(),
                function (FormAbstract $form, BaseModel $reference): void {
                    $form
                        ->add('reference_id', 'hidden', ['value' => $reference->getKey()])
                        ->add('reference_type', 'hidden', ['value' => $reference::class]);
                },
                fn (FormAbstract $form) => $form->add('reference_url', 'hidden', ['value' => url()->current()])
            )
            ->add(
                'content',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/fob-comment::comment.common.comment'))
                    ->required()
                    ->colspan(2)
                    ->toArray()
            )
            ->add(
                'name',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-comment::comment.common.name'))
                    ->when(
                        Arr::get($preparedData, 'name'),
                        fn (TextFieldOption $option, $value) => $option->defaultValue($value)->disabled(),
                        fn (TextFieldOption $option) => $option->required()
                    )
                    ->colspan(1)
                    ->toArray()
            )
            ->add(
                'email',
                EmailField::class,
                EmailFieldOption::make()->label(trans('plugins/fob-comment::comment.common.email'))
                    ->when(
                        Arr::get($preparedData, 'email'),
                        fn (EmailFieldOption $option, $value) => $option->defaultValue($value)->disabled(),
                        fn (EmailFieldOption $option) => $option->required()
                    )
                    ->colspan(1)
                    ->toArray()
            )
            ->add(
                'website',
                TextField::class,
                TextFieldOption::make()->label(trans('plugins/fob-comment::comment.common.website'))
                    ->colspan(2)
                    ->when(
                        Arr::get($preparedData, 'website'),
                        fn (TextFieldOption $option, $value) => $option->defaultValue($value)->disabled()
                    )
                    ->toArray()
            )
            ->when(
                CommentHelper::isEnableReCaptcha(),
                fn (FormAbstract $form) => $form->add('recaptcha', ReCaptchaField::class)
            )
            ->when(CommentHelper::isShowCommentCookieConsent(), function (FormAbstract $form): void {
                $form->add(
                    'cookie_consent',
                    OnOffCheckboxField::class,
                    OnOffFieldOption::make()
                        ->label(trans('plugins/fob-comment::comment.front.form.cookie_consent'))
                        ->colspan(2)
                        ->toArray()
                );
            })
            ->setFormEndKey('button')
            ->add('button', 'submit', [
                'label' => trans('plugins/fob-comment::comment.front.form.submit'),
                'attr' => [
                    'class' => 'btn btn-primary mb-4',
                ],
                'colspan' => 2,
            ]);
    }

    public static function createWithReference(BaseModel $model): FormAbstract
    {
        static::$reference = $model;

        return app(FormBuilder::class)->create(static::class);
    }

    public static function getReference(): ?BaseModel
    {
        return static::$reference;
    }
}
