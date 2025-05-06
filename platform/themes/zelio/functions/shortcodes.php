<?php

use Botble\Base\Forms\FieldOptions\CoreIconFieldOption;
use Botble\Base\Forms\FieldOptions\LabelFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\UiSelectorFieldOption;
use Botble\Base\Forms\Fields\CoreIconField;
use Botble\Base\Forms\Fields\LabelField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\UiSelectorField;
use Botble\Shortcode\Compilers\Shortcode as ShortcodeCompiler;
use Botble\Shortcode\Facades\Shortcode;
use Botble\Shortcode\Forms\FieldOptions\ShortcodeTabsFieldOption;
use Botble\Shortcode\Forms\Fields\ShortcodeTabsField;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Theme\Facades\Theme;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

app()->booted(function () {
    Event::listen(RouteMatched::class, function (): void {
        Shortcode::register(
            'image-slider',
            __('Image Slider'),
            __('Dynamic carousel for featured content with customizable links.'),
            function (ShortcodeCompiler $shortcode) {
                $tabs = Shortcode::fields()->getTabsData(['name', 'image', 'url', 'open_in_new_tab'], $shortcode);

                if (empty($tabs)) {
                    return null;
                }

                return Theme::partial('shortcodes.image-slider.index', compact('shortcode', 'tabs'));
            }
        );

        Shortcode::setAdminConfig('image-slider', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()->label(__('Title'))
                )
                ->add(
                    'subtitle',
                    TextareaField::class,
                    TextareaFieldOption::make()->label(__('Subtitle'))->rows(2)
                )
                ->add(
                    'tabs',
                    ShortcodeTabsField::class,
                    ShortcodeTabsFieldOption::make()
                        ->fields([
                            'name' => [
                                'type' => 'text',
                                'title' => __('Name'),
                            ],
                            'image' => [
                                'type' => 'image',
                                'title' => __('Image'),
                                'required' => true,
                            ],
                            'url' => [
                                'type' => 'text',
                                'title' => __('URL'),
                            ],
                            'open_in_new_tab' => [
                                'type' => 'onOff',
                                'title' => __('Open URL in a new tab'),
                            ],
                        ])
                        ->attrs($attributes)
                );
        });

        Shortcode::register(
            'skills',
            __('Skills'),
            __('Display a list of skills with icons and proficiency levels.'),
            function (ShortcodeCompiler $shortcode) {
                $tabs = Shortcode::fields()->getTabsData(['name', 'image', 'level'], $shortcode);

                return Theme::partial('shortcodes.skills.index', compact('shortcode', 'tabs'));
            }
        );

        Shortcode::setAdminConfig('skills', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'style',
                    UiSelectorField::class,
                    UiSelectorFieldOption::make()
                        ->label(__('Style'))
                        ->numberItemsPerRow(2)
                        ->emptyValue(1)
                        ->choices([
                            1 => [
                                'label' => __('Style :i', ['i' => 1]),
                                'image' => Theme::asset()->url('images/shortcodes/skills/style-1.png'),
                            ],
                            2 => [
                                'label' => __('Style :i', ['i' => 2]),
                                'image' => Theme::asset()->url('images/shortcodes/skills/style-2.png'),
                            ],
                        ])
                )
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()->label(__('Title'))
                )
                ->add(
                    'subtitle',
                    TextareaField::class,
                    TextareaFieldOption::make()->label(__('Subtitle'))->rows(2)
                )
                ->add(
                    'tabs',
                    ShortcodeTabsField::class,
                    ShortcodeTabsFieldOption::make()
                        ->label(__('Skills'))
                        ->fields([
                            'name' => [
                                'type' => 'text',
                                'title' => __('Name'),
                            ],
                            'image' => [
                                'type' => 'image',
                                'title' => __('Image'),
                                'required' => true,
                            ],
                            'level' => [
                                'type' => 'text',
                                'title' => __('Level'),
                                'placeholder' => __('Ex: 90%'),
                                'helper' => __('This level only displays on style 1.'),
                            ],
                        ])
                        ->attrs($attributes)
                )
                ->add(
                    'list',
                    ShortcodeTabsField::class,
                    ShortcodeTabsFieldOption::make()
                        ->label(__('Details'))
                        ->collapsible('style', 2, Arr::get($attributes, 'style', 1))
                        ->fields([
                            'label' => [
                                'type' => 'text',
                                'title' => __('Name'),
                            ],
                            'content' => [
                                'type' => 'textarea',
                                'title' => __('Content'),
                                'required' => true,
                            ],
                        ], 'list')
                        ->attrs($attributes)
                )
                ->add(
                    'extra_content',
                    TextareaField::class,
                    TextareaFieldOption::make()
                        ->label(__('Extra Content'))
                        ->collapsible('style', 1, Arr::get($attributes, 'style', 1))
                );
        });

        Shortcode::register(
            'resume',
            __('Resume'),
            __('Displays the resume with education and experience details in a structured layout.'),
            function (ShortcodeCompiler $shortcode) {
                $resumes = [];

                foreach (range(1, 2) as $i) {
                    $key = "resume_$i";

                    $resumes[$key] = Shortcode::fields()->getTabsData(['time', 'title', 'subtitle', 'description', 'image'], $shortcode, $key);
                }

                $resumes = array_filter($resumes);

                return Theme::partial('shortcodes.resume.index', compact('shortcode', 'resumes'));
            }
        );

        Shortcode::setAdminConfig('resume', function (array $attributes) {
            $form = ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'style',
                    UiSelectorField::class,
                    UiSelectorFieldOption::make()
                        ->label(__('Style'))
                        ->emptyValue(1)
                        ->withoutAspectRatio()
                        ->choices(
                            collect(range(1, 3))->mapWithKeys(function ($i) {
                                return [
                                    $i => [
                                        'label' => __('Style :i', ['i' => $i]),
                                        'image' => Theme::asset()->url("images/shortcodes/resume/style-$i.png"),
                                    ],
                                ];
                            })->all()
                        )
                )
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Title'))
                        ->collapsible('style', 1, Arr::get($attributes, 'style', 1))
                )
                ->add(
                    'subtitle',
                    TextareaField::class,
                    TextareaFieldOption::make()
                        ->label(__('Subtitle'))->rows(2)
                        ->collapsible('style', 1, Arr::get($attributes, 'style', 1))
                )
                ->add(
                    'action_text',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Action text'))
                        ->collapsible('style', 1, Arr::get($attributes, 'style', 1))
                )
                ->add(
                    'action_link',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Action link'))
                        ->collapsible('style', 1, Arr::get($attributes, 'style', 1))
                );

            foreach (range(1, 2) as $i) {
                $key = "resume_$i";

                $form->addOpenFieldset($key)
                    ->add(
                        "{$key}_title",
                        TextField::class,
                        TextFieldOption::make()->label(__('Resume :i Title', ['i' => $i]))
                    )
                    ->add(
                        "{$key}_title_icon",
                        CoreIconField::class,
                        CoreIconFieldOption::make()
                            ->label(__('Resume :i Title Icon', ['i' => $i]))
                            ->helperText(__('This icon will only display on style 1 and 2.'))
                    )
                    ->add(
                        "{$key}_display_type",
                        RadioField::class,
                        RadioFieldOption::make()
                            ->label(__('Display type'))
                            ->emptyValue('default')
                            ->choices([
                                'default' => __('Default'),
                                'timeline' => __('Timeline'),
                            ])
                    )
                    ->add(
                        $key,
                        ShortcodeTabsField::class,
                        ShortcodeTabsFieldOption::make()
                            ->label(__('Resume :i', ['i' => $i]))
                            ->attrs($attributes)
                            ->fields([
                                'time' => [
                                    'type' => 'text',
                                    'title' => __('Time'),
                                    'placeholder' => __('Ex: 2018 - 2020'),
                                ],
                                'title' => [
                                    'type' => 'text',
                                    'title' => __('Title'),
                                ],
                                'subtitle' => [
                                    'type' => 'text',
                                    'title' => __('Subtitle'),
                                ],
                                'description' => [
                                    'type' => 'text',
                                    'title' => __('Description'),
                                ],
                                'image' => [
                                    'type' => 'image',
                                    'title' => 'Image',
                                    'helper' => __('This image will only display on style 3.'),
                                ],
                            ], $key)
                    )
                    ->addCloseFieldset($key);
            }

            $form
                ->add(
                    'background_image',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->label(__('Background image'))
                        ->collapsible('style', 1, Arr::get($attributes, 'style', 1))
                )
                ->add(
                    'bottom_text',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Bottom text'))
                        ->helperText(__('Add a text to display at the bottom of the resume.'))
                        ->collapsible('style', 1, Arr::get($attributes, 'style', 1))
                );

            return $form;
        });

        Shortcode::register(
            'stats-counter',
            __('Stats Counter'),
            __('Displays key stats like experience, projects, clients, and awards.'),
            function (ShortcodeCompiler $shortcode) {
                $tabs = Shortcode::fields()->getTabsData(['label', 'highlight_text', 'count', 'icon'], $shortcode);

                if (empty($tabs)) {
                    return null;
                }

                return Theme::partial('shortcodes.stats-counter.index', compact('shortcode', 'tabs'));
            }
        );

        Shortcode::setAdminConfig('stats-counter', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'style',
                    UiSelectorField::class,
                    UiSelectorFieldOption::make()
                        ->label(__('Style'))
                        ->numberItemsPerRow(2)
                        ->emptyValue(1)
                        ->choices([
                            1 => [
                                'label' => __('Style :i', ['i' => 1]),
                                'image' => Theme::asset()->url('images/shortcodes/stats-counter/style-1.png'),
                            ],
                            2 => [
                                'label' => __('Style :i', ['i' => 2]),
                                'image' => Theme::asset()->url('images/shortcodes/stats-counter/style-2.png'),
                            ],
                        ])
                )
                ->add(
                    'tabs',
                    ShortcodeTabsField::class,
                    ShortcodeTabsFieldOption::make()
                        ->fields([
                            'label' => [
                                'type' => 'text',
                                'title' => __('Label'),
                            ],
                            'highlight_text' => [
                                'type' => 'text',
                                'title' => __('Highlight Text'),
                                'helper' => __('This text will only display on style 1.'),
                            ],
                            'count' => [
                                'type' => 'text',
                                'title' => __('Count'),
                            ],
                            'icon' => [
                                'type' => 'coreIcon',
                                'title' => 'Icon',
                                'helper' => __('This text will only display on style 2.'),
                            ],
                        ])
                        ->attrs($attributes)
                )
                ->add(
                    'background_image',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->label(__('Background image'))
                        ->collapsible('style', 2, Arr::get($attributes, 'style', 1))
                );
        });

        Shortcode::register(
            'hero-banner',
            __('Hero Banner'),
            __('Hero Banner'),
            function (ShortcodeCompiler $shortcode) {
                $skills = Shortcode::fields()->getTabsData(['name', 'image'], $shortcode);

                return Theme::partial('shortcodes.hero-banner.index', compact('shortcode', 'skills'));
            }
        );

        Shortcode::setAdminConfig('hero-banner', function (array $attributes) {
            $form = ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'style',
                    UiSelectorField::class,
                    UiSelectorFieldOption::make()
                        ->label(__('Style'))
                        ->defaultValue($attributes['style'] ?? 1)
                        ->choices(
                            collect(range(1, 3))->mapWithKeys(function ($i) {
                                return [
                                    $i => [
                                        'label' => __('Style :i', ['i' => $i]),
                                        'image' => Theme::asset()->url("images/shortcodes/hero-banner/style-$i.png"),
                                    ],
                                ];
                            })->all()
                        )
                )
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()->label(__('Title'))
                )
                ->add(
                    'subtitle',
                    TextField::class,
                    TextFieldOption::make()->label(__('Subtitle'))
                )
                ->add(
                    'description',
                    TextareaField::class,
                    TextareaFieldOption::make()->label(__('Description'))
                );

            foreach (['primary' => __('Primary'), 'secondary' => __('Secondary')] as $key => $label) {
                $form
                    ->addOpenFieldset("{$key}_button")
                    ->add(
                        "{$key}_button_text",
                        TextField::class,
                        TextFieldOption::make()
                            ->label(__(':label button text', ['label' => $label]))
                    )
                    ->add(
                        "{$key}_button_link",
                        TextField::class,
                        TextFieldOption::make()
                            ->label(__(':label button link', ['label' => $label]))
                            ->helperText(__('Enter the link to redirect when the button is clicked. Please enter the full URL, e.g. :url.', ['url' => url('/storage/resume/cv.pdf')]))
                    )
                    ->add(
                        "{$key}_button_icon",
                        CoreIconField::class,
                        CoreIconFieldOption::make()
                            ->label(__(':label button icon', ['label' => $label]))
                    )
                    ->add(
                        "open_{$key}_link_in_the_new_tab",
                        SelectField::class,
                        SelectFieldOption::make()
                            ->label(__('Open :label link in the new tab?', ['label' => $label]))
                            ->choices([
                                'yes' => __('Yes'),
                                'no' => __('No'),
                            ])
                    )
                    ->addCloseFieldset("{$key}_button");
            }

            return $form
                ->add(
                    'below_button_text',
                    TextareaField::class,
                    TextareaFieldOption::make()
                        ->collapsible('style', [1, 2], Arr::get($attributes, 'style', 1))
                        ->label(__('Below button text'))
                )
                ->addOpenFieldset('skills', [
                    'data-bb-collapse' => 'true',
                    'data-bb-trigger' => '[name=style]',
                    'data-bb-value' => json_encode([1, 2]),
                    'style' => in_array(Arr::get($attributes, 'style', 1), [1, 2]) ? '' : 'display: none;',
                ])
                ->add(
                    'skills_label',
                    LabelField::class,
                    LabelFieldOption::make()
                        ->label(__('Skills'))
                )
                ->add(
                    'skills',
                    ShortcodeTabsField::class,
                    ShortcodeTabsFieldOption::make()
                        ->label(__('Skills'))
                        ->fields([
                            'name' => [
                                'type' => 'text',
                                'title' => __('Name'),
                            ],
                            'image' => [
                                'type' => 'image',
                                'title' => __('Image'),
                                'required' => true,
                            ],
                        ])
                        ->attrs($attributes)
                )
                ->addCloseFieldset('skills')
                ->add(
                    'background_image',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->label(__('Background image'))
                        ->collapsible('style', [1, 2], Arr::get($attributes, 'style', 1))
                )
                ->add(
                    'background_image_dark',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->label(__('Dark background image'))
                        ->collapsible('style', 2, Arr::get($attributes, 'style', 1))
                )
                ->add(
                    'right_image',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->label(__('Right image'))
                        ->collapsible('style', [1, 2], Arr::get($attributes, 'style', 1))
                )
                ->add(
                    'right_image_shape',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->label(__('Right image shape'))
                        ->collapsible('style', [1, 2], Arr::get($attributes, 'style', 1))
                );
        });

        Shortcode::register(
            'corporation',
            __('Corporation'),
            __('Displays a corporation section with company details and achievements.'),
            function (ShortcodeCompiler $shortcode) {
                $companies = Shortcode::fields()->getTabsData(['name', 'image'], $shortcode, 'companies');
                $contacts = Shortcode::fields()->getTabsData(['label', 'content', 'url', 'icon'], $shortcode, 'contacts');
                $journeys = Shortcode::fields()->getTabsData(['date', 'title'], $shortcode, 'journeys');

                return Theme::partial(
                    'shortcodes.corporation.index',
                    compact('shortcode', 'companies', 'contacts', 'journeys')
                );
            }
        );

        Shortcode::setAdminConfig('corporation', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()->label(__('Title'))
                )
                ->add(
                    'subtitle',
                    TextField::class,
                    TextFieldOption::make()->label(__('Subtitle'))
                )
                ->add(
                    'companies',
                    ShortcodeTabsField::class,
                    ShortcodeTabsFieldOption::make()
                        ->label(__('Companies'))
                        ->fields([
                            'name' => [
                                'type' => 'text',
                                'title' => __('Name'),
                            ],
                            'image' => [
                                'type' => 'image',
                                'title' => __('Image'),
                                'required' => true,
                            ],
                        ], 'companies')
                        ->attrs($attributes)
                )
                ->add(
                    'contact_avatar',
                    MediaImageField::class,
                    MediaImageFieldOption::make()->label(__('Contact avatar'))
                )
                ->add(
                    'contacts',
                    ShortcodeTabsField::class,
                    ShortcodeTabsFieldOption::make()
                        ->label(__('Contacts'))
                        ->fields([
                            'label' => [
                                'type' => 'text',
                                'title' => __('Label'),
                            ],
                            'content' => [
                                'type' => 'text',
                                'title' => __('Content'),
                            ],
                            'url' => [
                                'type' => 'text',
                                'title' => __('URL'),
                            ],
                            'icon' => [
                                'type' => 'coreIcon',
                                'title' => 'Icon',
                            ],
                        ], 'contacts')
                        ->attrs($attributes)
                )
                ->add(
                    'journeys',
                    ShortcodeTabsField::class,
                    ShortcodeTabsFieldOption::make()
                        ->label(__('Journeys'))
                        ->fields([
                            'date' => [
                                'type' => 'text',
                                'title' => __('Date'),
                            ],
                            'title' => [
                                'type' => 'textarea',
                                'title' => __('Title'),
                            ],
                        ], 'journeys')
                        ->attrs($attributes)
                );
        });

        Shortcode::register(
            'experience',
            __('Experience'),
            __("Showcases the user's professional experience and skills."),
            function (ShortcodeCompiler $shortcode) {
                $experiences = Shortcode::fields()->getTabsData(['date', 'title', 'logo'], $shortcode, 'experiences');
                $skills = Shortcode::fields()->getTabsData(['name'], $shortcode, 'skills');

                return Theme::partial('shortcodes.experience.index', compact('shortcode', 'experiences', 'skills'));
            }
        );

        Shortcode::setAdminConfig('experience', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->withLazyLoading()
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()->label(__('Title'))
                )
                ->add(
                    'subtitle',
                    TextField::class,
                    TextFieldOption::make()->label(__('Subtitle'))
                )
                ->add(
                    'role_title',
                    TextField::class,
                    TextFieldOption::make()->label(__('Role title'))
                )
                ->add(
                    'role_description',
                    TextareaField::class,
                    TextareaFieldOption::make()
                        ->label(__('Role description'))
                        ->helperText(__('Enter the description to appear below the role title. Use line breaks to separate paragraphs.'))
                )
                ->add(
                    'experiences',
                    ShortcodeTabsField::class,
                    ShortcodeTabsFieldOption::make()
                        ->label(__('Experiences'))
                        ->fields([
                            'date' => [
                                'type' => 'text',
                                'title' => __('Date'),
                            ],
                            'title' => [
                                'type' => 'text',
                                'title' => __('Title'),
                            ],
                            'logo' => [
                                'type' => 'image',
                                'title' => __('Logo'),
                            ],
                        ], 'experiences')
                        ->attrs($attributes)
                )
                ->add(
                    'skills',
                    ShortcodeTabsField::class,
                    ShortcodeTabsFieldOption::make()
                        ->label(__('Skills'))
                        ->fields([
                            'name' => [
                                'type' => 'text',
                                'title' => __('Name'),
                            ],
                        ], 'skills')
                        ->attrs($attributes)
                )
                ->add(
                    'background_image',
                    MediaImageField::class,
                    MediaImageFieldOption::make()->label(__('Background image'))
                );
        });

        Shortcode::register('highlighted-box', __('Highlighted Box'), __('Highlighted Box'), function (ShortcodeCompiler $shortcode) {
            return Theme::partial('shortcodes.highlighted-box.index', compact('shortcode'));
        });

        Shortcode::setAdminConfig('highlighted-box', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->add(
                    'title',
                    TextField::class,
                    TextFieldOption::make()->label(__('Title'))
                )
                ->add(
                    'description',
                    TextareaField::class,
                    TextareaFieldOption::make()->label(__('Description'))->rows(2)
                );
        });
    });
});
