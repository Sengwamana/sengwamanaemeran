<?php

use Botble\Base\Facades\Html;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Forms\WidgetForm;

class SocialLinkWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Social Links'),
            'description' => __('Display social links.'),
        ]);
    }

    protected function settingForm(): WidgetForm|string|null
    {
        return WidgetForm::createFromArray($this->getConfig())
            ->add(
                'description',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(
                        __('Go to :link to manage social links.', [
                            'link' => Html::link(route('theme.options', 'opt-text-subsection-social-links'), __('Social Links')),
                        ])
                    )
            );
    }
}
