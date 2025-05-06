<?php

namespace Botble\SeoHelper\Entities;

use Botble\Base\Facades\BaseHelper;
use Botble\SeoHelper\Contracts\Entities\TitleContract;
use Botble\SeoHelper\Exceptions\InvalidArgumentException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Title implements TitleContract
{
    protected ?string $title = '';

    protected string $siteName = '';

    protected string $separator = '-';

    protected bool $titleFirst = true;

    protected int $max = 55;

    public function __construct()
    {
        $this->init();
    }

    protected function init(): void
    {
        $this->set(null);
        $this->title = theme_option('site_title');

        if (theme_option('show_site_name', false)) {
            $this->setSiteName($this->title);
        }

        $separator = theme_option('site_title_separator', '-');

        if (! $separator) {
            $separator = config('packages.seo-helper.general.title.separator', '-');
        }

        $this->setSeparator($separator);
        $this->switchPosition(config('packages.seo-helper.general.title.first', true));
        $this->setMax(config('packages.seo-helper.general.title.max', 55));
    }

    public function getTitleOnly(): ?string
    {
        return $this->title;
    }

    public function set(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSiteName(): string
    {
        return $this->siteName;
    }

    public function setSiteName($siteName): static
    {
        $this->siteName = $siteName;

        return $this;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function setSeparator($separator): static
    {
        $this->separator = trim($separator);

        return $this;
    }

    /**
     * Set title first.
     *
     * @return Title
     */
    public function setFirst()
    {
        return $this->switchPosition(true);
    }

    public function setLast(): Title
    {
        return $this->switchPosition(false);
    }

    protected function switchPosition($first): Title
    {
        $this->titleFirst = boolval($first);

        return $this;
    }

    /**
     * Check if title is first.
     *
     * @return bool
     */
    public function isTitleFirst()
    {
        return $this->titleFirst;
    }

    /**
     * Get title max length.
     *
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Set title max length.
     *
     * @param int $max
     *
     * @return Title
     * @throws InvalidArgumentException
     */
    public function setMax($max)
    {
        $this->checkMax($max);

        $this->max = $max;

        return $this;
    }

    /**
     * Make a Title instance.
     *
     * @param string $title
     * @param string $siteName
     * @param string $separator
     *
     * @return Title
     */
    public static function make($title, $siteName = '', $separator = '-')
    {
        return new self();
    }

    public function getTitle(): string
    {
        $separator = null;
        if ($this->getTitleOnly()) {
            $separator = $this->renderSeparator();
        }
        $output = $this->isTitleFirst()
            ? $this->renderTitleFirst($separator)
            : $this->renderTitleLast($separator);

        $output = Str::limit(strip_tags((string) $output), $this->getMax());

        return BaseHelper::html($output);
    }

    public function render(): string
    {
        return '<title>' . $this->getTitle() . '</title>';
    }

    protected function renderSeparator(): string
    {
        return empty($separator = $this->getSeparator()) ? ' ' : ' ' . $separator . ' ';
    }

    public function __toString()
    {
        return $this->render();
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function checkMax(string|int $max): void
    {
        if (! is_int($max)) {
            throw new InvalidArgumentException('The title maximum length must be integer.');
        }

        if ($max <= 0) {
            throw new InvalidArgumentException('The title maximum length must be greater 0.');
        }
    }

    protected function renderTitleFirst(?string $separator): ?string
    {
        $output = [];
        $output[] = $this->getTitleOnly();

        if ($this->hasSiteName()) {
            $output[] = $separator;
            $output[] = $this->getSiteName();
        }

        $output = array_unique($output);

        if (count($output) > 2) {
            return implode('', array_unique($output));
        }

        return Arr::first($output);
    }

    protected function renderTitleLast(string $separator): string
    {
        $output = [];

        if ($this->hasSiteName()) {
            $output[] = $this->getSiteName();
            $output[] = $separator;
        }

        $output[] = $this->getTitleOnly();

        return implode('', $output);
    }

    protected function hasSiteName(): bool
    {
        return ! empty($this->getSiteName());
    }
}
