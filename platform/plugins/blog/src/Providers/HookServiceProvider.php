<?php

namespace Botble\Blog\Providers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Supports\ServiceProvider;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Tag;
use Botble\Blog\Services\BlogService;
use Botble\Dashboard\Events\RenderingDashboardWidgets;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Botble\Media\Facades\RvMedia;
use Botble\Menu\Events\RenderingMenuOptions;
use Botble\Menu\Facades\Menu;
use Botble\Page\Models\Page;
use Botble\Page\Tables\PageTable;
use Botble\Shortcode\Compilers\Shortcode;
use Botble\Shortcode\Facades\Shortcode as ShortcodeFacade;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Slug\Models\Slug;
use Botble\Theme\Events\RenderingAdminBar;
use Botble\Theme\Events\RenderingThemeOptionSettings;
use Botble\Theme\Facades\AdminBar;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Menu::addMenuOptionModel(Category::class);
        Menu::addMenuOptionModel(Tag::class);

        $this->app['events']->listen(RenderingMenuOptions::class, function (): void {
            add_action(MENU_ACTION_SIDEBAR_OPTIONS, [$this, 'registerMenuOptions'], 2);
        });

        $this->app['events']->listen(RenderingDashboardWidgets::class, function (): void {
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerDashboardWidgets'], 21, 2);
        });

        add_filter(BASE_FILTER_PUBLIC_SINGLE_DATA, [$this, 'handleSingleView'], 2);

        if (defined('PAGE_MODULE_SCREEN_NAME')) {
            add_filter(PAGE_FILTER_FRONT_PAGE_CONTENT, [$this, 'renderBlogPage'], 2, 2);
        }

        PageTable::beforeRendering(function (): void {
            add_filter(PAGE_FILTER_PAGE_NAME_IN_ADMIN_LIST, [$this, 'addAdditionNameToPageName'], 147, 2);
        });

        $this->app['events']->listen(RenderingAdminBar::class, function (): void {
            AdminBar::registerLink(
                trans('plugins/blog::posts.post'),
                route('posts.create'),
                'add-new',
                'posts.create'
            );
        });

        if (function_exists('add_shortcode')) {
            shortcode()
                ->register(
                    $shortcodeName = 'blog-posts',
                    trans('plugins/blog::base.short_code_name'),
                    trans('plugins/blog::base.short_code_description'),
                    [$this, 'renderBlogPosts']
                )
                ->setAdminConfig(
                    $shortcodeName,
                    function (array $attributes) {
                        $categories = Category::query()
                            ->wherePublished()
                            ->pluck('name', 'id')
                            ->all();

                        return ShortcodeForm::createFromArray($attributes)
                            ->withLazyLoading()
                            ->add('paginate', 'number', [
                                'label' => trans('plugins/blog::base.number_posts_per_page'),
                                'attr' => [
                                    'placeholder' => trans('plugins/blog::base.number_posts_per_page'),
                                ],
                            ])
                            ->add(
                                'category_ids[]',
                                SelectField::class,
                                SelectFieldOption::make()
                                    ->label(__('Select categories'))
                                    ->choices($categories)
                                    ->when(Arr::get($attributes, 'category_ids'), function (SelectFieldOption $option, $categoriesIds): void {
                                        $option->selected(explode(',', $categoriesIds));
                                    })
                                    ->multiple()
                                    ->searchable()
                                    ->helperText(__('Leave categories empty if you want to show posts from all categories.'))
                            );
                    }
                );
        }

        $this->app['events']->listen(RenderingThemeOptionSettings::class, function (): void {
            add_action(RENDERING_THEME_OPTIONS_PAGE, [$this, 'addThemeOptions'], 35);
        });

        if (defined('THEME_FRONT_HEADER') && setting('blog_post_schema_enabled', 1)) {
            add_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, function ($screen, $post): void {
                add_filter(THEME_FRONT_HEADER, function ($html) use ($post) {
                    if (! $post instanceof Post) {
                        return $html;
                    }

                    $schemaType = setting('blog_post_schema_type', 'NewsArticle');

                    if (! in_array($schemaType, ['NewsArticle', 'News', 'Article', 'BlogPosting'])) {
                        $schemaType = 'NewsArticle';
                    }

                    $schema = [
                        '@context' => 'https://schema.org',
                        '@type' => $schemaType,
                        'mainEntityOfPage' => [
                            '@type' => 'WebPage',
                            '@id' => $post->url,
                        ],
                        'headline' => BaseHelper::clean($post->name),
                        'description' => BaseHelper::clean($post->description),
                        'image' => [
                            '@type' => 'ImageObject',
                            'url' => RvMedia::getImageUrl($post->image, null, false, RvMedia::getDefaultImage()),
                        ],
                        'author' => [
                            '@type' => 'Person',
                            'url' => fn () => BaseHelper::getHomepageUrl(),
                            'name' => class_exists($post->author_type) ? $post->author->name : '',
                        ],
                        'publisher' => [
                            '@type' => 'Organization',
                            'name' => theme_option('site_title'),
                            'logo' => [
                                '@type' => 'ImageObject',
                                'url' => RvMedia::getImageUrl(Theme::getLogo()),
                            ],
                        ],
                        'datePublished' => $post->created_at->toIso8601String(),
                        'dateModified' => $post->updated_at->toIso8601String(),
                    ];

                    return $html . Html::tag('script', json_encode($schema, JSON_UNESCAPED_UNICODE), ['type' => 'application/ld+json'])
                        ->toHtml();
                }, 35);
            }, 35, 2);
        }
    }

    public function addThemeOptions(): void
    {
        $pages = Page::query()
            ->wherePublished()
            ->pluck('name', 'id')
            ->all();

        theme_option()
            ->setSection([
                'title' => trans('plugins/blog::base.settings.title'),
                'id' => 'opt-text-subsection-blog',
                'subsection' => true,
                'icon' => 'ti ti-edit',
                'fields' => [
                    [
                        'id' => 'blog_page_id',
                        'type' => 'customSelect',
                        'label' => trans('plugins/blog::base.blog_page_id'),
                        'attributes' => [
                            'name' => 'blog_page_id',
                            'list' => [0 => trans('plugins/blog::base.select')] + $pages,
                            'value' => '',
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'id' => 'number_of_posts_in_a_category',
                        'type' => 'number',
                        'label' => trans('plugins/blog::base.number_posts_per_page_in_category'),
                        'attributes' => [
                            'name' => 'number_of_posts_in_a_category',
                            'value' => 12,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'id' => 'number_of_posts_in_a_tag',
                        'type' => 'number',
                        'label' => trans('plugins/blog::base.number_posts_per_page_in_tag'),
                        'attributes' => [
                            'name' => 'number_of_posts_in_a_tag',
                            'value' => 12,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                ],
            ]);
    }

    public function registerMenuOptions(): void
    {
        if (Auth::guard()->user()->hasPermission('categories.index')) {
            Menu::registerMenuOptions(Category::class, trans('plugins/blog::categories.menu'));
        }

        if (Auth::guard()->user()->hasPermission('tags.index')) {
            Menu::registerMenuOptions(Tag::class, trans('plugins/blog::tags.menu'));
        }
    }

    public function registerDashboardWidgets(array $widgets, Collection $widgetSettings): array
    {
        if (! Auth::guard()->user()->hasPermission('posts.index')) {
            return $widgets;
        }

        Assets::addScriptsDirectly(['/vendor/core/plugins/blog/js/blog.js']);

        return (new DashboardWidgetInstance())
            ->setPermission('posts.index')
            ->setKey('widget_posts_recent')
            ->setTitle(trans('plugins/blog::posts.widget_posts_recent'))
            ->setIcon('fas fa-edit')
            ->setColor('yellow')
            ->setRoute(route('posts.widget.recent-posts'))
            ->setBodyClass('')
            ->setColumn('col-md-6 col-sm-6')
            ->init($widgets, $widgetSettings);
    }

    public function handleSingleView(Slug|array $slug): Slug|array
    {
        return (new BlogService())->handleFrontRoutes($slug);
    }

    public function renderBlogPosts(Shortcode $shortcode): array|string
    {
        $categoryIds = ShortcodeFacade::fields()->getIds('category_ids', $shortcode);

        $posts = Post::query()
            ->wherePublished()
            ->orderByDesc('created_at')
            ->with(['slugable', 'categories.slugable'])
            ->when(! empty($categoryIds), function ($query) use ($categoryIds): void {
                $query->whereHas('categories', function ($query) use ($categoryIds): void {
                    $query->whereIn('categories.id', $categoryIds);
                });
            })
            ->paginate((int) $shortcode->paginate ?: 12);

        $view = 'plugins/blog::themes.templates.posts';
        $themeView = Theme::getThemeNamespace() . '::views.templates.posts';

        if (view()->exists($themeView)) {
            $view = $themeView;
        }

        return view($view, compact('posts', 'shortcode'))->render();
    }

    public function renderBlogPage(?string $content, Page $page): ?string
    {
        if ($page->getKey() == $this->getBlogPageId()) {
            $view = 'plugins/blog::themes.loop';

            if (view()->exists($viewPath = Theme::getThemeNamespace() . '::views.loop')) {
                $view = $viewPath;
            }

            return view($view, [
                'posts' => get_all_posts(true, (int) theme_option('number_of_posts_in_a_category', 12)),
            ])->render();
        }

        return $content;
    }

    public function addAdditionNameToPageName(?string $name, Page $page): ?string
    {
        if ($page->getKey() == $this->getBlogPageId()) {
            $subTitle = Html::tag('span', trans('plugins/blog::base.blog_page'), ['class' => 'additional-page-name'])
                ->toHtml();

            if (Str::contains($name, ' —')) {
                return $name . ', ' . $subTitle;
            }

            return $name . ' —' . $subTitle;
        }

        return $name;
    }

    protected function getBlogPageId(): int|string|null
    {
        return theme_option('blog_page_id', setting('blog_page_id'));
    }
}
