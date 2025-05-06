<?php

namespace Botble\Base\Forms;

use Closure;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @phpstan-consistent-constructor
 */
class MetaBox implements Arrayable
{
    protected string $title;

    protected ?string $subtitle = null;

    protected ?string $beforeWrapper = null;

    protected ?string $afterWrapper = null;

    protected bool $hasWrapper = true;

    protected bool $hasTable = false;

    protected Closure|string $content;

    protected ?string $headerActionContent = null;

    protected ?string $footerContent = null;

    protected array $attributes = [];

    public function __construct(protected string $id)
    {
    }

    public static function make(string $id): static
    {
        return new static($id);
    }

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function subtitle(string $subtitle): static
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function beforeWrapper(string $beforeWrapper): static
    {
        $this->beforeWrapper = $beforeWrapper;

        return $this;
    }

    public function afterWrapper(string $afterWrapper): static
    {
        $this->afterWrapper = $afterWrapper;

        return $this;
    }

    public function hasWrapper(bool $hasWrapper = true): static
    {
        $this->hasWrapper = $hasWrapper;

        return $this;
    }

    public function hasTable(bool $hasTable = true): static
    {
        $this->hasTable = $hasTable;

        return $this;
    }

    public function content(Closure|string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function headerActionContent(string $headerActionContent): static
    {
        $this->headerActionContent = $headerActionContent;

        return $this;
    }

    public function footerContent(?string $footerContent): static
    {
        $this->footerContent = $footerContent;

        return $this;
    }

    public function attributes(array $attributes): static
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'before_wrapper' => $this->beforeWrapper,
            'after_wrapper' => $this->afterWrapper,
            'wrap' => $this->hasWrapper,
            'has_table' => $this->hasTable,
            'content' => $this->content,
            'header_actions' => $this->headerActionContent,
            'footer' => $this->footerContent,
            'attributes' => $this->attributes,
        ];
    }
}
