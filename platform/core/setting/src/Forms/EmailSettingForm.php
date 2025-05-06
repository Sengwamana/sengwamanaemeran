<?php

namespace Botble\Setting\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Setting\Http\Requests\EmailSettingRequest;

class EmailSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        Assets::addScriptsDirectly('vendor/core/core/setting/js/email.js');

        $mailer = old('email_driver', setting('email_driver', config('mail.default')));

        $this
            ->setUrl(route('settings.email.update'))
            ->setSectionTitle(trans('core/setting::setting.panel.email'))
            ->setSectionDescription(trans('core/setting::setting.panel.email_description'))
            ->contentOnly()
            ->setActionButtons(view('core/setting::partials.email.action-buttons', ['form' => $this->getFormOption('id')])->render())
            ->setValidatorClass(EmailSettingRequest::class)
            ->add(
                'email_driver',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('core/setting::setting.email.mailer'))
                    ->choices(apply_filters('core_email_mailers', [
                            'smtp' => 'SMTP',
                            'mailgun' => 'Mailgun',
                            'ses' => 'SES',
                            'postmark' => 'Postmark',
                            'log' => 'Log (for testing only, will not send email)',
                            'array' => 'Array (for testing only, will not send email)',
                        ] + (function_exists('proc_open') ? ['sendmail' => 'Sendmail'] : [])))
                ->selected($mailer)
                ->addAttribute('data-bb-toggle', 'collapse')
                ->addAttribute('data-bb-target', '.email-fields')
            )
            ->add(
                'mailer',
                HtmlField::class,
                HtmlFieldOption::make()->view('core/setting::partials.email.email-fields', compact('mailer')),
            )
            ->add(
                'email_from_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.email.sender_name'))
                    ->value(old('email_from_name', setting('email_from_name', config('mail.from.name'))))
                    ->placeholder(trans('core/setting::setting.email.sender_name_placeholder'))
                    ->maxLength(60)
            )
            ->add(
                'email_from_address',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.email.sender_email'))
                    ->value(old('email_from_address', setting('email_from_address', get_admin_email()->first())))
                    ->placeholder('admin@example.com')
                    ->maxLength(60)
                    ->wrapperAttributes([
                        'class' => 'mb-0',
                    ])
            );
    }
}
