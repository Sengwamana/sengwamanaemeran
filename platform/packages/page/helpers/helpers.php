<?php

use Botble\Base\Models\BaseQueryBuilder;
use Botble\Base\Supports\RepositoryHelper;
use Botble\Page\Models\Page;
use Botble\Page\Supports\Template;
use Illuminate\Database\Eloquent\Collection;

if (! function_exists('get_all_pages')) {
    function get_all_pages(bool $active = true): Collection
    {
        $pages = Page::query()
            ->when($active, function (BaseQueryBuilder $query): void {
                $query->wherePublished();
            })
            ->orderByDesc('created_at')
            ->with('slugable');

        return RepositoryHelper::applyBeforeExecuteQuery($pages, new Page())->get();
    }
}

if (! function_exists('register_page_template')) {
    function register_page_template(array $templates): void
    {
        Template::registerPageTemplate($templates);
    }
}

if (! function_exists('get_page_templates')) {
    function get_page_templates(): array
    {
        return Template::getPageTemplates();
    }
}
