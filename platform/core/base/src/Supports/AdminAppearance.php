<?php

namespace Botble\Base\Supports;

use Botble\ACL\Contracts\HasPreferences;
use Botble\Base\Facades\Html;
use Botble\Setting\Facades\Setting;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

class AdminAppearance
{
    protected string $settingKey = 'admin_appearance';

    protected Authenticatable|HasPreferences $user;

    public function forUser(Authenticatable|HasPreferences $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function forCurrentUser(string $guard = null): static
    {
        $auth = auth($guard);

        if ($auth->check()) {
            $this->user = $auth->user();
        }

        return $this;
    }

    public function getLocale(): string
    {
        $generalLocale = config('core.base.general.locale', config('app.locale'));

        return $this->getUserSetting('locale', $generalLocale);
    }

    public function getLocaleDirection(): string
    {
        return $this->getUserSetting('locale_direction', setting('admin_locale_direction', 'ltr'));
    }

    public function getCurrentLayout(): string
    {
        return $this->getSetting('layout', array_key_first($this->getLayouts()));
    }

    public function isVerticalLayout(): bool
    {
        return $this->getCurrentLayout() === 'vertical';
    }

    public function isHorizontalLayout(): bool
    {
        return $this->getCurrentLayout() === 'horizontal';
    }

    public function showMenuItemIcon(): bool
    {
        return $this->getSetting('show_menu_item_icon', true);
    }

    public function getLayouts(): array
    {
        return [
            'vertical' => trans('core/setting::setting.admin_appearance.vertical'),
            'horizontal' => trans('core/setting::setting.admin_appearance.horizontal'),
        ];
    }

    public function getContainerWidth(): string
    {
        return $this->getSetting('container_width', array_key_first($this->getContainerWidths()));
    }

    public function getContainerWidths(): array
    {
        return [
            'container-xl' => trans('core/setting::setting.admin_appearance.container_width.default'),
            'container-3xl' => trans('core/setting::setting.admin_appearance.container_width.large'),
            'container-fluid' => trans('core/setting::setting.admin_appearance.container_width.full'),
        ];
    }

    public function getSetting(string $key, mixed $default = null)
    {
        return Setting::get($this->getSettingKey($key), $default);
    }

    public function getUserSetting(string $key, mixed $default = null)
    {
        $setting = null;

        if (isset($this->user) && $this->user->getAuthIdentifier() && $this->user instanceof HasPreferences) {
            $setting = $this->user->getMeta($key);
        }

        return $setting ?: $this->getSetting($key, $default);
    }

    public function getSettingKey(string $key): string
    {
        return "{$this->settingKey}_{$key}";
    }

    public function setSetting(string|array $key, mixed $value = null): void
    {
        $data = is_array($key) ? $key : [$key => $value];

        foreach ($data as $k => $v) {
            Setting::set("{$this->getSettingKey($k)}", $v);
        }

        Setting::save();
    }

    public function getCustomCSS(): string
    {
        $css = $this->getSetting('custom_css');

        if (empty($css)) {
            return '';
        }

        return Html::tag('style', $css);
    }

    public function getCustomJS(string $location): string
    {
        $js = $this->getSetting('custom_' . $location . '_js');

        if (empty($js)) {
            return '';
        }

        if ((! Str::contains($js, '<script') || ! Str::contains($js, '</script>')) && ! Str::contains($js, '<noscript') && ! Str::contains($js, '</noscript>')) {
            $js = Html::tag('script', $js);
        }

        return $js;
    }
}
