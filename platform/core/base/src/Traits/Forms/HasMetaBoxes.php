<?php

namespace Botble\Base\Traits\Forms;

use Botble\Base\Forms\MetaBox;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;

trait HasMetaBoxes
{
    protected array $metaBoxes = [];

    public function getMetaBoxes(): array
    {
        uasort($this->metaBoxes, function ($before, $after) {
            if (Arr::get($before, 'priority', 0) > Arr::get($after, 'priority', 0)) {
                return 1;
            } elseif (Arr::get($before, 'priority', 0) < Arr::get($after, 'priority', 0)) {
                return -1;
            }

            return 0;
        });

        return $this->metaBoxes;
    }

    public function getMetaBox(string $name): string|View
    {
        if (! Arr::get($this->metaBoxes, $name)) {
            return '';
        }

        $metaBox = $this->metaBoxes[$name];

        if ($metaBox instanceof MetaBox) {
            $metaBox = $metaBox->toArray();
        }

        if (isset($metaBox['content']) && $metaBox['content'] instanceof Closure) {
            $metaBox['content'] = call_user_func($metaBox['content'], $this->getModel());
        }

        $view = view('core/base::forms.partials.meta-box', compact('metaBox'));

        if (Arr::get($metaBox, 'render') === false) {
            return $view;
        }

        return $view->render();
    }

    public function addMetaBoxes(array|string $boxes): static
    {
        if (! is_array($boxes)) {
            $boxes = [$boxes];
        }

        $this->metaBoxes = array_merge($this->metaBoxes, $boxes);

        return $this;
    }

    public function addMetaBox(MetaBox $metaBox): static
    {
        $this->metaBoxes[$metaBox->getId()] = $metaBox;

        return $this;
    }

    public function removeMetaBox(string $name): static
    {
        Arr::forget($this->metaBoxes, $name);

        return $this;
    }
}
