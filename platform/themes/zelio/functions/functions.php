<?php

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Forms\FieldOptions\CoreIconFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\CoreIconField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Media\Facades\RvMedia;
use Botble\Portfolio\Forms\Fronts\QuotationForm;
use Botble\Portfolio\Forms\PackageForm;
use Botble\Portfolio\Forms\ProjectForm;
use Botble\Portfolio\Forms\ServiceForm;
use Botble\Portfolio\Models\CustomField;
use Botble\Portfolio\Models\CustomFieldOption;
use Botble\Portfolio\Models\Package;
use Botble\Portfolio\Models\ServiceCategory;
use Botble\Portfolio\Tables\PackageTable;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\SiteMapManager;
use Botble\Theme\Facades\Theme;
use Botble\Theme\FormFrontManager;
use Botble\Theme\Supports\ThemeSupport;
use Botble\Theme\Typography\TypographyItem;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;

if (is_plugin_active('portfolio')) {
    SlugHelper::registering(function (): void {
        SlugHelper::removeModule([
            Package::class,
            ServiceCategory::class,
        ]);
    });

    SeoHelper::removeModule([
        Package::class,
        ServiceCategory::class,
    ]);

    if (is_plugin_active('language-advanced')) {
        LanguageAdvancedManager::removeModule([
            CustomField::class,
            CustomFieldOption::class,
        ]);
    }

    FormFrontManager::remove(QuotationForm::class);
}

app()->booted(function (): void {
    register_sidebar([
        'id' => 'footer_sidebar',
        'name' => __('Footer Sidebar'),
        'description' => __('Displays the site logo, quick links, and copyright info.'),
    ]);

    register_page_template([
        'sidebar' => __('Sidebar'),
        'has-heading' => __('Has Heading'),
    ]);

    register_sidebar([
        'id' => 'sidebar_panel_sidebar',
        'name' => __('Panel Sidebar'),
        'description' => __('Displays widgets inside a slide-out panel, shown on desktop.'),
    ]);

    RvMedia::addSize('post-thumbnail', 1200, 800);

    Theme::typography()
        ->registerFontFamilies([
            new TypographyItem('primary', __('Primary'), 'Playfair Display'),
            new TypographyItem('secondary', __('Secondary'), 'Urbanist'),
        ]);

    ThemeSupport::registerSocialLinks();
    ThemeSupport::registerSiteCopyright();
    ThemeSupport::registerSocialSharing();
    ThemeSupport::registerLazyLoadImages();

    function get_header_style(): int
    {
        $headerStyle = theme_option('header_style', 1);

        return in_array($headerStyle, range(1, 3)) ? $headerStyle : 1;
    }

    function get_footer_style(): int
    {
        $footerStyle = theme_option('footer_style', 1);

        return in_array($footerStyle, range(1, 3)) ? $footerStyle : 1;
    }

    if (is_plugin_active('portfolio')) {
        SiteMapManager::removeKey('packages');
        SiteMapManager::removeKey('service-categories');

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->removeItem([
                    'cms-core-portfolio-quotation-requests',
                    'cms-core-portfolio-custom-fields',
                ]);
        });

        Event::listen(RouteMatched::class, function (): void {
            EmailHandler::removeTemplateSettings('portfolio');
        });

        ProjectForm::extend(function (ProjectForm $form): void {
            $form
                ->remove(['author', 'place'])
                ->addBefore(
                    'name',
                    'category_ids',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(__('Category'))
                        ->multiple()
                        ->searchable()
                        ->choices(
                            ServiceCategory::query()
                                ->wherePublished()
                                ->pluck('name', 'id')
                                ->all()
                        )
                        ->required()
                        ->metadata()
                )
                ->addAfter(
                    'client',
                    'link',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Link'))
                        ->helperText(__('Link to the project'))
                        ->metadata()
                );
        });

        ServiceForm::extend(function (ServiceForm $form): void {
            $form->add(
                'icon',
                CoreIconField::class,
                CoreIconFieldOption::make()
                    ->label(__('Icon'))
                    ->metadata()
            );
        });

        PackageForm::extend(function (PackageForm $form): void {
            $form
                ->remove(['content', 'annual_price', 'is_popular'])
                ->remove('price')
                ->addBefore(
                    'duration',
                    'price',
                    TextField::class,
                    TextFieldOption::make()
                        ->required()
                        ->colspan(2)
                        ->label(trans('plugins/portfolio::portfolio.price'))
                        ->placeholder(trans('plugins/portfolio::portfolio.form.price_placeholder'))
                );
        });

        PackageTable::extend(function (PackageTable $table): void {
            $table->removeColumn('is_popular');
        });
    }

    add_filter('cms_installer_themes', function () {
        return [
            'design' => [
                'label' => 'Zelio - Designer',
                'image' => Theme::asset()->url('images/demos/home-design.png'),
            ],
            'code' => [
                'label' => 'Zelio - Developer',
                'image' => Theme::asset()->url('images/demos/home-code.png'),
            ],
            'write' => [
                'label' => 'Zelio - Writer',
                'image' => Theme::asset()->url('images/demos/home-write.png'),
            ],
        ];
    }, 10);
});
