<?php

namespace Botble\Portfolio\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Portfolio\Enums\PackageDuration;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Package extends BaseModel
{
    protected $table = 'pf_packages';

    protected $fillable = [
        'name',
        'description',
        'content',
        'price',
        'annual_price',
        'duration',
        'features',
        'status',
        'is_popular',
        'action_url',
        'action_label',
        'order',
    ];

    protected $casts = [
        'duration' => PackageDuration::class,
        'status' => BaseStatusEnum::class,
        'is_popular' => 'boolean',
    ];

    protected function featureList(): Attribute
    {
        return Attribute::get(function () {
            $features = collect(explode(PHP_EOL, $this->features));

            return $features->map(function ($feature) {
                return [
                    'value' => trim($feature, '+- '),
                    'is_available' => str_starts_with($feature, '+'),
                ];
            });
        })->shouldCache();
    }
}
