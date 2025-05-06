<?php

namespace Botble\Translation\PanelSections;

use Botble\Base\PanelSections\PanelSection;
use Botble\Base\PanelSections\PanelSectionItem;

class LocalizationPanelSection extends PanelSection
{
    public function setup(): void
    {
        $this
            ->setId('others.localization')
            ->setTitle(trans('plugins/translation::translation.localization'))
            ->withPriority(1000)
            ->withItems([
                PanelSectionItem::make('localization.locales')
                    ->setTitle(trans('plugins/translation::translation.panel.locales.title'))
                    ->withIcon('ti ti-world-download')
                    ->withDescription(trans('plugins/translation::translation.panel.locales.description'))
                    ->withPriority(10)
                    ->withRoute('translations.locales'),
                PanelSectionItem::make('localization.theme_translation')
                    ->setTitle(trans('plugins/translation::translation.panel.theme-translations.title'))
                    ->withIcon('ti ti-language')
                    ->withDescription(trans('plugins/translation::translation.panel.theme-translations.description'))
                    ->withPriority(20)
                    ->withRoute('translations.theme-translations'),
                PanelSectionItem::make('localization.other_translation')
                    ->setTitle(trans('plugins/translation::translation.panel.admin-translations.title'))
                    ->withIcon('ti ti-message-language')
                    ->withDescription(trans('plugins/translation::translation.panel.admin-translations.description'))
                    ->withPriority(30)
                    ->withRoute('translations.index'),
            ]);
    }
}
