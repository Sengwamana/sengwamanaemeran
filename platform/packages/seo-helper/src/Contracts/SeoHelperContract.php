<?php

namespace Botble\SeoHelper\Contracts;

interface SeoHelperContract extends RenderableContract
{
    public function meta(): SeoMetaContract;

    public function setSeoMeta(SeoMetaContract $seoMeta): self;

    public function openGraph(): SeoOpenGraphContract;

    public function setSeoOpenGraph(SeoOpenGraphContract $seoOpenGraph): self;

    public function twitter(): SeoTwitterContract;

    public function setSeoTwitter(SeoTwitterContract $seoTwitter): self;

    public function setTitle(?string $title, ?string $siteName = null, ?string $separator = null): self;

    public function getTitle(): ?string;

    public function setDescription(?string $description): self;

    public function setImage(?string $image): self;
}
