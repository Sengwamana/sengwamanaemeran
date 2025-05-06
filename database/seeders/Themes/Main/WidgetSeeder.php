<?php

namespace Database\Seeders\Themes\Main;

use Botble\Page\Database\Traits\HasPageSeeder;
use Botble\Page\Models\Page;
use Botble\Theme\Database\Seeders\ThemeSeeder;
use Botble\Widget\Database\Traits\HasWidgetSeeder;
use Botble\Widget\Widgets\CoreSimpleMenu;
use ContactInformationWidget;
use LanguageSwitcherWidget;
use SiteCopyrightWidget;
use SiteLogoWidget;
use SocialLinkWidget;

class WidgetSeeder extends ThemeSeeder
{
    use HasWidgetSeeder;
    use HasPageSeeder;

    public function run(): void
    {
        $this->createWidgets($this->getData());
    }

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
                'widget_id' => SiteCopyrightWidget::class,
                'sidebar_id' => 'footer_sidebar',
                'position' => 3,
                'data' => [],
            ],
        ];
    }
}
