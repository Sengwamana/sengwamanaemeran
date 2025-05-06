<?php

namespace Botble\Contact\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Contact\Models\Contact;
use Botble\Contact\Models\ContactReply;
use Botble\Contact\Models\CustomField;
use Botble\Contact\Models\CustomFieldOption;
use Botble\Contact\Repositories\Eloquent\ContactReplyRepository;
use Botble\Contact\Repositories\Eloquent\ContactRepository;
use Botble\Contact\Repositories\Interfaces\ContactInterface;
use Botble\Contact\Repositories\Interfaces\ContactReplyInterface;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use Illuminate\Http\Request;
use Illuminate\Routing\Events\RouteMatched;

class ContactServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(ContactInterface::class, function () {
            return new ContactRepository(new Contact());
        });

        $this->app->bind(ContactReplyInterface::class, function () {
            return new ContactReplyRepository(new ContactReply());
        });
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/contact')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions', 'email'])
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadMigrations()
            ->publishAssets();

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-contact')
                        ->priority(120)
                        ->name('plugins/contact::contact.menu')
                        ->icon('ti ti-mail')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-contact-list')
                        ->parentId('cms-plugins-contact')
                        ->priority(120)
                        ->name('plugins/contact::contact.name')
                        ->icon('ti ti-cube')
                        ->route('contacts.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-contact-custom-fields')
                        ->parentId('cms-plugins-contact')
                        ->priority(130)
                        ->name('plugins/contact::contact.custom_field.name')
                        ->icon('ti ti-cube-plus')
                        ->route('contacts.custom-fields.index')
                        ->permissions('contact.custom-fields')
                );
        });

        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('contact')
                    ->setTitle(trans('plugins/contact::contact.settings.title'))
                    ->withIcon('ti ti-mail-cog')
                    ->withPriority(140)
                    ->withDescription(trans('plugins/contact::contact.settings.description'))
                    ->withRoute('contact.settings')
            );
        });

        $this->app['events']->listen(RouteMatched::class, function (): void {
            EmailHandler::addTemplateSettings(CONTACT_MODULE_SCREEN_NAME, config('plugins.contact.email', []));
        });

        $this->app->booted(function (): void {
            $this->app->register(HookServiceProvider::class);
        });

        if (defined('LANGUAGE_MODULE_SCREEN_NAME') && defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
            LanguageAdvancedManager::registerModule(CustomField::class, [
                'name',
                'placeholder',
            ]);

            LanguageAdvancedManager::registerModule(CustomFieldOption::class, [
                'label',
            ]);

            LanguageAdvancedManager::addTranslatableMetaBox('contact-custom-field-options');

            add_action(LANGUAGE_ADVANCED_ACTION_SAVED, function ($data, $request): void {
                if ($data::class == CustomField::class) {
                    $customFieldOptions = $request->input('options', []) ?: [];

                    if (! $customFieldOptions) {
                        return;
                    }

                    $newRequest = new Request();

                    $newRequest->replace([
                        'language' => $request->input('language'),
                        'ref_lang' => $request->input('ref_lang'),
                    ]);

                    foreach ($customFieldOptions as $option) {
                        if (empty($option['id'])) {
                            continue;
                        }

                        $customFieldOption = CustomFieldOption::query()->find($option['id']);

                        if ($customFieldOption) {
                            $newRequest->merge([
                                'label' => $option['label'],
                                'value' => null,
                            ]);

                            LanguageAdvancedManager::save($customFieldOption, $newRequest);
                        }
                    }
                }
            }, 1234, 2);
        }
    }
}
