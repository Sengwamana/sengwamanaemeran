<?php

namespace Botble\ACL\Notifications;

use Botble\Base\Facades\EmailHandler;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class ResetPasswordNotification extends Notification
{
    public string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $emailHandler = EmailHandler::setModule('acl')
            ->setTemplate('password-reminder')
            ->setType('core')
            ->addTemplateSettings('acl', config('core.acl.email', []))
            ->setVariableValue('reset_link', route('access.password.reset', ['token' => $this->token, 'email' => request()->input('email')]));

        return (new MailMessage())
            ->view(['html' => new HtmlString($emailHandler->getContent())])
            ->subject($emailHandler->getSubject());
    }
}
