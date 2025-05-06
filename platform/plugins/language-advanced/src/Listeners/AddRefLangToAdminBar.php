<?php

namespace Botble\LanguageAdvanced\Listeners;

use Botble\Language\Facades\Language;
use Botble\Theme\Facades\AdminBar;

class AddRefLangToAdminBar
{
    public function handle(): void
    {
        if (Language::getDefaultLocaleCode() === Language::getCurrentLocaleCode()) {
            return;
        }

        $groups = AdminBar::getLinksNoGroup();

        foreach ($groups as &$group) {
            $group['link'] .= sprintf('?%s=%s', Language::refLangKey(), Language::getCurrentLocaleCode());
        }

        AdminBar::setLinksNoGroup($groups);
    }
}
