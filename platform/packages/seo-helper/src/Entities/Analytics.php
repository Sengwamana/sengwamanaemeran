<?php

namespace Botble\SeoHelper\Entities;

use Botble\SeoHelper\Contracts\Entities\AnalyticsContract;
use Botble\Theme\Supports\ThemeSupport;

/**
 * @deprecated since 7.3.0 use ThemeSupport::renderGoogleTagManagerScript() instead.
 */
class Analytics implements AnalyticsContract
{
    protected ?string $google = '';

    public function setGoogle($code): static
    {
        $this->google = $code;

        return $this;
    }

    public function render(): string
    {
        return implode(PHP_EOL, array_filter([
            $this->renderGoogleScript(),
        ]));
    }

    public function __toString()
    {
        return $this->render();
    }

    protected function renderGoogleScript(): string
    {
        return ThemeSupport::renderGoogleTagManagerScript();
    }
}
