<?php

namespace Botble\Portfolio\Models;

use Botble\Base\Models\BaseModel;
use Botble\Portfolio\Enums\QuoteStatus;

class Quote extends BaseModel
{
    protected $table = 'pf_quotes';

    protected $fillable = [
        'name',
        'email',
        'fields',
        'message',
        'status',
    ];

    protected $casts = [
        'fields' => 'json',
        'status' => QuoteStatus::class,
    ];
}
