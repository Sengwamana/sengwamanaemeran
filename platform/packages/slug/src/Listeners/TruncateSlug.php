<?php

namespace Botble\Slug\Listeners;

use Botble\Slug\Models\Slug;

class TruncateSlug
{
    public function handle(): void
    {
        Slug::query()->truncate();
    }
}
