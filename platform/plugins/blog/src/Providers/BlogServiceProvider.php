<?php

namespace Botble\Blog\Providers;

use Botble\ACL\Models\User;
use Botble\Api\Facades\ApiHelper;
use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\DashboardMenuItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Blog\Models\Category;
use Botble\Blog\Models\Post;
use Botble\Blog\Models\Tag;
use Botble\Blog\Repositories\Eloquent\CategoryRepository;
use Botble\Blog\Repositories\Eloquent\PostRepository;
use Botble\Blog\Repositories\Eloquent\TagRepository;
use Botble\Blog\Repositories\Interfaces\CategoryInterface;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Blog\Repositories\Interfaces\TagInterface;
use Botble\DataSynchronize\PanelSections\ExportPanelSection;
use Botble\DataSynchronize\PanelSections\ImportPanelSection;
use Botble\Language\Facades\Language;
use Botble\LanguageAdvanced\Supports\LanguageAdvancedManager;
use Botble\PluginManagement\Events\DeactivatedPlugin;
use Botble\PluginManagement\Events\RemovedPlugin;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use Botble\Shortcode\View\View;
use Botble\Slug\Facades\SlugHelper;
use Botble\Slug\Models\Slug;
use Botble\Theme\Events\ThemeRoutingBeforeEvent;
use Botble\Theme\Facades\SiteMapManager;

/**
 * @since 02/07/2016 09:50 AM
 */
class BlogServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->bind(PostInterface::class, function () {
            return new PostRepository(new Post());
        });

        $this->app->bind(CategoryInterface::class, function () {
            return new CategoryRepository(new Category());
        });

        $this->app->bind(TagInterface::class, function () {
            return new TagRepository(new Tag());
        });
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/blog')
            ->loadHelpers()
            ->loadAndPublishConfigurations(['permissions', 'general'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadMigrations()
            ->publishAssets();

        if (class_exists('ApiHelper') && ApiHelper::enabled()) {
            $this->loadRoutes(['api']);
        }

        $this->app->register(EventServiceProvider::class);

        $this->app['events']->listen(ThemeRoutingBeforeEvent::class, function (): void {
            SiteMapManager::registerKey([
                'blog-categories',
                'blog-tags',
                'blog-posts-((?:19|20|21|22)\d{2})-(0?[1-9]|1[012])',
            ]);
        });

        SlugHelper::registering(function (): void {
            SlugHelper::registerModule(Post::class, fn () => trans('plugins/blog::base.blog_posts'));
            SlugHelper::registerModule(Category::class, fn () => trans('plugins/blog::base.blog_categories'));
            SlugHelper::registerModule(Tag::class, fn () => trans('plugins/blog::base.blog_tags'));

            SlugHelper::setPrefix(Tag::class, 'tag', true);
            SlugHelper::setPrefix(Post::class, null, true);
            SlugHelper::setPrefix(Category::class, null, true);
        });

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-blog')
                        ->priority(3)
                        ->name('plugins/blog::base.menu_name')
                        ->icon('ti ti-article')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-blog-post')
                        ->priority(10)
                        ->parentId('cms-plugins-blog')
                        ->name('plugins/blog::posts.menu_name')
                        ->icon('ti ti-file-text')
                        ->route('posts.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-blog-categories')
                        ->priority(20)
                        ->parentId('cms-plugins-blog')
                        ->name('plugins/blog::categories.menu_name')
                        ->icon('ti ti-folder')
                        ->route('categories.index')
                )
                ->registerItem(
                    DashboardMenuItem::make()
                        ->id('cms-plugins-blog-tags')
                        ->priority(30)
                        ->parentId('cms-plugins-blog')
                        ->name('plugins/blog::tags.menu_name')
                        ->icon('ti ti-tag')
                        ->route('tags.index')
                );
        });

        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('blog')
                    ->setTitle(trans('plugins/blog::base.settings.title'))
                    ->withIcon('ti ti-file-settings')
                    ->withDescription(trans('plugins/blog::base.settings.description'))
                    ->withPriority(120)
                    ->withRoute('blog.settings')
            );
        });

        PanelSectionManager::setGroupId('data-synchronize')->beforeRendering(function (): void {
            PanelSectionManager::default()
                ->registerItem(
                    ExportPanelSection::class,
                    fn () => PanelSectionItem::make('posts')
                        ->setTitle(trans('plugins/blog::posts.posts'))
                        ->withDescription(trans('plugins/blog::posts.export.description'))
                        ->withPriority(999)
                        ->withPermission('posts.export')
                        ->withRoute('tools.data-synchronize.export.posts.index')
                )
                ->registerItem(
                    ImportPanelSection::class,
                    fn () => PanelSectionItem::make('posts')
                        ->setTitle(trans('plugins/blog::posts.posts'))
                        ->withDescription(trans('plugins/blog::posts.import.description'))
                        ->withPriority(999)
                        ->withPermission('posts.import')
                        ->withRoute('tools.data-synchronize.import.posts.index')
                );
        });

        if (defined('LANGUAGE_MODULE_SCREEN_NAME') && defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
            if (
                defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME') &&
                $this->app['config']->get('plugins.blog.general.use_language_v2')
            ) {
                LanguageAdvancedManager::registerModule(Post::class, [
                    'name',
                    'description',
                    'content',
                ]);

                LanguageAdvancedManager::registerModule(Category::class, [
                    'name',
                    'description',
                ]);

                LanguageAdvancedManager::registerModule(Tag::class, [
                    'name',
                    'description',
                ]);
            } else {
                Language::registerModule([Post::class, Category::class, Tag::class]);
            }
        }

        User::resolveRelationUsing('posts', function (User $user) {
            return $user->morphMany(Post::class, 'author');
        });

        User::resolveRelationUsing('slugable', function (User $user) {
            return $user->morphMany(Slug::class, 'reference');
        });

        $this->app->booted(function (): void {
            SeoHelper::registerModule([Post::class, Category::class, Tag::class]);

            $configKey = 'packages.revision.general.supported';
            config()->set($configKey, array_merge(config($configKey, []), [Post::class]));

            $this->app->register(HookServiceProvider::class);
        });

        if (function_exists('shortcode')) {
            view()->composer([
                'plugins/blog::themes.post',
                'plugins/blog::themes.category',
                'plugins/blog::themes.tag',
            ], function (View $view): void {
                $view->withShortcodes();
            });
        }

        $this->app['events']->listen(
            [DeactivatedPlugin::class, RemovedPlugin::class],
            function (DeactivatedPlugin|RemovedPlugin $event): void {
                if ($event->plugin === 'member') {
                    Post::query()->where('author_type', 'Botble\Member\Models\Member')->update([
                        'author_id' => null,
                        'author_type' => User::class,
                    ]);
                }
            }
        );
    }
}
