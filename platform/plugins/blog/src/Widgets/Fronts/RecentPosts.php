<?php

namespace Botble\Blog\Widgets\Fronts;

class RecentPosts extends Posts
{
    public function __construct()
    {
        parent::__construct();

        $this->setConfigs([
            'name' => __('Recent Posts'),
            'description' => __('Display recent blog posts'),
            'type' => 'recent',
        ]);
    }
}
