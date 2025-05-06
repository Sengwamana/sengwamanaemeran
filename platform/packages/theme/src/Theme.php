<?php

namespace Botble\Theme;

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Media\Facades\RvMedia;
use Botble\Setting\Facades\Setting;
use Botble\Theme\Contracts\Theme as ThemeContract;
use Botble\Theme\Exceptions\UnknownPartialFileException;
use Botble\Theme\Exceptions\UnknownThemeException;
use Botble\Theme\Supports\SocialLink;
use Botble\Theme\Supports\ThemeSupport;
use Botble\Theme\Typography\Typography;
use Carbon\CarbonInterface;
use Closure;
use Illuminate\Config\Repository;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Response;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Illuminate\View\Factory;
use Symfony\Component\HttpFoundation\Cookie;
use Throwable;

class Theme implements ThemeContract
{
    public static string $namespace = 'theme';

    protected array $themeConfig = [];

    protected ?string $theme = null;

    protected ?string $inheritTheme = null;

    protected string $layout;

    protected string $content;

    protected array $regions = [];

    protected array $arguments = [];

    protected array $bindings = [];

    protected ?Cookie $cookie = null;

    protected array $widgets = [];

    protected array $bodyAttributes = [];

    protected array $htmlAttributes = [];

    protected Typography $typography;

    public function __construct(
        protected Repository $config,
        protected Dispatcher $events,
        protected Factory $view,
        protected Asset $asset,
        protected Filesystem $files,
        protected Breadcrumb $breadcrumb
    ) {
        $this->uses($this->getThemeName())->layout(setting('layout', 'default'));
    }

    public function layout(string $layout): self
    {
        // If layout name is not set, so use default from config.
        if ($layout) {
            $this->layout = $layout;
        }

        return $this;
    }

    /**
     * Alias of theme method.
     */
    public function uses(?string $theme = null): self
    {
        return $this->theme($theme);
    }

    /**
     * Set up a theme name.
     */
    public function theme(?string $theme = null): self
    {
        // If theme name is not set, so use default from config.
        if ($theme) {
            $this->theme = $theme;
        }

        // Is theme ready?
        if (! $this->exists($theme) && ! app()->runningInConsole() && ! AdminHelper::isInAdmin(true)) {
            throw new UnknownThemeException('Theme [' . $theme . '] not found.');
        }

        $this->inheritTheme = $this->getConfig('inherit');

        // If inherit theme is set and not exists, so throw exception.
        if ($this->hasInheritTheme() && ! $this->exists($this->getInheritTheme()) && ! AdminHelper::isInAdmin(true)) {
            throw new UnknownThemeException('Parent theme [' . $this->getInheritTheme() . '] not found.');
        }

        // Add location to look up view.
        $this->addPathLocation($this->path());

        // Fire event before set up a theme.
        $this->fire('before', $this);

        // Before from a public theme config.
        $this->fire('appendBefore', $this);

        // Add asset path to asset container.
        $this->registerAssetsPath();

        return $this;
    }

    protected function registerAssetsPath(): void
    {
        $assetsPath = $this->getThemeAssetsPath();

        $this->asset->addPath($assetsPath . '/' . $this->getConfig('containerDir.asset'));
    }

    public function hasInheritTheme(): bool
    {
        return $this->inheritTheme !== null;
    }

    public function getInheritTheme(): ?string
    {
        return $this->inheritTheme;
    }

    protected function getThemeAssetsPath(): string
    {
        $publicThemeName = $this->getPublicThemeName();

        $currentTheme = $this->getThemeName();

        $assetPath = $this->path();

        if ($publicThemeName != $currentTheme) {
            $assetPath = substr($assetPath, 0, -strlen($currentTheme)) . $publicThemeName;
        }

        return $assetPath;
    }

    /**
     * Check theme exists.
     */
    public function exists(?string $theme): bool
    {
        $path = platform_path($this->path($theme)) . '/';

        return File::isDirectory($path);
    }

    public function path(?string $forceThemeName = null): string
    {
        $themeDir = $this->getConfig('themeDir');

        $theme = $forceThemeName ?: $this->theme;

        return $themeDir . '/' . $theme;
    }

    /**
     * Get theme config.
     */
    public function getConfig(?string $key = null): mixed
    {
        if (! $this->themeConfig) {
            $this->themeConfig = $this->config->get('packages.theme.general', []);
        }

        $this->loadConfigFromTheme($this->theme);

        $this->themeConfig = $this->evaluateConfig($this->themeConfig);

        return empty($key) ? $this->themeConfig : Arr::get($this->themeConfig, $key);
    }

    public function getInheritConfig(?string $key = null): mixed
    {
        if (! $this->hasInheritTheme()) {
            return null;
        }

        $this->loadConfigFromTheme($theme = $this->getInheritTheme());

        if (! isset($this->themeConfig['themes'][$theme])) {
            return null;
        }

        $config = $this->themeConfig['themes'][$theme];

        return empty($key) ? $config : Arr::get($config, $key);
    }

    protected function loadConfigFromTheme(string $theme): void
    {
        // Config inside a public theme.
        // This config having buffer by array object.
        if ($theme && ! isset($this->themeConfig['themes'][$theme])) {
            $this->themeConfig['themes'][$theme] = [];

            // Require public theme config.
            $minorConfigPath = theme_path($theme . '/config.php');

            if ($this->files->exists($minorConfigPath)) {
                $this->themeConfig['themes'][$theme] = $this->files->getRequire($minorConfigPath);
            }
        }
    }

    /**
     * Evaluate config.
     *
     * Config minor is at public folder [theme]/config.php,
     * they can be overridden package config.
     */
    protected function evaluateConfig(array $config): array
    {
        if (! isset($config['themes'][$this->theme])) {
            return $config;
        }

        // Config inside a public theme.
        $minorConfig = $config['themes'][$this->theme];

        // Before event is special case, It's combination.
        if (isset($minorConfig['events']['before'])) {
            $minorConfig['events']['appendBefore'] = $minorConfig['events']['before'];
            unset($minorConfig['events']['before']);
        }

        // Merge two config into one.
        $config = array_replace_recursive($config, $minorConfig);

        // Reset theme config.
        $config['themes'][$this->theme] = [];

        return $config;
    }

    /**
     * Add location path to look up.
     */
    protected function addPathLocation(string $location): void
    {
        // First path is in the selected theme.
        $hints[] = platform_path($location);

        // This is nice feature to use inherit from another.
        if ($this->hasInheritTheme()) {
            $inheritPath = platform_path($this->path($this->getInheritTheme()));

            if ($this->files->isDirectory($inheritPath)) {
                $hints[] = $inheritPath;
            }
        }

        // Add namespace with hinting paths.
        $this->view->addNamespace($this->getThemeNamespace(), $hints);
    }

    public function getThemeNamespace(string $path = ''): string
    {
        // Namespace relate with the theme name.
        $namespace = static::$namespace . '.' . $this->getThemeName();

        if ($path) {
            return $namespace . '::' . $path;
        }

        return $namespace;
    }

    public function getThemeName(): string
    {
        if ($this->theme) {
            return $this->theme;
        }

        $theme = setting('theme');

        if ($theme) {
            return $theme;
        }

        return Arr::first(BaseHelper::scanFolder(theme_path()));
    }

    public function setThemeName(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getPublicThemeName(): string
    {
        $theme = $this->getThemeName();

        $publicThemeName = $this->getConfig('public_theme_name');

        if ($publicThemeName && $publicThemeName != $theme) {
            return $publicThemeName;
        }

        return $theme;
    }

    /**
     * Fire event to config listener.
     */
    public function fire(string $event, string|array|callable|null|object $args): void
    {
        if ($this->hasInheritTheme()) {
            $this->asset->isInheritTheme();

            $onEvent = $this->getInheritConfig('events.' . $event);

            if ($onEvent instanceof Closure) {
                $onEvent($args);
            }

            $this->asset->isInheritTheme(false);
        }

        $onEvent = $this->getConfig('events.' . $event);

        if ($onEvent instanceof Closure) {
            $onEvent($args);
        }

        $this->events->dispatch('theme.' . $event, $args);
    }

    /**
     * Return breadcrumb instance.
     */
    public function breadcrumb(): Breadcrumb
    {
        if (! $this->breadcrumb->getCrumbs()) {
            $this->breadcrumb->add(__('Home'), BaseHelper::getHomepageUrl());
        }

        return $this->breadcrumb;
    }

    /**
     * Append a place to existing region.
     */
    public function append(string $region, string $value): self
    {
        return $this->appendOrPrepend($region, $value);
    }

    /**
     * Append or prepend existing region.
     */
    protected function appendOrPrepend(string $region, string $value, string $type = 'append'): self
    {
        // If region not found, create a new region.
        if (isset($this->regions[$region])) {
            switch ($type) {
                case 'prepend':
                    $this->regions[$region] = $value . $this->regions[$region];

                    break;
                case 'append':
                    $this->regions[$region] .= $value;

                    break;
            }
        } else {
            $this->set($region, $value);
        }

        return $this;
    }

    /**
     * Set a place to regions.
     */
    public function set(string $region, mixed $value): self
    {
        // Content is reserve region for render sub-view.
        if ($region != 'content') {
            $this->regions[$region] = $value;
        }

        return $this;
    }

    /**
     * Prepend a place to existing region.
     */
    public function prepend(string $region, string $value): self
    {
        return $this->appendOrPrepend($region, $value, 'prepend');
    }

    /**
     * Binding data to view.
     */
    public function bind(string $variable, string|array|callable|null $callback = null)
    {
        $name = 'bind.' . $variable;

        // If callback pass, so put in a queue.
        if (! empty($callback)) {
            // Preparing callback in to queues.
            $this->events->listen($name, function () use ($callback) {
                return ($callback instanceof Closure) ? $callback() : $callback;
            });
        }

        // Passing variable to closure.
        $events = &$this->events;
        $bindings = &$this->bindings;

        // Buffer processes to save request.
        return Arr::get($this->bindings, $name, function () use (&$events, &$bindings, $name) {
            $response = current($events->dispatch($name));
            Arr::set($bindings, $name, $response);

            return $response;
        });
    }

    /**
     * Check having binded data.
     */
    public function binded(string $variable): bool
    {
        $name = 'bind.' . $variable;

        return $this->events->hasListeners($name);
    }

    /**
     * Assign data across all views.
     */
    public function share(string $key, $value)
    {
        return $this->view->share($key, $value);
    }

    /**
     * The same as "partial", but having prefix layout.
     */
    public function partialWithLayout(string $view, array $args = []): ?string
    {
        $view = $this->getLayoutName() . '.' . $view;

        return $this->partial($view, $args);
    }

    public function getLayoutName(): string
    {
        return $this->layout;
    }

    /**
     * Set up a partial.
     */
    public function partial(string $view, array $args = []): ?string
    {
        $partialDir = $this->getThemeNamespace($this->getConfig('containerDir.partial'));

        return $this->loadPartial($view, $partialDir, $args);
    }

    /**
     * Load a partial
     */
    public function loadPartial(string $view, string $partialDir, array $args): ?string
    {
        $path = $partialDir . '.' . $view;

        if (! $this->view->exists($path)) {
            throw new UnknownPartialFileException('Partial view [' . $view . '] not found.');
        }

        $partial = $this->view->make($path, $args)->render();
        $this->regions[$view] = $partial;

        return $this->regions[$view];
    }

    /**
     * Watch and set up a partial from anywhere.
     *
     * This method will first try to load the partial from current theme. If partial
     * is not found in theme then it loads it from app (i.e. app/views/partials)
     */
    public function watchPartial(string $view, array $args = []): ?string
    {
        try {
            return $this->partial($view, $args);
        } catch (UnknownPartialFileException) {
            $partialDir = $this->getConfig('containerDir.partial');

            return $this->loadPartial($view, $partialDir, $args);
        }
    }

    /**
     * Hook a partial before rendering.
     */
    public function partialComposer(string|array $view, Closure $callback, ?string $layout = null): void
    {
        $partialDir = $this->getConfig('containerDir.partial');

        $view = (array) $view;

        // Partial path with namespace.
        $path = $this->getThemeNamespace($partialDir);

        // This code support partialWithLayout.
        if (! empty($layout)) {
            $path = $path . '.' . $layout;
        }

        $view = array_map(function ($item) use ($path) {
            return $path . '.' . $item;
        }, $view);

        $this->view->composer($view, $callback);
    }

    /**
     * Hook a partial before rendering.
     */
    public function composer(string|array $view, Closure $callback, ?string $layout = null): void
    {
        $partialDir = $this->getConfig('containerDir.view');

        if (! is_array($view)) {
            $view = [$view];
        }

        // Partial path with namespace.
        $path = $this->getThemeNamespace($partialDir);

        // This code support partialWithLayout.
        if (! empty($layout)) {
            $path = $path . '.' . $layout;
        }

        $view = array_map(function ($item) use ($path) {
            return $path . '.' . $item;
        }, $view);

        $this->view->composer($view, $callback);
    }

    /**
     * Render a region.
     */
    public function place(string $region, ?string $default = null): ?string
    {
        return $this->get($region, $default);
    }

    /**
     * Render a region.
     */
    public function get(string $region, ?string $default = null)
    {
        if ($this->has($region)) {
            return $this->regions[$region];
        }

        return $default ?: '';
    }

    /**
     * Check region exists.
     */
    public function has(string $region): bool
    {
        return isset($this->regions[$region]);
    }

    /**
     * Place content in sub-view.
     */
    public function content(): ?string
    {
        return $this->regions['content'];
    }

    /**
     * Return asset instance.
     */
    public function asset(): Asset|AssetContainer
    {
        return $this->asset;
    }

    /**
     * The same as "of", but having prefix layout.
     */
    public function ofWithLayout(string $view, array $args = []): self
    {
        $view = $this->getLayoutName() . '.' . $view;

        return $this->of($view, $args);
    }

    /**
     * Set up a content to template.
     */
    public function of(string $view, array $args = []): self
    {
        $this->fireEventGlobalAssets();

        // Keeping arguments.
        $this->arguments = $args;

        $content = $this->view->make($view, $args)->render();

        // View path of content.
        $this->content = $view;

        // Set up a content regional.
        $this->regions['content'] = $content;

        return $this;
    }

    /**
     * Container view.
     *
     * Using a container module view inside a theme, this is
     * useful when you separate a view inside a theme.
     */
    public function scope(string $view, array $args = [], $default = null)
    {
        $viewDir = $this->getConfig('containerDir.view');

        // Add namespace to find in a theme path.
        $path = $this->getThemeNamespace($viewDir . '.' . $view);

        if ($this->view->exists($path)) {
            return $this->setUpContent($path, $args);
        }

        if (! empty($default)) {
            return $this->of($default, $args);
        }

        $this->handleViewNotFound($path);
    }

    /**
     * Set up a content to template.
     */
    public function setUpContent(string $view, array $args = []): self
    {
        $this->fireEventGlobalAssets();

        // Keeping arguments.
        $this->arguments = $args;

        try {
            $content = $this->view->make($view, $args)->render();
        } catch (Throwable $exception) {
            if (App::hasDebugModeEnabled()) {
                throw $exception;
            }

            report($exception);

            $content = str_replace(base_path('/'), '', $exception->getMessage());
        }

        // View path of content.
        $this->content = $view;

        // Set up a content regional.
        $this->regions['content'] = $content;

        return $this;
    }

    protected function handleViewNotFound(string $path): void
    {
        if (app()->isLocal() && app()->hasDebugModeEnabled()) {
            $path = str_replace($this->getThemeNamespace(), $this->getThemeName(), $path);
            $file = str_replace('::', '/', str_replace('.', '/', $path));
            dd(
                'This theme has not supported this view, please create file "' . theme_path(
                    $file
                ) . '.blade.php" to render this page!'
            );
        }

        abort(404);
    }

    /**
     * Load subview from direct path.
     */
    public function load(string $view, array $args = []): self
    {
        $view = ltrim($view, '/');

        $segments = explode('/', str_replace('.', '/', $view));

        // Pop file from segments.
        $view = array_pop($segments);

        // Custom directory path.
        $pathOfView = app('path.base') . '/' . implode('/', $segments);

        // Add temporary path with a hint type.
        $this->view->addNamespace('custom', $pathOfView);

        return $this->setUpContent('custom::' . $view, $args);
    }

    /**
     * Get all arguments assigned to content.
     */
    public function getContentArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Get a argument assigned to content.
     */
    public function getContentArgument(string $key, $default = null)
    {
        return Arr::get($this->arguments, $key, $default);
    }

    /**
     * Checking content argument existing.
     */
    public function hasContentArgument(string $key): bool
    {
        return isset($this->arguments[$key]);
    }

    /**
     * Find view location.
     */
    public function location(bool $realPath = false): ?string
    {
        if ($this->view->exists($this->content)) {
            return $realPath ? $this->view->getFinder()->find($this->content) : $this->content;
        }

        return null;
    }

    /**
     * Return a template with content.
     */
    public function render(int $statusCode = 200): Response
    {
        // Fire the event before render.
        $this->fire('after', $this);

        // Flush asset that need to serve.
        $this->asset->flush();

        // Layout directory.
        $layoutDir = $this->getConfig('containerDir.layout');

        $path = $this->getThemeNamespace($layoutDir . '.' . $this->layout);

        if (! $this->view->exists($path)) {
            $this->handleViewNotFound($path);
        }

        $content = $this->view->make($path)->render();

        // Append status code to view.
        $content = new Response($content, $statusCode);

        // Having cookie set.
        if ($this->cookie) {
            $content->withCookie($this->cookie);
        }

        $content->withHeaders([
            'CMS-Version' => get_core_version(),
            'Authorization-At' => Setting::get('membership_authorization_at'),
            'Activated-License' => ! empty(Setting::get('licensed_to')) ? 'Yes' : 'No',
        ]);

        return $content;
    }

    public function header(): string
    {
        if (! empty($this->breadcrumb->crumbs)) {
            $schema = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [],
            ];

            $index = 1;

            foreach ($this->breadcrumb->crumbs as $item) {
                $schema['itemListElement'][] = [
                    '@type' => 'ListItem',
                    'position' => $index,
                    'name' => BaseHelper::clean($item['label']),
                    'item' => $item['url'],
                ];

                $index++;
            }

            $schema = json_encode($schema, JSON_UNESCAPED_UNICODE);

            $this
                ->asset()
                ->container('header')
                ->writeScript('breadcrumb-schema', $schema, attributes: ['type' => 'application/ld+json']);
        }

        return $this->view->make('packages/theme::partials.header')->render();
    }

    public function footer(): string
    {
        return $this->view->make('packages/theme::partials.footer')->render();
    }

    /**
     * Magic method for set, prepend, append, has, get.
     */
    public function __call(string $method, array $parameters = [])
    {
        $callable = preg_split('|[A-Z]|', $method);

        if (in_array($callable[0], ['set', 'prepend', 'append', 'has', 'get'])) {
            $value = lcfirst(preg_replace('|^' . $callable[0] . '|', '', $method));
            array_unshift($parameters, $value);

            return call_user_func_array([$this, $callable[0]], $parameters);
        }

        return trigger_error('Call to undefined method ' . __CLASS__ . '::' . $method . '()', E_USER_ERROR);
    }

    public function routes(): void
    {
        require package_path('theme/routes/public.php');
    }

    public function registerRoutes(Closure|callable $closure): Router
    {
        return Route::group(['middleware' => ['web', 'core']], function () use ($closure): void {
            Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), fn () => $closure());
        });
    }

    public function loadView(string $view): string
    {
        return $this->view->make($this->getThemeNamespace('views') . '.' . $view)->render();
    }

    public function getStyleIntegrationPath(): string
    {
        return public_path($this->getThemeAssetsPath() . '/css/style.integration.css');
    }

    public function fireEventGlobalAssets(): self
    {
        $this->fire('asset', $this->asset);

        // Fire event before render theme.
        $this->fire('beforeRenderTheme', $this);

        // Fire event before render layout.
        $this->fire('beforeRenderLayout.' . $this->layout, $this);

        return $this;
    }

    public function getThemeScreenshot(string $theme): string
    {
        $publicThemeName = Theme::getPublicThemeName();

        $themeName = Theme::getThemeName() == $theme && $publicThemeName ? $publicThemeName : $theme;

        $screenshot = public_path($this->getConfig('themeDir') . '/' . $themeName . '/screenshot.png');

        if (! File::exists($screenshot)) {
            $screenshot = $this->path($theme) . '/screenshot.png';
        }

        if (! File::exists($screenshot)) {
            return RvMedia::getDefaultImage();
        }

        return 'data:image/png;base64,' . base64_encode(File::get($screenshot));
    }

    public function registerThemeIconFields(array $icons, array $css = [], array $js = []): void
    {
        ThemeSupport::registerThemeIconFields($icons, $css, $js);
    }

    public function registerFacebookIntegration(): void
    {
        ThemeSupport::registerFacebookIntegration();
    }

    public function registerSocialLinks(): void
    {
        ThemeSupport::registerSocialLinks();
    }

    public function getSocialLinksRepeaterFields(): array
    {
        return ThemeSupport::getSocialLinksRepeaterFields();
    }

    /**
     * @return array<SocialLink>
     */
    public function getSocialLinks(): array
    {
        return ThemeSupport::getSocialLinks();
    }

    public function convertSocialLinksToArray(array|string|null $data): array
    {
        if (! $data) {
            return [];
        }

        return ThemeSupport::convertSocialLinksToArray($data);
    }

    public function getThemeIcons(): array
    {
        return ThemeSupport::getThemeIcons();
    }

    public function addBodyAttributes(array $bodyAttributes): static
    {
        $this->bodyAttributes = [...$this->bodyAttributes, ...$bodyAttributes];

        return $this;
    }

    public function getBodyAttribute(string $attribute): ?string
    {
        return $this->bodyAttributes[$attribute] ?? null;
    }

    public function getBodyAttributes(): array
    {
        return $this->bodyAttributes;
    }

    public function bodyAttributes(): string
    {
        if (BaseHelper::isRtlEnabled()) {
            $this->bodyAttributes['dir'] = 'rtl';
        }

        if ($this->get('bodyClass')) {
            $this->bodyAttributes['class'] = $this->get('bodyClass');
        }

        return apply_filters('theme_body_attributes', Html::attributes($this->bodyAttributes));
    }

    public function addHtmlAttributes(array $htmlAttributes): static
    {
        $this->htmlAttributes = [...$this->htmlAttributes, ...$htmlAttributes];

        return $this;
    }

    public function getHtmlAttribute(string $attribute): ?string
    {
        return $this->htmlAttributes[$attribute] ?? null;
    }

    public function getHtmlAttributes(): array
    {
        return $this->htmlAttributes;
    }

    public function htmlAttributes(): string
    {
        $lang = str_replace('_', '-', app()->getLocale());

        if ($lang) {
            $this->addHtmlAttributes(['lang' => $lang]);
        }

        return apply_filters('theme_html_attributes', Html::attributes($this->htmlAttributes));
    }

    public function registerPreloader(): void
    {
        ThemeSupport::registerPreloader();
    }

    public function getPreloaderVersions(): array
    {
        return ThemeSupport::getPreloaderVersions();
    }

    public function registerToastNotification(): void
    {
        ThemeSupport::registerToastNotification();
    }

    public function getSiteCopyright(): ?string
    {
        return ThemeSupport::getSiteCopyright();
    }

    public function getLogo(string $logoKey = 'logo'): ?string
    {
        return apply_filters('theme_logo', theme_option($logoKey));
    }

    public function getFavicon(): ?string
    {
        return apply_filters('theme_favicon', theme_option('favicon'));
    }

    public function getSiteTitle(): ?string
    {
        return apply_filters('theme_site_title', theme_option('site_title'));
    }

    public function getLogoImage(
        array $attributes = [],
        string $logoKey = 'logo',
        int $maxHeight = 0,
        ?string $logoUrl = null
    ): ?HtmlString {
        if ($logoUrl) {
            $logo = $logoUrl;
        } else {
            $logo = $this->getLogo($logoKey);
        }

        if (! $logo) {
            return null;
        }

        $height = theme_option('logo_height') ?: $maxHeight;

        if ($height) {
            $attributes['style'] = sprintf('max-height: %s', is_numeric($height) ? "{$height}px" : $height);
        }

        return apply_filters('theme_logo_image', RvMedia::image($logo, $this->getSiteTitle(), attributes: $attributes, lazy: false));
    }

    public function formatDate(CarbonInterface|string|int|null $date, ?string $format = null): ?string
    {
        return ThemeSupport::formatDate($date, $format);
    }

    public function typography(): Typography
    {
        $this->typography ??= new Typography();

        return $this->typography;
    }

    public function renderSocialSharing(?string $url = null, ?string $title = null, ?string $thumbnail = null): string
    {
        return ThemeSupport::renderSocialSharingButtons($url, $title, $thumbnail);
    }

    public function termAndPrivacyPolicyUrl(): ?string
    {
        return theme_option('term_and_privacy_policy_url');
    }
}
