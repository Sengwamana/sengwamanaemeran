<?php

namespace Database\Seeders\Themes\Write;

use Botble\Page\Models\Page;
use Botble\Widget\Widgets\CoreSimpleMenu;
use ContactInformationWidget;
use Database\Seeders\Themes\Main\WidgetSeeder as MainWidgetSeeder;
use LanguageSwitcherWidget;
use SiteLogoWidget;
use SocialLinkWidget;

class WidgetSeeder extends MainWidgetSeeder
{
    public function getData(): array
    {
        $footerMenuPages = Page::query()
            ->whereIn('name', ['Home', 'Services', 'Portfolio', 'Pricing', 'Blog', 'Contact'])
            ->select('id', 'name')
            ->get();

        return [
            [
                'widget_id' => ContactInformationWidget::class,
                'sidebar_id' => 'sidebar_panel_sidebar',
                'position' => 1,
                'data' => [
                    'bio' => "I'm always excited to take on new projects and collaborate with innovative minds.",
                    'details' => collect([
                        'Phone' => '+1 234 567 890',
                        'Email' => 'contact@botble.com',
                        'Website' => 'https://botble.com',
                        'Address' => '123 Main Street, New York, NY 10001',
                    ])
                        ->map(fn ($value, $key) => [
                            ['key' => 'label', 'value' => $key],
                            ['key' => 'value', 'value' => $value],
                        ])
                        ->all(),
                ],
            ],
            [
                'widget_id' => SocialLinkWidget::class,
                'sidebar_id' => 'sidebar_panel_sidebar',
                'position' => 2,
                'data' => [
                    'name' => 'Social',
                ],
            ],
            [
                'widget_id' => LanguageSwitcherWidget::class,
                'sidebar_id' => 'sidebar_panel_sidebar',
                'position' => 3,
                'data' => [],
            ],
            [
                'widget_id' => ContactInformationWidget::class,
                'sidebar_id' => 'primary_sidebar',
                'position' => 1,
                'data' => [
                    'image' => $this->filePath('general/avatar.png'),
                    'signature' => $this->filePath('general/signature.png'),
                    'details' => collect([
                        ['icon' => 'ti ti-phone', 'label' => 'Phone', 'value' => '+1 234 567 890', 'url' => 'tel:+1234567890'],
                        ['icon' => 'ti ti-mail', 'label' => 'Email', 'value' => 'contact@botble.com', 'url' => 'mailto:contact@botble.com'],
                        ['icon' => 'ti ti-world', 'label' => 'Website', 'value' => 'botble.com', 'url' => 'https://botble.com'],
                        ['icon' => 'ti ti-map', 'label' => 'Address', 'value' => '123 Main Street, New York, NY 10001'],
                    ])
                        ->map(fn ($item) => [
                            ['key' => 'label', 'value' => $item['label']],
                            ['key' => 'value', 'value' => $item['value']],
                            ['key' => 'icon', 'value' => $item['icon']],
                            ['key' => 'url', 'value' => $item['url'] ?? null],
                        ])
                        ->all(),
                    'display_social_links' => true,
                ],
            ],
            [
                'widget_id' => SiteLogoWidget::class,
                'sidebar_id' => 'footer_sidebar',
                'position' => 1,
                'data' => [],
            ],
            [
                'widget_id' => CoreSimpleMenu::class,
                'sidebar_id' => 'footer_sidebar',
                'position' => 2,
                'data' => [
                    'id' => CoreSimpleMenu::class,
                    'items' => collect($footerMenuPages)->map(fn ($item) => [
                        ['key' => 'label', 'value' => $item->name],
                        ['key' => 'url', 'value' => $item->url],
                    ])->all(),
                ],
            ],
            [
                'widget_id' => SocialLinkWidget::class,
                'sidebar_id' => 'footer_sidebar',
                'position' => 3,
                'data' => [
                    'name' => 'Social',
                ],
            ],
        ];
    }
}
