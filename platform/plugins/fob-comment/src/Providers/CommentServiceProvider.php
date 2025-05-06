<?php

namespace FriendsOfBotble\Comment\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Models\BaseModel;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Blog\Forms\PostForm;
use Botble\PluginManagement\Events\DeactivatedPlugin;
use Botble\PluginManagement\Events\RemovedPlugin;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use Botble\Theme\FormFrontManager;
use FriendsOfBotble\Comment\Enums\CommentStatus;
use FriendsOfBotble\Comment\Forms\Fronts\CommentForm;
use FriendsOfBotble\Comment\Forms\ReplyCommentForm;
use FriendsOfBotble\Comment\Http\Requests\Fronts\CommentRequest;
use FriendsOfBotble\Comment\Http\Requests\Fronts\ReplyCommentRequest;
use FriendsOfBotble\Comment\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->instance('fob.comments.counter', []);
    }

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/fob-comment')
            ->publishAssets()
            ->loadAndPublishViews()
            ->loadRoutes()
            ->loadAndPublishConfigurations(['permissions'])
            ->loadAndPublishTranslations()
            ->loadMigrations();

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem([
                    'id' => 'cms-plugins-fob-comment',
                    'priority' => 99,
                    'name' => 'plugins/fob-comment::comment.title',
                    'icon' => 'ti ti-messages',
                    'route' => 'fob-comment.comments.index',
                ]);
        });

        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('fob-comment')
                    ->setTitle(trans('plugins/fob-comment::comment.settings.title'))
                    ->withDescription(trans('plugins/fob-comment::comment.settings.description'))
                    ->withIcon('ti ti-message-cog')
                    ->withPriority(0)
                    ->withRoute('fob-comment.settings')
            );
        });

        $this->app->booted(function (): void {
            add_filter(BASE_FILTER_PUBLIC_COMMENT_AREA, function (string $html, ?BaseModel $model) {
                if (! $model) {
                    return $html;
                }

                if ($model->getMetaData('allow_comments', true) == '0') {
                    return $html;
                }

                return $html . view('plugins/fob-comment::comment', compact('model'))->render();
            }, 1, 2);

            add_filter(BASE_FILTER_APPEND_MENU_NAME, function (?string $html, string $menuId) {
                if ($menuId !== 'cms-plugins-fob-comment') {
                    return $html;
                }

                return view('core/base::partials.navbar.badge-count', ['class' => 'unapproved-comments-count']);
            }, 1, 2);

            add_filter(BASE_FILTER_MENU_ITEMS_COUNT, function (array $data = []) {
                if (! Auth::guard()->user()->hasPermission('fob-comment.comments.index')) {
                    return $data;
                }

                $data[] = [
                    'key' => 'unapproved-comments-count',
                    'value' => Comment::query()->where('status', CommentStatus::PENDING)->count(),
                ];

                return $data;
            }, 1, 2);

            if (is_plugin_active('blog')) {
                PostForm::extend(function (PostForm $form): void {
                    $form->add(
                        'allow_comments',
                        OnOffCheckboxField::class,
                        CheckboxFieldOption::make()
                            ->label(trans('plugins/fob-comment::comment.allow_comments'))
                            ->metadata()
                            ->defaultValue(true)
                            ->toArray()
                    );
                });
            }

            if (class_exists(FormFrontManager::class)) {
                FormFrontManager::register(CommentForm::class, CommentRequest::class);
                FormFrontManager::register(ReplyCommentForm::class, ReplyCommentRequest::class);
            }

            $this->app['events']->listen([DeactivatedPlugin::class, RemovedPlugin::class], function (DeactivatedPlugin|RemovedPlugin $event): void {
                if ($event->plugin === 'member') {
                    Comment::query()->where('author_type', 'Botble\Member\Models\Member')->update([
                        'author_id' => null,
                        'author_type' => null,
                    ]);
                }
            });
        });
    }
}
