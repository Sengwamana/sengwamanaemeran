<?php

namespace Botble\Theme\Supports;

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\FormHelper;
use Botble\Media\Facades\RvMedia;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Shortcode\Compilers\Shortcode;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Support\Http\Requests\Request;
use Botble\Theme\Events\RenderingThemeOptionSettings;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Facades\ThemeOption;
use Botble\Theme\Forms\Fields\ThemeIconField;
use Botble\Theme\Http\Requests\UpdateOptionsRequest;
use Botble\Theme\ThemeOption\Fields\ColorField;
use Botble\Theme\ThemeOption\Fields\IconField;
use Botble\Theme\ThemeOption\Fields\MediaImageField;
use Botble\Theme\ThemeOption\Fields\NumberField as NumberFieldOption;
use Botble\Theme\ThemeOption\Fields\RepeaterField;
use Botble\Theme\ThemeOption\Fields\SelectField;
use Botble\Theme\ThemeOption\ThemeOptionSection;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ThemeSupport
{
    public static function registerYoutubeShortcode(?string $viewPath = null): void
    {
        $viewPath = $viewPath ?: 'packages/theme::shortcodes';

        shortcode()
            ->register(
                'youtube-video',
                __('YouTube video'),
                __('Add YouTube video'),
                function ($shortcode) use ($viewPath) {
                    $url = Youtube::getYoutubeVideoEmbedURL($shortcode->content);
                    $width = $shortcode->width;
                    $height = $shortcode->height;

                    return view($viewPath . '.youtube', compact('url', 'width', 'height'))
                        ->render();
                }
            )
            ->setPreviewImage('youtube-video', asset('vendor/core/packages/theme/images/ui-blocks/youtube-video.jpg'))
            ->setAdminConfig('youtube-video', function ($attributes, $content) {
                return ShortcodeForm::createFromArray($attributes)
                    ->add('url', TextField::class, [
                        'label' => __('YouTube URL'),
                        'attr' => [
                            'placeholder' => 'https://www.youtube.com/watch?v=SlPhMPnQ58k ',
                            'data-shortcode-attribute' => 'content',
                        ],
                        'value' => $content,
                    ])
                    ->add('width', NumberField::class, [
                        'label' => __('Width'),
                    ])
                    ->add('height', NumberField::class, [
                        'label' => __('Height'),
                    ]);
            });
    }

    public static function registerGoogleMapsShortcode(?string $viewPath = null): void
    {
        $viewPath = $viewPath ?: 'packages/theme::shortcodes';

        shortcode()
            ->register(
                'google-map',
                __('Google Maps'),
                __('Add Google Maps iframe'),
                function (Shortcode $shortcode) use ($viewPath) {
                    $address = $shortcode->content;

                    if (! $address) {
                        return '';
                    }

                    $width = $shortcode->width ?: '100%';
                    $height = $shortcode->height ?: '500';

                    return view($viewPath . '.google-map', compact('address', 'width', 'height'))
                        ->render();
                }
            )
            ->setPreviewImage('google-map', asset('vendor/core/packages/theme/images/ui-blocks/google-map.jpg'))
            ->setAdminConfig('google-map', function (array $attributes, ?string $content) {
                return ShortcodeForm::createFromArray($attributes)
                    ->add('address', 'textarea', [
                        'label' => __('Address'),
                        'attr' => [
                            'data-shortcode-attribute' => 'content',
                            'placeholder' => '24 Roberts Street, SA73, Chester',
                            'rows' => 3,
                        ],
                        'value' => $content,
                    ])
                    ->add('width', NumberField::class, [
                        'label' => __('Width'),
                    ])
                    ->add('height', NumberField::class, [
                        'label' => __('Height'),
                        'default_value' => 500,
                    ]);
            });
    }

    public static function getCustomJS(string $location): string
    {
        $js = setting('custom_' . $location . '_js');

        if (empty($js)) {
            return '';
        }

        if ((! Str::contains($js, '<script') || ! Str::contains($js, '</script>')) && ! Str::contains(
            $js,
            '<noscript'
        ) && ! Str::contains($js, '</noscript>')) {
            $js = Html::tag('script', $js);
        }

        return $js;
    }

    public static function getCustomHtml(string $location): string
    {
        $html = setting('custom_' . $location . '_html');

        if (empty($html)) {
            return '';
        }

        return $html;
    }

    public static function insertBlockAfterTopHtmlTags(?string $block, ?string $html): ?string
    {
        if (! $block || ! $html) {
            return $html;
        }

        preg_match_all('/^<([a-z]+)([^>]+)*(?:>(.*)<\/\1>|\s+\/>)$/sm', $html, $matches);

        if (empty($matches[0])) {
            return $html;
        }

        $parsedHtml = '';

        foreach ($matches[0] as $blockItem) {
            $parsedHtml .= Str::replaceLast('</', $block . '</', $blockItem);
        }

        return $parsedHtml;
    }

    public static function registerPreloader(): void
    {
        add_filter(THEME_FRONT_HEADER, function (?string $html): string {
            if (theme_option('preloader_enabled', 'no') != 'yes') {
                return $html;
            }

            $preloader = null;

            if (theme_option('preloader_version', 'v1') === 'v1') {
                $preloader = view('packages/theme::fronts.preloader')->render();
            }

            return $html . apply_filters('theme_preloader', $preloader);
        }, 16);

        app('events')->listen(RenderingThemeOptionSettings::class, function (): void {
            theme_option()
                ->setField([
                    'id' => 'preloader_enabled',
                    'section_id' => 'opt-text-subsection-general',
                    'type' => 'customSelect',
                    'label' => __('Enable Preloader?'),
                    'attributes' => [
                        'name' => 'preloader_enabled',
                        'list' => [
                            'yes' => __('Yes'),
                            'no' => __('No'),
                        ],
                        'value' => 'no',
                        'options' => [
                            'class' => 'form-control',
                        ],
                    ],
                ])
                ->when(count(static::getPreloaderVersions()) > 1, function () {
                    return theme_option()
                        ->setField([
                            'id' => 'preloader_version',
                            'section_id' => 'opt-text-subsection-general',
                            'type' => 'customSelect',
                            'label' => __('Preloader Version'),
                            'attributes' => [
                                'name' => 'preloader_version',
                                'list' => static::getPreloaderVersions(),
                                'value' => 'v1',
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                        ]);
                });
        });
    }

    public static function getPreloaderVersions(): array
    {
        return apply_filters('theme_preloader_versions', [
            'v1' => __('Default'),
        ]);
    }

    public static function registerToastNotification(): void
    {
        add_filter(THEME_FRONT_FOOTER, function (?string $html): string {
            if (AdminHelper::isInAdmin()) {
                return $html;
            }

            $toastNotification = view('packages/theme::fronts.toast-notification')->render();

            return $html . apply_filters('theme_toast_notification', $toastNotification);
        }, 16);
    }

    public static function registerThemeIconFields(array $icons, array $css = [], array $js = []): void
    {
        app()->booted(function () use ($icons): void {
            app('form')->component(
                'themeIcon',
                $icons ? 'packages/theme::forms.fields.icons-field' : 'core/base::forms.partials.core-icon',
                [
                    'name',
                    'value' => null,
                    'attributes' => [],
                ]
            );

            add_filter('form_custom_fields', function (FormAbstract $form, FormHelper $formHelper) {
                if ($formHelper->hasCustomField('themeIcon')) {
                    return $form;
                }

                return $form->addCustomField('themeIcon', ThemeIconField::class);
            }, 29, 2);
        });

        if ($icons) {
            add_filter('theme_icon_js_code', function (?string $html) use ($css, $js) {
                $cssHtml = '';
                $jsHtml = '';

                foreach ($css as $cssItem) {
                    $cssHtml .= Html::style($cssItem)->toHtml();
                }

                foreach ($js as $jsItem) {
                    $jsHtml .= Html::style($jsItem)->toHtml();
                }

                return $html . $cssHtml . $jsHtml;
            });

            add_filter('theme_icon_list_icons', function (array $defaultIcons) use ($icons) {
                return array_merge($defaultIcons, $icons);
            });

            if ($css) {
                Assets::addStylesDirectly($css);
            }

            add_filter('core_icons', function (array $coreIcons) use ($icons) {
                $themeIcons = [];

                foreach ($icons as $icon) {
                    $themeIcons[$icon] = [
                        'name' => $icon,
                        'basename' => $icon,
                        'path' => null,
                    ];
                }

                return array_merge($coreIcons, $themeIcons);
            }, 120);
        }
    }

    public static function registerFacebookIntegration(): void
    {
        app('events')->listen(RenderingThemeOptionSettings::class, function (): void {
            theme_option()
                ->setSection([
                    'title' => __('Facebook Integration'),
                    'id' => 'opt-text-subsection-facebook-integration',
                    'subsection' => true,
                    'icon' => 'ti ti-brand-facebook',
                    'fields' => [
                        [
                            'id' => 'facebook_chat_enabled',
                            'type' => 'customSelect',
                            'label' => __('Enable Facebook chat?'),
                            'attributes' => [
                                'name' => 'facebook_chat_enabled',
                                'list' => [
                                    'no' => __('No'),
                                    'yes' => __('Yes'),
                                ],
                                'value' => 'no',
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'helper' => __(
                                'To show chat box on that website, please go to :link and add :domain to whitelist domains!',
                                [
                                    'domain' => Html::link(url('')),
                                    'link' => Html::link(
                                        sprintf(
                                            'https://www.facebook.com/%s/settings/?tab=messenger_platform',
                                            theme_option('facebook_page_id', '[PAGE_ID]')
                                        )
                                    ),
                                ]
                            ),
                        ],
                        [
                            'id' => 'facebook_page_id',
                            'type' => 'text',
                            'label' => __('Facebook page ID'),
                            'helper' => __(
                                'You can get fan page ID using this site :link',
                                ['link' => Html::link('https://findidfb.com')]
                            ),
                            'attributes' => [
                                'name' => 'facebook_page_id',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                        ],
                        [
                            'id' => 'facebook_comment_enabled_in_post',
                            'type' => 'customSelect',
                            'label' => __('Enable Facebook comment in post detail page?'),
                            'attributes' => [
                                'name' => 'facebook_comment_enabled_in_post',
                                'list' => [
                                    'yes' => __('Yes'),
                                    'no' => __('No'),
                                ],
                                'value' => 'no',
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                        ],
                        [
                            'id' => 'facebook_app_id',
                            'type' => 'text',
                            'label' => __('Facebook App ID'),
                            'attributes' => [
                                'name' => 'facebook_app_id',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                                'placeholder' => 'Ex: 2061237023872679',
                            ],
                            'helper' => __(
                                'You can create your app in :link',
                                ['link' => Html::link('https://developers.facebook.com/apps')]
                            ),
                        ],
                        [
                            'id' => 'facebook_admins',
                            'type' => 'repeater',
                            'label' => __('Facebook Admins'),
                            'attributes' => [
                                'name' => 'facebook_admins',
                                'value' => null,
                                'fields' => [
                                    [
                                        'type' => 'text',
                                        'label' => __('Facebook Admin ID'),
                                        'attributes' => [
                                            'name' => 'text',
                                            'value' => null,
                                            'options' => [
                                                'class' => 'form-control',
                                                'data-counter' => 40,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'helper' => __(
                                'Facebook admins to manage comments :link',
                                ['link' => Html::link('https://developers.facebook.com/docs/plugins/comments')]
                            ),
                        ],
                    ],
                ]);
        });

        add_filter(THEME_FRONT_HEADER, function (?string $html): ?string {
            if (theme_option('facebook_app_id')) {
                $html .= Html::meta('', theme_option('facebook_app_id'), ['property' => 'fb:app_id'])->toHtml();
            }

            if (theme_option('facebook_admins')) {
                foreach (json_decode(theme_option('facebook_admins'), true) as $facebookAdminId) {
                    if (Arr::get($facebookAdminId, '0.value')) {
                        $html .= Html::meta('', Arr::get($facebookAdminId, '0.value'), ['property' => 'fb:admins'])
                            ->toHtml();
                    }
                }
            }

            if (theme_option('facebook_chat_enabled', 'no') == 'yes' && theme_option('facebook_page_id')) {
                $html .= '<link href="//connect.facebook.net" rel="dns-prefetch" />';
            }

            return $html;
        }, 1180);

        add_filter(THEME_FRONT_FOOTER, function (?string $html): string {
            if (AdminHelper::isInAdmin()) {
                return $html;
            }

            if (
                theme_option('facebook_comment_enabled_in_post', 'no') == 'yes'
                || (theme_option('facebook_chat_enabled', 'no') == 'yes' && theme_option('facebook_page_id'))
            ) {
                return $html . view('packages/theme::partials.facebook-integration')->render();
            }

            return $html;
        }, 1180);

        add_filter(BASE_FILTER_PUBLIC_COMMENT_AREA, function ($html) {
            if (
                theme_option('facebook_comment_enabled_in_post', 'yes') == 'yes' ||
                theme_option('facebook_comment_enabled_in_gallery', 'yes') == 'yes' ||
                theme_option('facebook_comment_enabled_in_product', 'yes') == 'yes'
            ) {
                return $html . view('packages/theme::partials.facebook-comments')->render();
            }

            return $html;
        }, 1180);
    }

    public static function registerSocialLinks(): void
    {
        app('events')->listen(RenderingThemeOptionSettings::class, function (): void {
            ThemeOption::setSection(
                ThemeOptionSection::make('opt-text-subsection-social-links')
                    ->title(__('Social Links'))
                    ->icon('ti ti-social')
                    ->fields([
                        [
                            'id' => 'social_links',
                            'type' => 'repeater',
                            'label' => __('Social Links'),
                            'attributes' => [
                                'name' => 'social_links',
                                'value' => null,
                                'fields' => static::getSocialLinksRepeaterFields(),
                            ],
                        ],
                    ])
            );
        });
    }

    public static function getSocialLinksRepeaterFields(): array
    {
        return [
            [
                'type' => 'text',
                'label' => __('Name'),
                'attributes' => [
                    'name' => 'name',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'type' => 'coreIcon',
                'label' => __('Icon'),
                'attributes' => [
                    'name' => 'icon',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'type' => 'text',
                'label' => __('URL'),
                'attributes' => [
                    'name' => 'url',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'type' => 'mediaImage',
                'label' => __('Icon Image (It will override icon above if set)'),
                'attributes' => [
                    'name' => 'image',
                    'value' => null,
                ],
            ],
            [
                'type' => 'customColor',
                'label' => __('Color'),
                'attributes' => [
                    'name' => 'color',
                    'value' => 'transparent',
                    'options' => [
                        'default_value' => 'transparent',
                    ],
                ],
            ],
            [
                'type' => 'customColor',
                'label' => __('Background color'),
                'attributes' => [
                    'name' => 'background-color',
                    'value' => null,
                    'options' => [
                        'default_value' => 'transparent',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<SocialLink>
     */
    public static function getSocialLinks(): array
    {
        $data = theme_option('social_links');

        if (empty($data)) {
            return [];
        }

        $data = json_decode($data, true);

        if (empty($data)) {
            return [];
        }

        return static::convertSocialLinksToArray($data);
    }

    public static function convertSocialLinksToArray(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        $socialLinks = [];

        foreach ($data as $item) {
            $item = Arr::pluck($item, 'value', 'key');

            $socialLinks[] = new SocialLink(
                name: Arr::get($item, 'name') ?: Arr::get($item, 'social-name'),
                url: Arr::get($item, 'url') ?: Arr::get($item, 'social-url'),
                icon: Arr::get($item, 'icon') ?: Arr::get($item, 'social-icon'),
                image: Arr::get($item, 'image') ?: Arr::get($item, 'social-image'),
                color: Arr::get($item, 'color'),
                backgroundColor: Arr::get($item, 'background-color')
            );
        }

        return $socialLinks;
    }

    public static function getDefaultSocialLinksData(): array
    {
        return [
            [
                ['key' => 'name', 'value' => 'Facebook'],
                ['key' => 'icon', 'value' => 'ti ti-brand-facebook'],
                ['key' => 'url', 'value' => 'https://www.facebook.com'],
            ],
            [
                ['key' => 'name', 'value' => 'X (Twitter)'],
                ['key' => 'icon', 'value' => 'ti ti-brand-x'],
                ['key' => 'url', 'value' => 'https://x.com'],
            ],
            [
                ['key' => 'name', 'value' => 'YouTube'],
                ['key' => 'icon', 'value' => 'ti ti-brand-youtube'],
                ['key' => 'url', 'value' => 'https://www.youtube.com'],
            ],
            [
                ['key' => 'name', 'value' => 'Instagram'],
                ['key' => 'icon', 'value' => 'ti ti-brand-linkedin'],
                ['key' => 'url', 'value' => 'https://www.linkedin.com'],
            ],
        ];
    }

    public static function getThemeIcons(): array
    {
        return apply_filters('theme_icon_list_icons', []);
    }

    public static function registerSiteCopyright(): void
    {
        app('events')->listen(RenderingThemeOptionSettings::class, function (): void {
            ThemeOption::setField([
                'id' => 'copyright',
                'section_id' => 'opt-text-subsection-general',
                'type' => 'textarea',
                'label' => __('Copyright'),
                'attributes' => [
                    'name' => 'copyright',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => __('Change copyright'),
                        'data-counter' => 255,
                        'rows' => 3,
                    ],
                ],
                'helper' => __('Copyright on footer of site. Using %Y to display current year.'),
            ]);
        });
    }

    public static function getSiteCopyright(): ?string
    {
        $copyright = theme_option('copyright');

        if ($copyright) {
            $year = Carbon::now()->format('Y');

            $copyright = str_replace('%Y', $year, $copyright);
            $copyright = str_replace(':year', $year, $copyright);
        }

        $copyright = apply_filters('theme_site_copyright', $copyright);

        if (! $copyright) {
            return null;
        }

        return BaseHelper::clean(nl2br($copyright));
    }

    public static function registerLazyLoadImages(): void
    {
        app('events')->listen(RenderingThemeOptionSettings::class, function (): void {
            ThemeOption::setField([
                'id' => 'lazy_load_images',
                'section_id' => 'opt-text-subsection-general',
                'type' => 'onOff',
                'label' => __('Lazy load images'),
                'attributes' => [
                    'name' => 'lazy_load_images',
                    'value' => false,
                ],
                'helper' => 'Enable lazy load images to improve page load time.',
            ])->setField([
                'id' => 'lazy_load_placeholder_image',
                'section_id' => 'opt-text-subsection-general',
                'type' => 'mediaImage',
                'label' => __('Lazy load placeholder image (250x250px)'),
                'attributes' => [
                    'name' => 'lazy_load_placeholder_image',
                    'value' => null,
                ],
                'helper' => __('This image will be used as placeholder for lazy load images. The best size for this image is 250x250px.'),
            ]);
        });

        if (! theme_option('lazy_load_images', false)) {
            return;
        }

        add_filter('core_media_image', function (
            HtmlString $html,
            ?string $url = null,
            array|string|null $alt = null,
            array $attributes = [],
            bool $secure = false
        ) {
            if (
                AdminHelper::isInAdmin()
                || Arr::get($attributes, 'data-bb-lazy') !== 'true'
            ) {
                return $html;
            }

            $placeholderPath = theme_option('lazy_load_placeholder_image');

            if (! $placeholderPath) {
                return $html;
            }

            $attributes['data-src'] = $url;

            return Html::image(RvMedia::getImageUrl($placeholderPath), $alt, $attributes, $secure);
        }, 120, 4);

        Theme::asset()
            ->container('footer')
            ->add('lazyload', asset('vendor/core/packages/theme/plugins/lazyload.min.js'), ['jquery'], ['defer'], version: get_cms_version());

        add_filter(THEME_FRONT_FOOTER, function (?string $html) {
            if (AdminHelper::isInAdmin()) {
                return $html;
            }

            return $html . <<<'HTML'
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        window.Theme = window.Theme || {};

                        Theme.lazyLoadInstance = new LazyLoad({
                            elements_selector: '[data-bb-lazy="true"]:not(.loaded)',
                        });
                    });

                    document.addEventListener('shortcode.loaded', function () {
                        Theme.lazyLoadInstance.update()
                    });
                </script>
            HTML;
        });
    }

    public static function registerSocialSharing(): void
    {
        app('events')->listen(RenderingThemeOptionSettings::class, function (): void {
            ThemeOption::setSection(
                ThemeOptionSection::make('opt-text-subsection-social-sharing')
                    ->title(__('Social Sharing'))
                    ->icon('ti ti-share')
                    ->fields([
                        RepeaterField::make()
                            ->name('social_sharing')
                            ->label(__('Social sharing buttons'))
                            ->defaultValue(self::getDefaultSocialSharingData())
                            ->fields([
                                SelectField::make()
                                    ->name('social')
                                    ->label(__('Social'))
                                    ->options([
                                        'facebook' => __('Facebook'),
                                        'x' => __('X (Twitter)'),
                                        'linkedin' => __('LinkedIn'),
                                        'pinterest' => __('Pinterest'),
                                        'whatsapp' => __('WhatsApp'),
                                        'telegram' => __('Telegram'),
                                        'email' => __('Email'),
                                    ]),
                                IconField::make()
                                    ->name('icon')
                                    ->label(__('Icon')),
                                MediaImageField::make()
                                    ->name('icon_image')
                                    ->label(__('Icon image (It will override icon above if set)')),
                                ColorField::make()
                                    ->name('color')
                                    ->label(__('Color')),
                                ColorField::make()
                                    ->name('background_color')
                                    ->label(__('Background color')),
                            ]),
                    ])
            );
        });
    }

    public static function getDefaultSocialSharingData(): array
    {
        return [
            [
                ['key' => 'social', 'value' => 'facebook'],
                ['key' => 'icon', 'value' => 'ti ti-brand-facebook'],
            ],
            [
                ['key' => 'social', 'value' => 'x'],
                ['key' => 'icon', 'value' => 'ti ti-brand-x'],
            ],
            [
                ['key' => 'social', 'value' => 'pinterest'],
                ['key' => 'icon', 'value' => 'ti ti-brand-pinterest'],
            ],
            [
                ['key' => 'social', 'value' => 'linkedin'],
                ['key' => 'icon', 'value' => 'ti ti-brand-linkedin'],
            ],
            [
                ['key' => 'social', 'value' => 'whatsapp'],
                ['key' => 'icon', 'value' => 'ti ti-brand-whatsapp'],
            ],
            [
                ['key' => 'social', 'value' => 'email'],
                ['key' => 'icon', 'value' => 'ti ti-mail'],
            ],
        ];
    }

    public static function getSocialSharingButtons(string $url, string $title, ?string $thumbnail = null): array
    {
        $socialSharing = theme_option('social_sharing') ?: self::getDefaultSocialSharingData();

        if (empty($socialSharing)) {
            return [];
        }

        if (! is_array($socialSharing)) {
            $socialSharing = json_decode($socialSharing, true);
        }

        if (empty($socialSharing)) {
            return [];
        }

        $items = [];

        foreach ($socialSharing as $item) {
            $item = Arr::pluck($item, 'value', 'key');

            $social = (string) Arr::get($item, 'social');
            $icon = (string) Arr::get($item, 'icon');
            $image = (string) Arr::get($item, 'icon_image');
            $color = (string) Arr::get($item, 'color');
            $backgroundColor = (string) Arr::get($item, 'background_color');

            if (! $social || (! $icon && ! $image)) {
                continue;
            }

            $encodedTitle = match ($social) {
                'linkedin' => rawurldecode(strip_tags($title)),
                'email' => $title,
                'x' => Str::limit(strip_tags($title), 200),
                default => strip_tags($title),
            };

            $encodedUrl = match ($social) {
                'x', 'whatsapp' => $url,
                default => urlencode($url),
            };

            $items[$social] = [
                'name' => $name = match ($social) {
                    'facebook' => __('Facebook'),
                    'x' => __('X (Twitter)'),
                    'linkedin' => __('LinkedIn'),
                    'pinterest' => __('Pinterest'),
                    'whatsapp' => __('WhatsApp'),
                    'telegram' => __('Telegram'),
                    'email' => __('Email'),
                    default => __('Unknown'),
                },
                'icon' => $image ? Html::image(RvMedia::getImageUrl($image), $name, attributes: ['loading' => false]) : BaseHelper::renderIcon($icon),
                'url' => match ($social) {
                    'facebook' => sprintf('https://www.facebook.com/sharer.php?u=%s', $encodedUrl),
                    'x' => sprintf('https://x.com/intent/tweet?url=%s&text=%s', $encodedUrl, $encodedTitle),
                    'linkedin' => sprintf('https://www.linkedin.com/sharing/share-offsite?url=%s&sumary=%s', $encodedUrl, $encodedTitle),
                    'pinterest' => sprintf(
                        'https://pinterest.com/pin/create/button/?url=%s&description=%s&media=%s',
                        $encodedUrl,
                        $encodedTitle,
                        $thumbnail
                    ),
                    'whatsapp' => sprintf('https://api.whatsapp.com/send?text=%s %s', $encodedTitle, $encodedUrl),
                    'telegram' => sprintf('https://t.me/share/url?url=%s&text=%s', $encodedUrl, $encodedTitle),
                    'email' => sprintf('mailto:?subject=%s&body=%s', $encodedTitle, $encodedUrl),
                    default => $encodedUrl,
                },
                'color' => $color === 'transparent' ? null : $color,
                'background_color' => $backgroundColor === 'transparent' ? null : $backgroundColor,
            ];
        }

        return $items;
    }

    public static function renderSocialSharingButtons(?string $url = null, ?string $title = null, ?string $thumbnail = null): string
    {
        $socials = static::getSocialSharingButtons(
            $url ?: URL::current(),
            $title ?: SeoHelper::getDescription(),
            $thumbnail ?: SeoHelper::openGraph()->getProperty('image')
        );

        if (empty($socials)) {
            return '';
        }

        return view('packages/theme::fronts.social-sharing', compact('socials', 'url'));
    }

    public static function supportedDateFormats(): array
    {
        $formats = [
            'M d, Y',
            'F j, Y',
            'F d, Y',
            'Y-m-d',
            'Y-M-d',
            'd-m-Y',
            'd-M-Y',
            'm/d/Y',
            'M/d/Y',
            'd/m/Y',
            'd/M/Y',
            'd.m.Y',
        ];

        if ($extraDateFormat = config('packages.theme.general.extra_date_format')) {
            $formats[] = $extraDateFormat;
        }

        return apply_filters('theme_date_formats', array_unique($formats));
    }

    public static function registerDateFormatOption(): void
    {
        app('events')->listen(RenderingThemeOptionSettings::class, function (): void {
            ThemeOption::setField(
                SelectField::make()
                    ->sectionId('opt-text-subsection-general')
                    ->label(__('Date format'))
                    ->name('date_format')
                    ->defaultValue(Arr::first(self::supportedDateFormats()))
                    ->options(
                        collect(self::supportedDateFormats())
                            ->mapWithKeys(fn ($format) => [$format => sprintf('%s (%s)', $format, date($format))])
                            ->all()
                    )
                    ->helperText(__('Choose date format for your front theme.'))
            );
        });

        add_filter('core_request_rules', function (array $rules, Request $request): array {
            if (! $request instanceof UpdateOptionsRequest) {
                return $rules;
            }

            $rules['date_format'] = ['sometimes', 'required', 'string', Rule::in(self::supportedDateFormats())];

            return $rules;
        }, 999, 2);
    }

    public static function formatDate(CarbonInterface|string|int|null $date, ?string $format = null): ?string
    {
        $format = $format ?: theme_option('date_format');
        $supportedDateFormats = self::supportedDateFormats();

        if (! $format || ! in_array($format, $supportedDateFormats)) {
            $format = Arr::first($supportedDateFormats);
        }

        return BaseHelper::formatDate($date, $format, true);
    }

    public static function renderGoogleTagManagerScript(): string
    {
        $googleTagManagerCode = setting('google_tag_manager_code');
        $googleTagManagerId = setting('google_tag_manager_id', setting('google_analytics'));
        $renderType = setting(
            'google_tag_manager_type',
            $googleTagManagerCode ? 'code' : 'id'
        );

        if (! BaseHelper::hasDemoModeEnabled() && $renderType === 'code' && $googleTagManagerCode) {
            return trim($googleTagManagerCode);
        }

        if ($renderType === 'id' && $googleTagManagerId) {
            return trim(
                <<<HTML
                <!-- Global site tag (gtag.js) - Google Analytics -->
                <script async defer src='https://www.googletagmanager.com/gtag/js?id=$googleTagManagerId'></script>
                <script>
                  window.dataLayer = window.dataLayer || [];
                  function gtag(){dataLayer.push(arguments);}
                  gtag('js', new Date());

                  gtag('config', '$googleTagManagerId');
                </script>
            HTML
            );
        }

        return '';
    }

    public static function registerSiteLogoHeight(int $defaultValue = 50): void
    {
        app('events')->listen(RenderingThemeOptionSettings::class, function () use ($defaultValue): void {
            ThemeOption::setField(
                NumberFieldOption::make()
                    ->sectionId('opt-text-subsection-logo')
                    ->name('logo_height')
                    ->label(__('Logo height (px)'))
                    ->helperText(__('Set the height of the logo in pixels. The default value is :default.', ['default' => $defaultValue . 'px']))
                    ->attributes([
                        'min' => 0,
                        'max' => 150,
                    ])
                    ->defaultValue($defaultValue)
                    ->priority(100)
            );
        });
    }
}
