<?php

namespace Botble\Setting\Supports;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

abstract class SettingStore
{
    protected array $data = [];

    protected bool $unsaved = false;

    protected bool $loaded = false;

    protected array $guard = [
        'activated_plugins',
        'theme',
        'licensed_to',
        'media_random_hash',
        'admin_appearance_custom_css',
        'admin_appearance_custom_header_js',
        'admin_appearance_custom_body_js',
        'admin_appearance_custom_footer_js',
    ];

    public function get(string|array $key, mixed $default = null): mixed
    {
        $this->load();

        return Arr::get($this->data, $key, $default);
    }

    public function has(string $key): bool
    {
        $this->load();

        return Arr::has($this->data, $key);
    }

    public function set(string|array $key, mixed $value = null, bool $force = false): self
    {
        $this->load();
        $this->unsaved = true;

        if (! is_array($key)) {
            $key = [$key => $value];
        }

        foreach ($key as $k => $v) {
            if (! $force && in_array($k, $this->guard)) {
                continue;
            }

            Arr::set($this->data, $k, $v);
        }

        return $this;
    }

    public function forceSet(string|array $key, mixed $value = null): self
    {
        return $this->set($key, $value, true);
    }

    public function forget(string $key, bool $force = false): self
    {
        $this->unsaved = true;

        if ($this->has($key)) {
            if (! $force && in_array($key, $this->guard)) {
                return $this;
            }

            Arr::forget($this->data, $key);
        }

        return $this;
    }

    public function forgetAll(): self
    {
        $this->unsaved = true;
        $this->data = [];

        return $this;
    }

    public function all(): array
    {
        $this->load();

        return $this->data;
    }

    public function save(): bool
    {
        if (! $this->unsaved) {
            return false;
        }

        $this->write($this->data);
        $this->unsaved = false;

        return true;
    }

    public function load(bool $force = false): void
    {
        if (! $this->loaded || $force) {
            $this->data = $this->read();
            $this->loaded = true;
        }
    }

    abstract protected function read(): array;

    abstract protected function write(array $data): void;

    abstract public function delete(array|string $keys = [], array $except = [], bool $force = false);

    abstract public function forceDelete(array|string $keys = [], array $except = []);

    abstract public function newQuery(): Builder;
}
