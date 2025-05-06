<?php

namespace Botble\Menu;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Facades\MetaBox;
use Botble\Base\Forms\FieldOptions\CoreIconFieldOption;
use Botble\Base\Forms\FieldOptions\InputFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\ColorField;
use Botble\Base\Forms\Fields\CoreIconField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\RepositoryHelper;
use Botble\Menu\Forms\MenuNodeForm;
use Botble\Menu\Http\Requests\MenuRequest;
use Botble\Menu\Models\Menu as MenuModel;
use Botble\Menu\Models\MenuNode;
use Botble\Support\Http\Requests\Request as BaseRequest;
use Botble\Support\Services\Cache\Cache;
use Botble\Theme\Facades\Theme;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Throwable;

class Menu
{
    protected Cache $cache;

    protected array $menuOptionModels = [];

    protected Collection $data;

    protected bool $loaded = false;

    public function __construct(protected Repository $config)
    {
        $this->cache = Cache::make(MenuModel::class);

        $this->addMenuLocation('main-menu', trans('packages/menu::menu.main_navigation'));
    }

    public function hasMenu(string $slug): bool
    {
        $this->load();

        return $this->data
            ->where('slug', $slug)
            ->isNotEmpty();
    }

    public function recursiveSaveMenu(array $menuNodes, int|string $menuId, int|string $parentId): array
    {
        try {
            foreach ($menuNodes as &$row) {
                $child = Arr::get($row, 'children', []);

                foreach ($child as $index => $item) {
                    $child[$index]['menuItem']['position'] = $index;
                }

                $hasChild = ! empty($child);

                $row['menuItem'] = $this->saveMenuNode($row['menuItem'], $menuId, $parentId, $hasChild);

                if (! empty($child) && is_array($child)) {
                    $this->recursiveSaveMenu($child, $menuId, $row['menuItem']['id']);
                }
            }

            return $menuNodes;
        } catch (Exception) {
            return [];
        }
    }

    protected function saveMenuNode(
        array $menuItem,
        int|string $menuId,
        int|string $parentId,
        bool $hasChild = false
    ): array {
        /**
         * @var MenuNode $node
         */

        $node = MenuNode::query()->findOrNew(Arr::get($menuItem, 'id'));

        MenuNodeForm::createFromModel($node)
            ->saving(function (MenuNodeForm $form) use ($hasChild, $parentId, $menuId, $menuItem): void {
                /**
                 * @var MenuNode $node
                 */
                $node = $form->getModel();
                $node->fill($menuItem);
                $node->menu_id = $menuId;
                $node->parent_id = $parentId;
                $node->has_child = $hasChild;

                $node = $this->getReferenceMenuNode($menuItem, $node);
                $node->save();
            });

        $menuItem['id'] = $node->getKey();

        return $menuItem;
    }

    public function getReferenceMenuNode(array $item, MenuNode $menuNode): MenuNode
    {
        switch (Arr::get($item, 'reference_type')) {
            case 'custom-link':
            case '':
                $menuNode->reference_id = 0;
                $menuNode->reference_type = null;
                $menuNode->url = str_replace('&amp;', '&', Arr::get($item, 'url'));

                break;

            default:
                $menuNode->reference_id = (int) Arr::get($item, 'reference_id');
                $menuNode->reference_type = Arr::get($item, 'reference_type');

                if (class_exists($menuNode->reference_type)) {
                    $reference = $menuNode->reference_type::find($menuNode->reference_id);
                    if ($reference) {
                        $menuNode->url = str_replace(url(''), '', $reference->url);
                    }
                }

                break;
        }

        return $menuNode;
    }

    public function addMenuLocation(string $location, string $description): self
    {
        $locations = $this->getMenuLocations();
        $locations[$location] = $description;

        $this->config->set('packages.menu.general.locations', $locations);

        return $this;
    }

    public function getMenuLocations(): array
    {
        return $this->config->get('packages.menu.general.locations', []);
    }

    public function removeMenuLocation(string $location): self
    {
        $locations = $this->getMenuLocations();
        Arr::forget($locations, $location);

        $this->config->set('packages.menu.general.locations', $locations);

        return $this;
    }

    public function renderMenuLocation(string $location, array $attributes = []): ?string
    {
        $this->load();

        $html = '';

        foreach ($this->data as $menu) {
            if (! in_array($location, $menu->locations->pluck('location')->all())) {
                continue;
            }

            $attributes['slug'] = $menu->slug;
            $html .= $this->generateMenu($attributes);
        }

        return $html;
    }

    public function isLocationHasMenu(string $location): bool
    {
        $this->load();

        foreach ($this->data as $menu) {
            if (in_array($location, $menu->locations->pluck('location')->all())) {
                return true;
            }
        }

        return false;
    }

    public function load(bool $force = false): void
    {
        if (! $this->loaded || $force) {
            $this->data = $this->read();
            $this->loaded = true;
        }
    }

    protected function read(): Collection
    {
        $with = apply_filters('cms_menu_load_with_relations', [
            'menuNodes',
            'menuNodes.child',
            'locations',
        ]);

        $items = MenuModel::query()
            ->wherePublished()
            ->with($with);

        return RepositoryHelper::applyBeforeExecuteQuery($items, new MenuModel())->get();
    }

    public function generateMenu(array $args = []): ?string
    {
        $this->load();

        $view = Arr::get($args, 'view');

        $theme = Arr::get($args, 'theme', true);

        $cacheKey = 'menu_location_' . md5(serialize(BaseHelper::getHomepageUrl()) . serialize($args));

        $cacheEnabled = setting('cache_front_menu_enabled', true);

        if ($cacheEnabled && $this->cache->has($cacheKey) && ($cachedData = $this->cache->get($cacheKey))) {
            $data = $cachedData;
        } else {
            $menu = Arr::get($args, 'menu');

            $slug = Arr::get($args, 'slug');
            if (! $menu && ! $slug) {
                return null;
            }

            $parentId = Arr::get($args, 'parent_id', 0);

            if (! $menu) {
                $menu = $this->data->where('slug', $slug)->first();
            }

            if (! $menu) {
                $menu = RepositoryHelper::applyBeforeExecuteQuery(
                    MenuModel::query()->where('slug', $slug),
                    new MenuModel(),
                    true
                )->first();
            }

            if (! $menu) {
                return null;
            }

            if (! Arr::has($args, 'menu_nodes')) {
                $menuNodes = $menu->menuNodes->where('parent_id', $parentId);
            } else {
                $menuNodes = Arr::get($args, 'menu_nodes', []);
            }

            if ($menuNodes instanceof Collection) {
                try {
                    $menuNodes->loadMissing('reference');
                } catch (Throwable) {
                }
            }

            $menuNodes = $menuNodes->sortBy('position');

            $data = [
                'menu' => $menu,
                'menu_nodes' => $menuNodes,
            ];

            $data['options'] = Html::attributes(Arr::get($args, 'options', []));
        }

        if ($theme && $view) {
            return Theme::partial($view, $data);
        }

        if ($view) {
            return view($view, $data)->render();
        }

        return view('packages/menu::partials.default', $data)->render();
    }

    public function registerMenuOptions(string $model, string $name): void
    {
        $options = Menu::generateSelect([
            'model' => new $model(),
            'options' => [
                'class' => 'list-unstyled list-item',
            ],
        ]);

        echo view('packages/menu::menu-options', compact('options', 'name'));
    }

    public function generateSelect(array $args = []): ?string
    {
        /**
         * @var BaseModel|Builder $model
         */
        $model = Arr::get($args, 'model');

        $options = Html::attributes(Arr::get($args, 'options', []));

        if (! Arr::has($args, 'items')) {
            if (method_exists($model, 'children')) {
                $items = $model
                    ->where('parent_id', Arr::get($args, 'parent_id', 0))
                    ->with(['children', 'children.children'])
                    ->orderBy('name');
            } else {
                $items = $model->orderBy('name');
            }

            if (Arr::get($args, 'active', true)) {
                $items = $items->where('status', BaseStatusEnum::PUBLISHED);
            }

            $items = apply_filters(BASE_FILTER_BEFORE_GET_ADMIN_LIST_ITEM, $items, $model, $model::class)->get();
        } else {
            $items = Arr::get($args, 'items', []);
        }

        if (empty($items)) {
            return null;
        }

        return view('packages/menu::partials.select', compact('items', 'model', 'options'))->render();
    }

    public function addMenuOptionModel(string $model): self
    {
        $this->menuOptionModels[] = $model;

        return $this;
    }

    public function getMenuOptionModels(): array
    {
        return $this->menuOptionModels;
    }

    public function setMenuOptionModels(array $models): self
    {
        $this->menuOptionModels = $models;

        return $this;
    }

    public function clearCacheMenuItems(): self
    {
        try {
            $nodes = MenuNode::query()->get();

            foreach ($nodes as $node) {
                if (! $node->reference_type ||
                    ! class_exists($node->reference_type) ||
                    ! $node->reference_id ||
                    ! $node->reference
                ) {
                    continue;
                }

                $node->url = rtrim(str_replace(url(''), '', $node->reference->url), '/');

                if ($node->url === rtrim(url(''), '/')) {
                    $node->url = '/';
                }

                $node->save();
            }
        } catch (Exception $exception) {
            BaseHelper::logError($exception);
        }

        return $this;
    }

    public function useMenuItemIconImage(): void
    {
        FormAbstract::beforeRendering(function (FormAbstract $form): FormAbstract {
            $model = $form->getModel();

            if ($model instanceof MenuNode) {
                $form
                    ->modify(
                        'icon_font',
                        CoreIconField::class,
                        CoreIconFieldOption::make()
                    )
                    ->addAfter('icon_font', 'icon_image', 'mediaImage', [
                        'label' => __('Icon image'),
                        'attr' => [
                            'data-update' => 'icon_image',
                        ],
                        'value' => $model->icon_image ?: $model->getMetaData('icon_image', true),
                        'help_block' => [
                            'text' => __('It will replace Icon Font if it is present.'),
                        ],
                        'wrapper' => [
                            'style' => 'display: block;',
                        ],
                    ]);
            }

            return $form;
        }, 124);

        FormAbstract::beforeSaving(function (FormAbstract $form): void {
            $model = $form->getModel();

            if ($model instanceof MenuNode) {
                $request = $form->getRequest();

                if ($request->has('data.icon_image')) {
                    if ($iconImage = $request->input('data.icon_image')) {
                        MetaBox::saveMetaBoxData($model, 'icon_image', $iconImage);
                    } else {
                        MetaBox::deleteMetaData($model, 'icon_image');
                    }

                    return;
                }

                if ($menuNodes = $request->input('menu_nodes')) {
                    $menuNodes = json_decode($menuNodes, true);

                    if ($menuNodes) {
                        $this->saveMenuNodeImages($menuNodes, $model);
                    }
                }
            }
        }, 170);

        add_filter('menu_nodes_item_data', function (MenuNode $data): MenuNode {
            $data->icon_image = $data->getMetaData('icon_image', true);

            return $data;
        }, 170);

        add_filter('cms_menu_load_with_relations', function (array $relations): array {
            return array_merge($relations, ['menuNodes.metadata', 'menuNodes.child.metadata']);
        }, 170);
    }

    public function saveMenuNodeImages(array $nodes, MenuNode $model): void
    {
        foreach ($nodes as $node) {
            if ($node['menuItem']['id'] == $model->getKey() && isset($node['menuItem']['icon_image'])) {
                if ($iconImage = $node['menuItem']['icon_image']) {
                    MetaBox::saveMetaBoxData($model, 'icon_image', $iconImage);
                } else {
                    MetaBox::deleteMetaData($model, 'icon_image');
                }
            }

            if (! empty($node['children'])) {
                $this->saveMenuNodeImages($node['children'], $model);
            }
        }
    }

    public function useMenuItemBadge(): void
    {
        MenuNodeForm::extend(function (MenuNodeForm $form) {
            /**
             * @var MenuNode $menuNode
             */
            $menuNode = $form->getModel();

            $form->add(
                'badge_text',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('packages/menu::menu.badge_text'))
                    ->value($menuNode->getMetaData('badge_text', true))
                    ->toArray()
            )
                ->add(
                    'badge_color',
                    ColorField::class,
                    InputFieldOption::make()
                        ->value($menuNode->getMetaData('badge_color', true) ?: '#ffffff')
                        ->label(trans('packages/menu::menu.badge_color'))
                        ->toArray()
                );

            return $form;
        });

        MenuNodeForm::beforeSaving(function (FormAbstract $form) {
            $model = $form->getModel();

            if ($model instanceof MenuNode) {
                $request = $form->getRequest();

                if ($menuNodes = $request->input('menu_nodes')) {
                    $menuNodes = json_decode($menuNodes, true);

                    $this->saveMenuNodeBadges($menuNodes, $model);
                }
            }

            return $form;
        }, 170);

        add_filter('core_request_rules', function (array $rules, BaseRequest $request) {
            if ($request instanceof MenuRequest) {
                $rules['badge_text'] = ['nullable', 'string'];
                $rules['badge_color'] = ['nullable', 'string'];
            }

            return $rules;
        }, 10, 2);
    }

    public function saveMenuNodeBadges(array $nodes, MenuNode $model): void
    {
        foreach ($nodes as $node) {
            if ($node['menuItem']['id'] == $model->getKey()) {
                if (isset($node['menuItem']['badge_text'])) {
                    MetaBox::saveMetaBoxData($model, 'badge_text', $node['menuItem']['badge_text']);
                }

                if (isset($node['menuItem']['badge_color'])) {
                    MetaBox::saveMetaBoxData($model, 'badge_color', $node['menuItem']['badge_color']);
                }
            }

            if (! empty($node['children'])) {
                $this->saveMenuNodeBadges($node['children'], $model);
            }
        }
    }
}
