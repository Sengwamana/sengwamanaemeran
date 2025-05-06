<?php

use Botble\Base\Facades\Html;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Forms\WidgetForm;

class LanguageSwitcherWidget extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Language Switcher'),
            'description' => __('Display language switcher.'),
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
                        __('Go to :link to manage languages.', [
                            'link' => Html::link(route('languages.index'), __('Languages')),
                        ])
                    )
            );
    }

    protected function requiredPlugins(): array
    {
        return ['language'];
    }
}
