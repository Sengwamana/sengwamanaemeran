<?php

namespace Botble\Contact\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\Contact\Enums\ContactStatusEnum;
use Botble\Contact\Http\Requests\EditContactRequest;
use Botble\Contact\Models\Contact;

class ContactForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::addScriptsDirectly('vendor/core/plugins/contact/js/contact.js')
            ->addStylesDirectly('vendor/core/plugins/contact/css/contact.css');

        $this
            ->model(Contact::class)
            ->setValidatorClass(EditContactRequest::class)
            ->add(
                'status',
                SelectField::class,
                StatusFieldOption::make()
                    ->choices(ContactStatusEnum::labels())
            )
            ->setBreakFieldPoint('status')
            ->addMetaBoxes([
                'information' => [
                    'title' => trans('plugins/contact::contact.contact_information'),
                    'content' => view('plugins/contact::contact-info', ['contact' => $this->getModel()])->render(),
                ],
                'replies' => [
                    'title' => trans('plugins/contact::contact.replies'),
                    'content' => view('plugins/contact::reply-box', ['contact' => $this->getModel()])->render(),
                ],
            ]);
    }
}
