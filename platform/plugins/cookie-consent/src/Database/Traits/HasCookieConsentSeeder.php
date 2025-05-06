<?php

namespace Botble\CookieConsent\Database\Traits;

use Botble\Base\Facades\Html;

trait HasCookieConsentSeeder
{
    public function getCookieConsentPageName(): string
    {
        return 'Cookie Policy';
    }

    public function getCookieConsentPageContent(): string
    {
        return Html::tag('h3', 'EU Cookie Consent') .
            Html::tag(
                'p',
                'To use this website we are using Cookies and collecting some Data. To be compliant with the EU GDPR we give you to choose if you allow us to use certain Cookies and to collect some Data.'
            ) .
            Html::tag('h4', 'Essential Data') .
            Html::tag(
                'p',
                'The Essential Data is needed to run the Site you are visiting technically. You can not deactivate them.'
            ) .
            Html::tag(
                'p',
                '- Session Cookie: PHP uses a Cookie to identify user sessions. Without this Cookie the Website is not working.'
            ) .
            Html::tag(
                'p',
                '- XSRF-Token Cookie: Laravel automatically generates a CSRF "token" for each active user session managed by the application. This token is used to verify that the authenticated user is the one actually making the requests to the application.'
            );
    }
}
