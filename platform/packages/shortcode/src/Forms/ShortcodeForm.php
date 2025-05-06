<?php

namespace Botble\Shortcode\Forms;

use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Models\BaseModel;
use Botble\Shortcode\Forms\Fields\ShortcodeTabsField;

class ShortcodeForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(BaseModel::class)
            ->contentOnly()
            ->addCustomField('tabs', ShortcodeTabsField::class);
    }

    public function renderForm(array $options = [], bool $showStart = false, bool $showFields = true, bool $showEnd = false): string
    {
        return parent::renderForm($options, $showStart, $showFields, $showEnd);
    }

    public function withLazyLoading(bool $lazy = true): static
    {
        self::beforeRendering(function (self $form) use ($lazy) {
            if (! $lazy) {
                $form->remove('enable_lazy_loading');

                return $this;
            }

            $form->add(
                'enable_lazy_loading',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Enable lazy loading'))
                    ->choices([
                        'no' => __('No'),
                        'yes' => __('Yes'),
                    ])
                    ->helperText(__('When enabled, shortcode content will be loaded sequentially as the page loads, rather than all at once. This can help improve page load times.'))
            );

            return $this;
        });

        return $this;
    }
}
