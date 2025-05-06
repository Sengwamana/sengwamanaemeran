<?php

namespace Botble\Page\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Page\Models\Page;
use Botble\Page\Services\PageService;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Events\RenderingSingleEvent;
use Botble\Theme\Facades\Theme;

class PublicController extends BaseController
{
    public function getPage(string $slug, PageService $pageService)
    {
        $slug = SlugHelper::getSlug($slug, SlugHelper::getPrefix(Page::class));

        abort_unless($slug, 404);

        $data = $pageService->handleFrontRoutes($slug);

        if (isset($data['slug']) && $data['slug'] !== $slug->key) {
            return redirect()->to(url(SlugHelper::getPrefix(Page::class) . '/' . $data['slug']));
        }

        event(new RenderingSingleEvent($slug));

        return Theme::scope($data['view'], $data['data'], $data['default_view'])->render();
    }
}
