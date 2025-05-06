<?php

namespace Botble\Testimonial\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;

class Testimonial extends BaseModel
{
    protected $table = 'testimonials';

    protected $fillable = [
        'name',
        'company',
        'content',
        'image',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'content' => SafeContent::class,
        'company' => SafeContent::class,
        'name' => SafeContent::class,
    ];
}
