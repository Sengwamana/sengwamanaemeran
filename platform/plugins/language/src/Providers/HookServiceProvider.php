<?php

namespace Botble\Language\Providers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\ServiceProvider;
use Botble\Language\Facades\Language;
use Botble\Language\Models\Language as LanguageModel;
use Botble\Language\Models\LanguageMeta;
use Botble\Menu\Models\Menu;
use Botble\Setting\Forms\GeneralSettingForm;
use Botble\Table\CollectionDataTable;
use Botble\Table\EloquentDataTable;
use Botble\Theme\Events\RenderingThemeOptionSettings;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_action(BASE_ACTION_META_BOXES, [$this, 'addLanguageBox'], 50, 2);
        add_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, [$this, 'addCurrentLanguageEditingAlert'], 55, 2);
        add_action(BASE_ACTION_BEFORE_EDIT_CONTENT, [$this, 'getCurrentAdminLanguage'], 55, 2);

        add_filter(FILTER_SLUG_PREFIX, [$this, 'setSlugPrefix'], 500);
        add_filter(LANGUAGE_FILTER_SWITCHER, [$this, 'languageSwitcher'], 50, 2);
        add_filter(BASE_FILTER_BEFORE_GET_FRONT_PAGE_ITEM, [$this, 'checkItemLanguageBeforeShow'], 50, 2);
        add_filter(BASE_FILTER_BEFORE_GET_SINGLE, [$this, 'getRelatedDataForOtherLanguage'], 50, 2);

        add_filter(BASE_FILTER_GET_LIST_DATA, [$this, 'addLanguageColumn'], 50, 2);
        add_filter(BASE_FILTER_TABLE_HEADINGS, [$this, 'addLanguageTableHeading'], 50, 2);

        add_filter(BASE_FILTER_TABLE_BUTTONS, [$this, 'addLanguageSwitcherToTable'], 247, 2);
        add_filter(BASE_FILTER_TABLE_QUERY, [$this, 'getDataByCurrentLanguage'], 157);
        add_filter(BASE_FILTER_BEFORE_GET_ADMIN_LIST_ITEM, [$this, 'checkItemLanguageBeforeGetAdminListItem'], 50);

        add_filter(BASE_FILTER_SITE_LANGUAGE_DIRECTION, fn () => Language::getCurrentLocaleRTL() ? 'rtl' : 'ltr', 1);
        add_filter(MENU_FILTER_NODE_URL, [$this, 'updateMenuNodeUrl'], 1);

        $this->app['events']->listen(RenderingThemeOptionSettings::class, function (): void {
            add_filter('theme-options-action-meta-boxes', [$this, 'addLanguageMetaBoxForThemeOptionsAndWidgets'], 55, 2);
        });

        add_filter('widget-top-meta-boxes', [$this, 'addLanguageMetaBoxForThemeOptionsAndWidgets'], 55, 2);
        add_filter('setting_email_template_meta_boxes', [$this, 'settingEmailTemplateMetaBoxes'], 55, 2);
        add_filter('payment_method_after_settings', [$this, 'settingEmailTemplateMetaBoxes'], 55, 2);
        add_filter('setting_email_template_path', [$this, 'settingEmailTemplatePath'], 55, 3);
        add_filter('setting_email_subject_key', [$this, 'settingEmailSubjectKey'], 55);
        add_filter('payment_setting_key', [$this, 'paymentSettingKey'], 55);

        FormAbstract::beforeRendering([$this, 'changeDataBeforeRenderingForm'], 1134);

        GeneralSettingForm::extend(function (GeneralSettingForm $form): void {
            $form
                ->addAfter(
                    'locale_direction',
                    'language_instruction',
                    HtmlField::class,
                    HtmlFieldOption::make()->view('plugins/language::forms.general-setting-form-label')
                );
        });

        add_filter('cms_language_flag', function (?string $flag, ?string $name = null) {
            if (! $name) {
                return $flag;
            }

            if ($languageFlag = Language::getActiveLanguage()->where('lang_name', $name)->value('lang_flag')) {
                return $languageFlag;
            }

            return $flag;
        }, 50, 2);

        add_filter('core_default_language', function (array $defaultLanguage) {
            $default = Language::getDefaultLanguage();

            if (! $default) {
                return $defaultLanguage;
            }

            return [
                'locale' => $default->lang_locale,
                'code' => $default->lang_code,
                'name' => $default->lang_name,
                'flag' => $default->lang_flag,
                'is_rtl' => $default->lang_is_rtl,
            ];
        }, 50);

        add_filter('core_available_locales', function (array $availableLocales) {
            $languages = Language::getActiveLanguage(['lang_locale', 'lang_code', 'lang_name', 'lang_flag', 'lang_is_rtl']);

            if ($languages->isEmpty()) {
                return $availableLocales;
            }

            $availableLocales = [];

            foreach ($languages as $language) {
                $key = $language->lang_locale;

                if (isset($availableLocales[$key])) {
                    $key = $language->lang_code;
                }

                $availableLocales[$key] = [
                    'locale' => $language->lang_locale,
                    'code' => $language->lang_code,
                    'name' => $language->lang_name,
                    'flag' => $language->lang_flag,
                    'is_rtl' => $language->lang_is_rtl,
                ];
            }

            return $availableLocales;
        }, 50);
    }

    public function settingEmailTemplateMetaBoxes(?string $data, array $params = []): string
    {
        $languages = Language::getActiveLanguage(['lang_id', 'lang_name', 'lang_code', 'lang_flag']);

        if ($languages->count() < 2) {
            return $data;
        }

        return $data . view('plugins/language::partials.admin-list-language-chooser', [
            'route' => 'settings.email.template.edit',
            'params' => $params,
            'languages' => $languages,
        ])->render();
    }

    public function settingEmailTemplatePath(string $path, string $module, string $templateKey): string
    {
        $currentLocale = is_in_admin(true) ? Language::getCurrentAdminLocale() : Language::getCurrentLocale();
        $locale = $currentLocale !== Language::getDefaultLocale() ? $currentLocale : null;

        if ($locale && in_array($locale, array_keys(Language::getSupportedLocales()))) {
            return "$module/$locale/$templateKey.tpl";
        }

        return $path;
    }

    public function paymentSettingKey(string $key): string
    {
        $currentLocale = is_in_admin(true) ? Language::getCurrentAdminLocale() : Language::getCurrentLocale();
        $locale = $currentLocale !== Language::getDefaultLocale() ? $currentLocale : null;

        if ($locale && in_array($locale, array_keys(Language::getSupportedLocales()))) {
            if (str_contains($key, 'name') || str_contains($key, 'description')) {
                return "{$key}_{$locale}";
            }
        }

        return $key;
    }

    public function settingEmailSubjectKey(string $key): string
    {
        $currentLocale = is_in_admin(true) ? Language::getCurrentAdminLocale() : Language::getCurrentLocale();
        $locale = $currentLocale !== Language::getDefaultLocale() ? $currentLocale : null;

        if ($locale && in_array($locale, array_keys(Language::getSupportedLocales()))) {
            return $key . '_' . $locale;
        }

        return $key;
    }

    public function addLanguageBox(string $priority, array|string|Model|null $object = null): void
    {
        if ($object instanceof BaseModel && in_array($object::class, Language::supportedModels())) {
            MetaBox::addMetaBox(
                'language_wrap',
                trans('plugins/language::language.name'),
                [$this, 'languageMetaField'],
                $object::class,
                'top'
            );
        }
    }

    public function addLanguageMetaBoxForThemeOptionsAndWidgets(?string $data, string $screen): ?string
    {
        if (! in_array($screen, [THEME_OPTIONS_MODULE_SCREEN_NAME, WIDGET_MANAGER_MODULE_SCREEN_NAME])) {
            return $data;
        }

        $languages = Language::getActiveLanguage(['lang_id', 'lang_name', 'lang_code', 'lang_flag']);

        if ($languages->count() < 2) {
            return $data;
        }

        $params = [];

        if ($screen === THEME_OPTIONS_MODULE_SCREEN_NAME) {
            $params = ['id' => Route::current()->parameter('id')];
        }

        return $data . view(
            'plugins/language::partials.admin-list-language-chooser',
            compact('languages', 'params')
        )->render();
    }

    public function setSlugPrefix(string $prefix): string
    {
        if (is_in_admin()) {
            $currentLocale = Language::getCurrentAdminLocale();
        } else {
            $currentLocale = Language::getCurrentLocale();
        }

        if ($currentLocale && (! setting('language_hide_default') || $currentLocale != Language::getDefaultLocale())) {
            if (! $prefix) {
                return $currentLocale;
            }

            if ($prefix === $currentLocale || Str::contains($prefix, $currentLocale . '/')) {
                return $prefix;
            }

            return $currentLocale . '/' . $prefix;
        }

        return $prefix;
    }

    public function languageMetaField(): ?string
    {
        $languages = Language::getActiveLanguage([
            'lang_code',
            'lang_flag',
            'lang_name',
        ]);

        if ($languages->isEmpty()) {
            return null;
        }

        $related = [];
        $args = func_get_args();

        $referenceId = null;
        $langMetaOrigin = null;
        $langMetaCode = null;

        if (($reference = $args[0]) && $reference->id) {
            $referenceId = $reference->id;
            $langMetaCode = $reference->languageMeta?->lang_meta_code;
            $langMetaOrigin = $reference->languageMeta?->lang_meta_origin;
        } elseif ($refFrom = Language::getRefFrom()) {
            $langMetaOrigin = LanguageMeta::query()
                ->where([
                    'reference_id' => $refFrom,
                    'reference_type' => $reference::class,
                ])
                ->value('lang_meta_origin');

            $referenceId = $refFrom;
            $langMetaCode = Language::getRefLang();
        }

        if ($referenceId && $langMetaOrigin) {
            $related = Language::getRelatedLanguageItem($referenceId, $langMetaOrigin);
        }

        $currentLanguage = self::checkCurrentLanguage($languages, $langMetaCode);

        if (! $currentLanguage) {
            $currentLanguage = Language::getDefaultLanguage([
                'lang_flag',
                'lang_name',
                'lang_code',
            ]);
        }

        $route = $this->getRoutes();

        $queryParams = request()->query();

        $queryParams[Language::refFromKey()] = ! empty($args[0]) && $args[0]->id ? $args[0]->id : 0;

        return view(
            'plugins/language::language-box',
            compact('args', 'languages', 'currentLanguage', 'related', 'route', 'queryParams')
        )->render();
    }

    public function checkCurrentLanguage(array|EloquentCollection $languages, ?string $value): ?LanguageModel
    {
        $refLang = Language::getRefLang();

        $currentLanguage = null;
        foreach ($languages as $language) {
            if ($value && $language->lang_code == $value) {
                $currentLanguage = $language;

                break;
            } elseif ($refLang && $language->lang_code === $refLang) {
                $currentLanguage = $language;

                break;
            } elseif ($language->lang_is_default) {
                $currentLanguage = $language;

                break;
            }
        }

        if (empty($currentLanguage)) {
            foreach ($languages as $language) {
                if ($language->lang_is_default) {
                    $currentLanguage = $language;

                    break;
                }
            }
        }

        return $currentLanguage;
    }

    protected function getRoutes(): array
    {
        $currentRoute = implode('.', explode('.', Route::currentRouteName(), -1));

        return apply_filters(LANGUAGE_FILTER_ROUTE_ACTION, [
            'create' => $currentRoute . '.create',
            'edit' => $currentRoute . '.edit',
        ]);
    }

    public function addCurrentLanguageEditingAlert(Request $request, $data = null): void
    {
        if (
            $data instanceof BaseModel &&
            in_array($data::class, Language::supportedModels()) &&
            Language::getActiveLanguage()->count() > 1) {
            $code = Language::getCurrentAdminLocaleCode();
            if (empty($code)) {
                $code = $this->getCurrentAdminLanguage($request, $data);
            }

            $language = null;
            if (! empty($code) && is_string($code)) {
                Language::setCurrentAdminLocale($code);
                $language = LanguageModel::query()->where('lang_code', $code)->value('lang_name');
            }

            echo view('plugins/language::partials.notification', compact('language'))->render();
        }

        echo null;
    }

    public function getCurrentAdminLanguage(Request $request, ?Model $data = null): ?string
    {
        $code = null;
        if ($refLang = Language::getRefLang()) {
            $code = $refLang;
        } elseif (! empty($data) && $data->getKey()) {
            $code = $data->languageMeta?->lang_meta_code;
        }

        if (empty($code) || ! is_string($code)) {
            $code = Language::getDefaultLocaleCode();
        }

        Language::setCurrentAdminLocale($code);

        return $code;
    }

    public function addLanguageTableHeading(array $headings, string|Model $model): array
    {
        if (
            $model instanceof BaseModel &&
            in_array($model::class, Language::supportedModels()) &&
            ($countLanguage = count(Language::getActiveLanguage())) &&
            $countLanguage > 1 &&
            $countLanguage < 4
        ) {
            if (is_in_admin() && Auth::guard()->check() && ! Auth::guard()->user()->hasAnyPermission($this->getRoutes())) {
                return $headings;
            }

            return array_merge($headings, Language::getTableHeading());
        }

        return $headings;
    }

    public function addLanguageColumn(
        EloquentDataTable|CollectionDataTable $data,
        string|Model $model
    ): EloquentDataTable|CollectionDataTable {
        if (
            $model instanceof BaseModel &&
            in_array($model::class, Language::supportedModels()) &&
            ($countLanguage = count(Language::getActiveLanguage())) &&
            $countLanguage > 1 &&
            $countLanguage < 4
        ) {
            $route = $this->getRoutes();

            if (is_in_admin() && Auth::guard()->check() && ! Auth::guard()->user()->hasAnyPermission($route)) {
                return $data;
            }

            return $data->addColumn('language', function ($item) use ($route) {
                $relatedLanguages = [];

                $currentLanguage = $item->languageMeta?->lang_meta_code;

                if ($languageMetaOrigin = $item->languageMeta?->lang_meta_origin) {
                    $relatedLanguages = Language::getRelatedLanguageItem($item->id, $languageMetaOrigin);
                }

                $languages = Language::getActiveLanguage();
                $data = '';

                foreach ($languages as $language) {
                    if ($language->lang_code == $currentLanguage) {
                        $data .= view('plugins/language::partials.status.active', compact('route', 'item'))->render();
                    } else {
                        $added = false;
                        if (! empty($relatedLanguages)) {
                            foreach ($relatedLanguages as $key => $relatedLanguage) {
                                if ($key == $language->lang_code) {
                                    $data .= view(
                                        'plugins/language::partials.status.edit',
                                        compact('route', 'relatedLanguage')
                                    )->render();
                                    $added = true;
                                }
                            }
                        }

                        if (! $added) {
                            $data .= view(
                                'plugins/language::partials.status.plus',
                                compact('route', 'item', 'language')
                            )->render();
                        }
                    }
                }

                return view('plugins/language::partials.language-column', compact('data'))->render();
            });
        }

        return $data;
    }

    public function languageSwitcher(string $switcher, array $options = []): string
    {
        return view('plugins/language::partials.switcher', compact('options'))->render();
    }

    public function checkItemLanguageBeforeShow(Builder|Model $data): Builder|Model
    {
        return $this->getDataByCurrentLanguageCode($data, Language::getCurrentLocaleCode());
    }

    protected function getDataByCurrentLanguageCode(Builder|Model $data, ?string $languageCode): Builder|Model
    {
        $model = $data->getModel();

        if (
            $model instanceof BaseModel &&
            in_array($model::class, Language::supportedModels()) &&
            ! empty($languageCode) &&
            ! $model instanceof LanguageModel &&
            ! $model instanceof LanguageMeta &&
            Language::getCurrentAdminLocaleCode() !== 'all'
        ) {
            Language::initModelRelations();

            return $data
                ->whereHas('languageMeta', function (Builder $query) use ($languageCode): void {
                    $query->where('lang_meta_code', $languageCode);
                });
        }

        return $data;
    }

    public function checkItemLanguageBeforeGetAdminListItem(Builder|Model $data): Builder|Model
    {
        return $this->getDataByCurrentLanguageCode($data, Language::getCurrentAdminLocaleCode());
    }

    public function getRelatedDataForOtherLanguage(Collection|Builder $query, ?Model $model)
    {
        if (! setting('language_show_default_item_if_current_version_not_existed', true) || is_in_admin()) {
            return $query;
        }

        if ($query instanceof Builder) {
            $model = $query->getModel();
        }

        if (
            $model instanceof BaseModel &&
            in_array($model::class, Language::supportedModels()) &&
            ! $model instanceof LanguageModel &&
            ! $model instanceof LanguageMeta
        ) {
            $data = $query->first();

            if (! empty($data)) {
                $current = $data->languageMeta;

                if ($current) {
                    Language::setCurrentAdminLocale($current->lang_meta_code);
                    if ($current->lang_meta_code != Language::getCurrentLocaleCode()) {
                        if (
                            ! setting('language_show_default_item_if_current_version_not_existed', 1) &&
                            ! $model instanceof Menu
                        ) {
                            return $data;
                        }

                        $referenceId = LanguageMeta::query()
                            ->where('lang_meta_origin', $current->lang_meta_origin)
                            ->where('reference_id', '!=', $data->getKey())
                            ->where('reference_type', $model::class)
                            ->where('lang_meta_code', Language::getCurrentLocaleCode())
                            ->value('reference_id');

                        if ($referenceId && $result = $model->where('id', $referenceId)) {
                            return $result;
                        }
                    }
                }
            }
        }

        return $query;
    }

    public function addLanguageSwitcherToTable(array $buttons, string $model): array
    {
        if (
            in_array($model, Language::supportedModels())
            && ($countLanguage = count(Language::getActiveLanguage()))
            && $countLanguage > 1
        ) {
            $activeLanguages = Language::getActiveLanguage(['lang_code', 'lang_name', 'lang_flag']);
            $languageButtons = [];
            $currentLanguage = Language::getCurrentAdminLocaleCode();

            foreach ($activeLanguages as $item) {
                $languageButtons[] = [
                    'className' => 'change-data-language-item ' . ($item->lang_code === $currentLanguage ? 'active' : ''),
                    'text' => Html::tag(
                        'span',
                        $item->lang_name,
                        ['data-href' => route('languages.change.data.language', $item->lang_code)]
                    )->toHtml(),
                ];
            }

            $languageButtons[] = [
                'className' => 'change-data-language-item ' . ($currentLanguage == 'all' ? 'active' : ''),
                'text' => Html::tag(
                    'span',
                    trans('plugins/language::language.show_all'),
                    ['data-href' => route('languages.change.data.language', 'all')]
                )->toHtml(),
            ];

            $flag = $activeLanguages->where('lang_code', $currentLanguage)->first();

            if (! empty($flag)) {
                $flag = language_flag($flag->lang_flag, $flag->lang_name);
            } else {
                $flag = BaseHelper::renderIcon('ti ti-flag');
            }

            $language = [
                'language' => [
                    'extend' => 'collection',
                    'text' => $flag . Html::tag('span', trans('plugins/language::language.change_language'), attributes: ['class' => 'ms-1'])->toHtml(),
                    'buttons' => $languageButtons,
                ],
            ];

            $buttons = array_merge($buttons, $language);
        }

        return $buttons;
    }

    public function getDataByCurrentLanguage(Builder $query): Builder
    {
        $model = $query->getModel();

        if (
            $model instanceof BaseModel
            && in_array($model::class, Language::supportedModels())
            && ($countLanguage = count(Language::getActiveLanguage()))
            && $countLanguage > 1
            && ($languageCode = Language::getCurrentAdminLocaleCode())
            && $languageCode !== 'all'
        ) {
            Language::initModelRelations();

            return $query
                ->whereHas('languageMeta', function (Builder $query) use ($languageCode): void {
                    $query->where('lang_meta_code', $languageCode);
                });
        }

        return $query;
    }

    public function changeDataBeforeRenderingForm(FormAbstract $form): FormAbstract
    {
        $model = $form->getModel();

        if (
            $model instanceof BaseModel &&
            is_in_admin() &&
            Language::getCurrentAdminLocaleCode() != Language::getDefaultLocaleCode() &&
            in_array($model::class, Language::supportedModels())
        ) {
            $refLang = Language::getRefLang();
            $refFrom = Language::getRefFrom();

            if ($refLang && $refFrom && $model instanceof Builder) {
                $model = $model->getModel()->find($refFrom);

                if ($model) {
                    $form->setupModel($model->replicate());
                }
            }
        }

        return $form;
    }

    public function updateMenuNodeUrl(?string $value): string
    {
        if (is_in_admin() || in_array($value, ['#', 'javascript:void(0)'])) {
            return $value;
        }

        return filter_var($value, FILTER_VALIDATE_URL) ? $value : Language::localizeURL($value);
    }
}
