<?php

namespace FriendsOfBotble\Comment\Forms\Settings;

use Botble\Base\Facades\Html;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\FormAbstract;
use Botble\Captcha\Facades\Captcha;
use Botble\Setting\Forms\SettingForm;
use FriendsOfBotble\Comment\Http\Requests\Settings\CommentSettingRequest;
use FriendsOfBotble\Comment\Support\CommentHelper;

class CommentSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setValidatorClass(CommentSettingRequest::class)
            ->setSectionTitle(trans('plugins/fob-comment::comment.settings.title'))
            ->setSectionDescription(trans('plugins/fob-comment::comment.settings.description'))
            ->when(is_plugin_active('captcha'), function (FormAbstract $form): void {
                $form->add(
                    'fob_comment_enable_recaptcha',
                    OnOffCheckboxField::class,
                    OnOffFieldOption::make()
                        ->label(trans('plugins/fob-comment::comment.settings.form.enable_recaptcha'))
                        ->value(setting('fob_comment_enable_recaptcha', false))
                        ->when(! Captcha::isEnabled(), function (OnOffFieldOption $option) {
                            return $option->helperText(
                                trans(
                                    'plugins/fob-comment::comment.settings.form.enable_recaptcha_help',
                                    ['url' => Html::link(
                                        route('captcha.settings'),
                                        trans('plugins/fob-comment::comment.settings.form.captcha_setting_label')
                                    )]
                                )
                            );
                        })
                        ->toArray()
                );
            })
            ->add(
                'fob_comment_comment_moderation',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-comment::comment.settings.form.comment_moderation'))
                    ->helperText(trans('plugins/fob-comment::comment.settings.form.comment_moderation_help'))
                    ->value(CommentHelper::commentMustBeModerated())
                    ->toArray()
            )
            ->add(
                'fob_comment_show_comment_cookie_consent',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-comment::comment.settings.form.show_comment_cookie_consent'))
                    ->value(CommentHelper::isShowCommentCookieConsent())
                    ->toArray()
            )
            ->add(
                'fob_comment_auto_fill_comment_form',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-comment::comment.settings.form.auto_fill_comment_form'))
                    ->value(CommentHelper::isAutoFillCommentForm())
                    ->helperText(trans('plugins/fob-comment::comment.settings.form.auto_fill_comment_form_help'))
                    ->toArray()
            )
            ->add(
                'fob_comment_comment_order',
                RadioField::class,
                RadioFieldOption::make()
                    ->label(trans('plugins/fob-comment::comment.settings.form.comment_order'))
                    ->helperText('Choose the preferred order for displaying comments in the list.')
                    ->choices([
                        'asc' => trans('plugins/fob-comment::comment.settings.form.comment_order_choices.asc'),
                        'desc' => trans('plugins/fob-comment::comment.settings.form.comment_order_choices.desc'),
                    ])
                    ->selected(CommentHelper::getCommentOrder())
                    ->toArray()
            )
            ->add(
                'fob_comment_display_admin_badge',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-comment::comment.settings.form.display_admin_badge'))
                    ->value(CommentHelper::isDisplayAdminBadge())
                    ->toArray()
            )
            ->add(
                'fob_comment_show_admin_role_name_for_admin_badge',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-comment::comment.settings.form.show_admin_role_name_for_admin_badge'))
                    ->helperText(trans('plugins/fob-comment::comment.settings.form.show_admin_role_name_for_admin_badge_helper'))
                    ->value(setting('fob_comment_show_admin_role_name_for_admin_badge', 'true'))
                    ->toArray()
            );
    }
}
