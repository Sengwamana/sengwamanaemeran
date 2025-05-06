<?php

namespace Botble\Blog\Services\Abstracts;

use Botble\Blog\Models\Post;
use Illuminate\Http\Request;

abstract class StoreCategoryServiceAbstract
{
    abstract public function execute(Request $request, Post $post): void;
}
