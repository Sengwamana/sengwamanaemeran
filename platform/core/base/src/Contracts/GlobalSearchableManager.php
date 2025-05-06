<?php

namespace Botble\Base\Contracts;

use Illuminate\Support\Collection;

interface GlobalSearchableManager
{
    /**
     * @param class-string<GlobalSearchableProvider> $provider
     */
    public function registerProvider(string $provider): static;

    /**
     * @return Collection<GlobalSearchableResult>
     */
    public function search(string $keyword, int $limit = 20): Collection;
}
