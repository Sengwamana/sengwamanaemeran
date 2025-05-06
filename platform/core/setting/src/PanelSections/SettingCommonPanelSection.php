<?php

namespace Botble\Setting\PanelSections;

use Botble\Base\PanelSections\PanelSection;
use Botble\Base\PanelSections\PanelSectionItem;

class SettingCommonPanelSection extends PanelSection
{
    public function setup(): void
    {
        $this
            ->setId('settings.common')
            ->setTitle(trans('core/setting::setting.panel.common'))
            ->withItems([
                PanelSectionItem::make('general')
                    ->setTitle(trans('core/setting::setting.panel.general'))
                    ->withIcon('ti ti-settings')
                    ->withDescription(trans('core/setting::setting.panel.general_description'))
                    ->withPriority(-9999)
                    ->withRoute('settings.general'),
                PanelSectionItem::make('email')
                    ->setTitle(trans('core/setting::setting.panel.email'))
                    ->withIcon('ti ti-mail')
                    ->withDescription(trans('core/setting::setting.panel.email_description'))
                    ->withPriority(-9990)
                    ->withRoute('settings.email'),
                PanelSectionItem::make('email_templates')
                    ->setTitle(trans('core/setting::setting.email.email_templates'))
                    ->withIcon('ti ti-mail-code')
                    ->withDescription(trans('core/setting::setting.email.email_templates_description'))
                    ->withPriority(-9980)
                    ->withRoute('settings.email.template')
                    ->withPermission('settings.email'),
                PanelSectionItem::make('email_rules')
                    ->setTitle(trans('core/setting::setting.email.email_rules'))
                    ->withIcon('ti ti-mail-check')
                    ->withDescription(trans('core/setting::setting.email.email_rules_description'))
                    ->withPriority(-9970)
                    ->withRoute('settings.email.rules')
                    ->withPermission('settings.email'),
                PanelSectionItem::make('media')
                    ->setTitle(trans('core/setting::setting.panel.media'))
                    ->withIcon('ti ti-folder')
                    ->withDescription(trans('core/setting::setting.panel.media_description'))
                    ->withPriority(10)
                    ->withRoute('settings.media'),
                PanelSectionItem::make('admin_appearance')
                    ->setTitle(trans('core/setting::setting.admin_appearance.title'))
                    ->withIcon('ti ti-palette')
                    ->withDescription(trans('core/setting::setting.admin_appearance.description'))
                    ->withPriority(110)
                    ->withRoute('settings.admin-appearance'),
                PanelSectionItem::make('cache')
                    ->setTitle(trans('core/setting::setting.cache.title'))
                    ->withIcon('ti ti-box')
                    ->withDescription(trans('core/setting::setting.cache.description'))
                    ->withPriority(120)
                    ->withRoute('settings.cache'),
                PanelSectionItem::make('datatables')
                    ->setTitle(trans('core/setting::setting.datatable.title'))
                    ->withIcon('ti ti-table-options')
                    ->withDescription(trans('core/setting::setting.datatable.description'))
                    ->withPriority(130)
                    ->withRoute('settings.datatables'),
            ]);
    }
}
