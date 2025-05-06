<?php

namespace Botble\Blog\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Http\Request;

class PublicController extends BaseController
{
    public function getSearch(Request $request, PostInterface $postRepository)
    {
        $query = BaseHelper::stringify($request->input('q'));

        $title = __('Search result for: ":query"', compact('query'));

        SeoHelper::setTitle($title)
            ->setDescription($title);

        $posts = $postRepository->getSearch($query, 0, (int) theme_option('number_of_posts_in_a_category', 12));

        Theme::breadcrumb()->add($title, route('public.search'));

        return Theme::scope('search', compact('posts'))
            ->render();
    }
}
