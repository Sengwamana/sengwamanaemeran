<?php

namespace Botble\Base\Forms\FieldOptions;

class MediaImageFieldOption extends ImageFieldOption
{
    protected bool $allowThumb = true;

    protected ?string $previewImage = null;

    public function getPreviewImage(): ?string
    {
        return $this->previewImage;
    }

    public function previewImage(?string $previewImage): static
    {
        $this->previewImage = $previewImage;

        return $this;
    }

    public function isAllowThumb(): bool
    {
        return $this->allowThumb;
    }

    public function allowThumb(bool $allowThumb): static
    {
        $this->allowThumb = $allowThumb;

        return $this;
    }

    public function toArray(): array
    {
        $data = parent::toArray();

        if (! $this->allowThumb) {
            $data['attr']['allow_thumb'] = false;
        }

        if ($this->previewImage) {
            $data['attr']['preview_image'] = $this->previewImage;
        }

        return $data;
    }
}
