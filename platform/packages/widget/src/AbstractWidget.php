<?php

namespace Botble\Widget;

use Botble\Theme\Facades\Theme;
use Botble\Widget\Facades\WidgetGroup as WidgetGroupFacade;
use Botble\Widget\Forms\WidgetForm;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class AbstractWidget
{
    private array $config = [];

    private array $extraAdminConfig = [];

    private string $frontendTemplate = 'frontend';

    private string $backendTemplate = 'backend';

    protected ?string $theme = null;

    protected array|Collection $data = [];

    protected bool $loaded = false;

    protected ?WidgetGroup $group = null;

    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            $this->config[$key] = $value;
        }
    }

    public function getWidgetDirectory(): ?string
    {
        $reflection = new ReflectionClass($this);

        return File::basename(File::dirname($reflection->getFilename()));
    }

    public function getConfig(string $name = null, $default = null): array|int|string|null
    {
        if ($name) {
            return Arr::get($this->config, $name, $default);
        }

        return $this->config;
    }

    protected function adminConfig(): array
    {
        return $this->extraAdminConfig;
    }

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run(): ?string
    {
        if ($this->checkIfMissingPlugins()) {
            return '';
        }

        $widgetGroup = app('botble.widget-group-collection');
        $widgetGroup->load();
        $widgetGroupData = $widgetGroup->getData();

        Theme::uses(Theme::getThemeName());

        $args = func_get_args();

        $this->group = WidgetGroupFacade::group($args[0]);

        $data = $widgetGroupData
            ->where('widget_id', $this->getId())
            ->where('sidebar_id', $this->group->getId())
            ->where('position', $args[1])
            ->first();

        if (! empty($data)) {
            $this->config = array_merge($this->config, $data->data);
        }

        $viewData = array_merge([
            'config' => $this->config,
            'sidebar' => $args[0],
            'position' => $data->position,
            'widgetId' => $data->widget_id,
        ], $this->data());

        $html = null;

        $widgetDirectory = $this->getWidgetDirectory();
        $namespace = Str::afterLast($this->frontendTemplate, '.');

        if (View::exists(Theme::getThemeNamespace('widgets.' . $widgetDirectory . '.templates.' . $namespace))) {
            $html = Theme::loadPartial(
                $namespace,
                Theme::getThemeNamespace('/../widgets/' . $widgetDirectory . '/templates'),
                $viewData
            );
        } elseif (view()->exists($this->frontendTemplate)) {
            $html = view($this->frontendTemplate, $viewData)->render();
        }

        return apply_filters('widget_rendered', $html, $this);
    }

    public function getId(): string
    {
        return $this::class;
    }

    public function form(?string $sidebarId = null, int $position = 0): ?string
    {
        if ($this->checkIfMissingPlugins()) {
            return '';
        }

        Theme::uses(Theme::getThemeName());

        if (! empty($sidebarId)) {
            $widgetGroup = app('botble.widget-group-collection');
            $widgetGroup->load();
            $widgetGroupData = $widgetGroup->getData();

            $data = $widgetGroupData
                ->where('widget_id', $this->getId())
                ->where('sidebar_id', $sidebarId)
                ->where('position', $position)
                ->first();

            if (! empty($data)) {
                $this->config = array_merge($this->config, $data->data);
            }
        }

        $settingForm = $this->settingForm();

        return $settingForm instanceof WidgetForm ? $settingForm->renderForm() : $settingForm;
    }

    protected function settingForm(): WidgetForm|string|null
    {
        $widgetDirectory = $this->getWidgetDirectory();
        $namespace = Str::afterLast($this->backendTemplate, '.');

        if (View::exists(Theme::getThemeNamespace('widgets.' . $widgetDirectory . '.templates.' . $namespace))) {
            return Theme::loadPartial(
                $namespace,
                Theme::getThemeNamespace('/../widgets/' . $widgetDirectory . '/templates'),
                array_merge([
                    'config' => $this->config,
                ], $this->adminConfig())
            );
        }

        if (! view()->exists($this->backendTemplate)) {
            return null;
        }

        return view($this->backendTemplate, array_merge([
            'config' => $this->config,
        ], $this->adminConfig()))->render();
    }

    protected function data(): array|Collection
    {
        return $this->data;
    }

    protected function setBackendTemplate(string $template): self
    {
        $this->backendTemplate = $template;

        return $this;
    }

    protected function setFrontendTemplate(string $template): self
    {
        $this->frontendTemplate = $template;

        return $this;
    }

    public function getGroup(): ?WidgetGroup
    {
        return $this->group;
    }

    protected function requiredPlugins(): array
    {
        return [];
    }

    protected function checkIfMissingPlugins(): bool
    {
        if (! empty($this->requiredPlugins())) {
            foreach ($this->requiredPlugins() as $plugin) {
                if (! is_plugin_active($plugin)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function setConfigs(array $config): void
    {
        $this->config = [
            ...$this->config,
            ...$config,
        ];
    }
}
