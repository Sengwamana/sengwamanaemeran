<?php

namespace Botble\Newsletter\Forms;

use Botble\Base\Facades\BaseHelper;
use Botble\Newsletter\Facades\Newsletter as NewsletterFacade;
use Botble\Newsletter\Http\Requests\Settings\NewsletterSettingRequest;
use Botble\Setting\Forms\SettingForm;
use Exception;
use Illuminate\Support\Arr;

class NewsletterSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $mailchimpContactList = [];

        if (setting('newsletter_mailchimp_api_key')) {
            try {
                $contacts = collect(NewsletterFacade::driver('mailchimp')->contacts());

                if (! setting('newsletter_mailchimp_list_id')) {
                    setting()->set(['newsletter_mailchimp_list_id' => Arr::get($contacts, 'id')])->save();
                }

                $mailchimpContactList = $contacts->pluck('name', 'id')->all();
            } catch (Exception $exception) {
                BaseHelper::logError($exception);
            }
        }

        $sendGridContactList = [];

        if (setting('newsletter_sendgrid_api_key')) {
            try {
                $contacts = collect(NewsletterFacade::driver('sendgrid')->contacts());

                if (! setting('newsletter_sendgrid_list_id')) {
                    setting()->set(['newsletter_sendgrid_list_id' => Arr::get($contacts->first(), 'id')])->save();
                }

                $sendGridContactList = $contacts->pluck('name', 'id')->all();
            } catch (Exception $exception) {
                BaseHelper::logError($exception);
            }
        }

        $this
            ->setSectionTitle(trans('plugins/newsletter::newsletter.settings.title'))
            ->setSectionDescription(trans('plugins/newsletter::newsletter.settings.description'))
            ->setValidatorClass(NewsletterSettingRequest::class)
            ->add('newsletter_contacts_list_api_fields', 'html', [
                'html' => view('plugins/newsletter::partials.newsletter-contacts-list-api-fields', compact('mailchimpContactList', 'sendGridContactList')),
                'wrapper' => [
                    'class' => 'mb-0',
                ],
            ]);
    }
}
