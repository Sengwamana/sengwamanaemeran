<?php

namespace Botble\Setting\Forms;

use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Setting\Http\Requests\CacheSettingRequest;

class CacheSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setUrl(route('settings.cache.update'))
            ->setSectionTitle(trans('core/setting::setting.cache.title'))
            ->setSectionDescription(trans('core/setting::setting.cache.description'))
            ->setValidatorClass(CacheSettingRequest::class)
            ->add(
                'cache_admin_menu_enable',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/setting::setting.cache.form.cache_admin_menu'))
                    ->helperText(trans('core/setting::setting.cache.form.cache_admin_menu_helper'))
                    ->value(setting('cache_admin_menu_enable', false))
            )
            ->add(
                'cache_front_menu_enabled',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/setting::setting.cache.form.cache_front_menu'))
                    ->helperText(trans('core/setting::setting.cache.form.cache_front_menu_helper'))
                    ->value(setting('cache_front_menu_enabled', true))
            )
            ->add(
                'cache_user_avatar_enabled',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/setting::setting.cache.form.cache_user_avatar'))
                    ->helperText(trans('core/setting::setting.cache.form.cache_user_avatar_helper'))
                    ->value(setting('cache_user_avatar_enabled', true))
            )
            ->add(
                'enable_cache_site_map',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/setting::setting.cache.form.enable_cache_site_map'))
                    ->helperText(trans('core/setting::setting.cache.form.enable_cache_site_map_helper', ['url' => url('sitemap.xml')]))
                    ->value($targetValue = setting('enable_cache_site_map', true))
            )
            ->addOpenCollapsible('enable_cache_site_map', '1', $targetValue)
            ->add(
                'cache_time_site_map',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('core/setting::setting.cache.form.cache_time_site_map'))
                    ->value(setting('cache_time_site_map', 60))
            )
            ->addCloseCollapsible('enable_cache_site_map', '1');
    }
}
