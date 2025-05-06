<?php

namespace Botble\Language\Listeners;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Language\Listeners\Concerns\EnsureThemePackageExists;
use Botble\Language\Models\Language;
use Botble\Widget\Models\Widget;

class CopyThemeWidgets
{
    use EnsureThemePackageExists;

    public function handle(CreatedContentEvent $event): void
    {
        if (! $this->determineIfThemesExists()) {
            return;
        }

        if (! $event->data instanceof Language) {
            return;
        }

        $theme = setting('theme');

        if (! $theme) {
            return;
        }

        $copiedWidgets = Widget::query()
            ->where('theme', $theme)
            ->get()
            ->toArray();

        if (empty($copiedWidgets)) {
            return;
        }

        foreach ($copiedWidgets as $key => $widget) {
            $copiedWidgets[$key]['theme'] = $theme . '-' . $event->data->lang_code;
            $copiedWidgets[$key]['data'] = json_encode($widget['data']);
            unset($copiedWidgets[$key]['id']);
        }

        Widget::query()->insertOrIgnore($copiedWidgets);
    }
}
