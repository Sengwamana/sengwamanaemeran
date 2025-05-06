<?php

namespace ArchiElite\Announcement\Models;

use Botble\Base\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Announcement extends BaseModel
{
    protected $fillable = [
        'name',
        'content',
        'has_action',
        'action_label',
        'action_url',
        'action_open_new_tab',
        'dismissible',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'dismissible' => 'bool',
        'has_action' => 'bool',
        'action_open_new_tab' => 'bool',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'bool',
    ];

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', Carbon::now());
            })
            ->where(function (Builder $query): void {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', Carbon::now());
            });
    }

    protected function formattedContent(): Attribute
    {
        return Attribute::get(
            fn () => preg_replace('/<p[^>]*>([\s\S]*?)<\/p[^>]*>/', '$1', $this->content)
        )->shouldCache();
    }

    protected function isAvailable(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->is_active) {
                return false;
            }

            if ($this->start_date || $this->end_date) {
                if (! $this->end_date) {
                    return $this->start_date->isPast();
                }

                if (! $this->start_date) {
                    return $this->end_date->isFuture();
                }

                return $this->start_date->isPast() && $this->end_date->isFuture();
            }

            return true;
        })->shouldCache();
    }
}
