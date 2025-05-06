<?php

namespace Botble\Table\Columns\Concerns;

use Botble\Table\Columns\FormattedColumn;
use Illuminate\Support\Str;

trait Maskable
{
    protected bool $mask = false;

    protected string $maskCharacter = '*';

    protected int $maskIndex = 0;

    protected ?int $maskLength = null;

    protected string $maskEncoding = 'UTF-8';

    public function initializeMaskable(): void
    {
        $this->getValueUsing(
            fn (FormattedColumn $column, mixed $value) => $column->applyMaskIfAvailable($value)
        );
    }

    public function mask(string $character = '*', int $index = 0, ?int $length = null, string $encoding = 'UTF-8'): static
    {
        $this->mask = true;
        $this->maskCharacter($character);
        $this->maskIndex($index);
        $this->maskLength($length);
        $this->maskEncoding($encoding);

        return $this;
    }

    public function maskCharacter(string $character): static
    {
        $this->maskCharacter = $character;

        return $this;
    }

    public function maskIndex(int $index): static
    {
        $this->maskIndex = $index;

        return $this;
    }

    public function maskLength(?int $length): static
    {
        $this->maskLength = $length;

        return $this;
    }

    public function maskEncoding(string $encoding): static
    {
        $this->maskEncoding = $encoding;

        return $this;
    }

    public function applyMaskIfAvailable(mixed $text): mixed
    {
        if (! $text || ! $this->mask) {
            return $text;
        }

        return $this->applyMask($text);
    }

    public function applyMask(mixed $text): mixed
    {
        if (! is_string($text)) {
            return $text;
        }

        return Str::mask($text, $this->maskCharacter, $this->maskIndex, $this->maskLength, $this->maskEncoding);
    }
}
