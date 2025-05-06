<?php

namespace Botble\Shortcode\Compilers;

use Botble\Shortcode\View\View;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ShortcodeCompiler
{
    protected bool $enabled = false;

    protected bool $strip = false;

    protected array $matches = [];

    protected array $registered = [];

    protected string $editLink;

    public function enable(): self
    {
        $this->enabled = true;

        return $this;
    }

    public function disable(): self
    {
        $this->enabled = false;

        return $this;
    }

    public function setEditLink(string $editLink, string $permission): void
    {
        if ($permission && (! Auth::guard()->check() || ! Auth::guard()->user()->hasPermission($permission))) {
            return;
        }

        $this->editLink = $editLink;
    }

    public function getEditLink(): ?string
    {
        if (! isset($this->editLink)) {
            return null;
        }

        do_action('shortcode_set_edit_link', $this, $this->editLink);

        return $this->editLink;
    }

    public function add(
        string $key,
        ?string $name,
        ?string $description = null,
        string|null|callable|array $callback = null,
        string $previewImage = ''
    ): void {
        $shortcode = compact('key', 'name', 'description', 'callback', 'previewImage');

        $this->registered[$key] = isset($this->registered[$key])
            ? [...$this->registered[$key], ...$shortcode]
            : $shortcode;
    }

    public function setPreviewImage(string $key, string $previewImage): void
    {
        if (! $this->hasShortcode($key)) {
            return;
        }

        $this->registered[$key]['previewImage'] = $previewImage;
    }

    public function remove(string $key): void
    {
        if ($this->hasShortcode($key)) {
            unset($this->registered[$key]);
        }
    }

    public function compile(string $value, bool $force = false): string
    {
        // Only continue is shortcode have been registered
        if ((! $this->enabled || ! $this->hasShortcodes()) && ! $force) {
            return $value;
        }

        // Set empty result
        $result = '';

        // Here we will loop through all the tokens returned by the Zend lexer and
        // parse each one into the corresponding valid PHP. We will then have this
        // template as the correctly rendered PHP that can be rendered natively.
        foreach (token_get_all($value) as $token) {
            $result .= is_array($token) ? $this->parseToken($token) : $token;
        }

        return $result;
    }

    public function hasShortcodes(): bool
    {
        return ! empty($this->registered);
    }

    public function hasShortcode(string $key): bool
    {
        return Arr::has($this->registered, $key);
    }

    protected function parseToken(array $token): string
    {
        [$id, $content] = $token;
        if ($id == T_INLINE_HTML) {
            $content = $this->renderShortcodes($content);
        }

        return $content;
    }

    protected function renderShortcodes(string $value): string
    {
        $pattern = $this->getRegex();

        return preg_replace_callback('/' . $pattern . '/s', [$this, 'render'], $value);
    }

    public function render(array $matches): string|null|View
    {
        // Compile the shortcode
        $compiled = $this->compileShortcode($matches);
        $name = $compiled->getName();

        if ($compiled->enable_lazy_loading === 'yes' && ! request()->ajax()) {
            add_filter(THEME_FRONT_FOOTER, function (?string $html) {
                return $html . view('packages/shortcode::partials.lazy-loading-script')->render();
            }, 120);

            $placeholderView = Theme::getThemeNamespace('partials.lazy-loading-placeholder');

            if (! view()->exists($placeholderView)) {
                $placeholderView = 'packages/shortcode::partials.lazy-loading-placeholder';
            }

            return view($placeholderView, [
                'name' => $name,
                'attributes' => Arr::except($compiled->toArray(), 'enable_lazy_loading'),
            ]);
        }

        $callback = apply_filters('shortcode_get_callback', $this->getCallback($name), $name);

        // Render the shortcode through the callback
        return apply_filters(
            'shortcode_content_compiled',
            call_user_func_array($callback, [
                $compiled,
                $compiled->getContent(),
                $this,
                $name,
            ]),
            $name,
            $callback,
            $this
        );
    }

    protected function compileShortcode($matches): Shortcode
    {
        // Set matches
        $this->setMatches($matches);
        // pars the attributes
        $attributes = $this->parseAttributes($this->matches[3]);

        // return shortcode instance
        return new Shortcode(
            $this->getName(),
            $attributes,
            $this->getContent()
        );
    }

    protected function setMatches(array $matches = []): void
    {
        $this->matches = $matches;
    }

    public function getName(): ?string
    {
        return $this->matches[2];
    }

    public function getContent(): ?string
    {
        if (! $this->matches) {
            return null;
        }

        // Compile the content, to support nested shortcode
        return $this->compile($this->matches[5]);
    }

    public function getCallback(string $key): string|null|callable|array
    {
        // Get the callback from the shortcode array
        $callback = $this->registered[$key]['callback'];
        // if is a string
        if (is_string($callback)) {
            // Parse the callback
            [$class, $method] = Str::parseCallback($callback, 'register');
            // If the class exist
            if (class_exists($class)) {
                // return class and method
                return [
                    app($class),
                    $method,
                ];
            }
        }

        return $callback;
    }

    protected function parseAttributes(?string $text): array
    {
        // decode attribute values
        $text = htmlspecialchars_decode($text, ENT_QUOTES);

        $attributes = [];
        // attributes pattern
        $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
        // Match
        if (preg_match_all($pattern, preg_replace('/[\x{00a0}\x{200b}]+/u', ' ', $text), $match, PREG_SET_ORDER)) {
            foreach ($match as $item) {
                if (! empty($item[1])) {
                    $attributes[strtolower($item[1])] = stripcslashes($item[2]);
                } elseif (! empty($item[3])) {
                    $attributes[strtolower($item[3])] = stripcslashes($item[4]);
                } elseif (! empty($item[5])) {
                    $attributes[strtolower($item[5])] = stripcslashes($item[6]);
                } elseif (isset($item[7]) && strlen($item[7])) {
                    $attributes[] = stripcslashes($item[7]);
                } elseif (isset($item[8])) {
                    $attributes[] = stripcslashes($item[8]);
                }
            }
        } else {
            $attributes = ltrim($text);
        }

        return is_array($attributes) ? $attributes : [$attributes];
    }

    public function getShortcodeNames(array $except = []): string
    {
        $shortcodes = Arr::except($this->registered, $except);

        return join('|', array_map('preg_quote', array_keys($shortcodes)));
    }

    protected function getRegex(array $except = []): string
    {
        $name = $this->getShortcodeNames($except);

        return '\\[(\\[?)(' . $name . ')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*+(?:\\[(?!\\/\\2\\])[^\\[]*+)*+)\\[\\/\\2\\])?)(\\]?)';
    }

    /**
     * Remove all shortcode tags from the given content.
     */
    public function strip(?string $content, array $except = []): ?string
    {
        if (empty($this->registered) || ! $content) {
            return $content;
        }

        $pattern = $this->getRegex($except);

        return preg_replace_callback('/' . $pattern . '/s', [$this, 'stripTag'], $content);
    }

    public function getStrip(): bool
    {
        return $this->strip;
    }

    public function setStrip(bool $strip): void
    {
        $this->strip = $strip;
    }

    protected function stripTag(array $match): ?string
    {
        if ($match[1] == '[' && $match[6] == ']') {
            return substr($match[0], 1, -1);
        }

        return $match[1] . $match[6];
    }

    public function getRegistered(): array
    {
        return $this->registered;
    }

    public function setAdminConfig(string $key, string|null|callable|array $html): void
    {
        $this->registered[$key]['admin_config'] = $html;
    }

    public function modifyAdminConfig(string $key, callable $callback): void
    {
        $this->registered[$key]['admin_config_modifier'] = $callback;
    }

    public function getAttributes(string $value): array
    {
        $pattern = $this->getRegex();

        preg_match('/' . $pattern . '/s', $value, $matches);

        if (! $matches) {
            return [];
        }

        $this->setMatches($matches);

        return $this->parseAttributes($this->matches[3]);
    }

    public function whitelistShortcodes(): array
    {
        return apply_filters('core_whitelist_shortcodes', ['media', 'youtube-video']);
    }
}
