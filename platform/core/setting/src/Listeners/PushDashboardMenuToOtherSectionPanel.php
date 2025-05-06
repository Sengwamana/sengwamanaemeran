<?php

namespace Botble\Setting\Listeners;

use Botble\Base\Events\PanelSectionsRendering;
use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Setting\PanelSections\SettingOthersPanelSection;

class PushDashboardMenuToOtherSectionPanel
{
    public function handle(PanelSectionsRendering $event): void
    {
        if ($event->panelSectionManager->getGroupId() !== 'settings') {
            return;
        }

        $menuItems = DashboardMenu::getItemsByParentId('cms-core-settings');

        foreach ($menuItems as $menuItem) {
            if (empty($menuItem['name'])) {
                continue;
            }

            if (! empty($menuItem['children'])) {
                foreach ($menuItem['children'] as $child) {
                    $this->registerPanel($child);
                }

                continue;
            }

            $this->registerPanel($menuItem);
        }
    }

    protected function registerPanel(array $menuItem): void
    {
        PanelSectionManager::default()
            ->registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make($menuItem['id'])
                    ->setTitle(trans($menuItem['name']))
                    ->withDescription(trans($menuItem['description'] ?? ''))
                    ->withIcon($menuItem['icon'] ?? 'ti ti-settings')
                    ->withPriority($menuItem['priority'] ?? 500)
                    ->withPermissions($menuItem['permissions'] ?? [])
                    ->withUrl($menuItem['url'] ?? '#')
            );
    }
}
