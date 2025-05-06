<?php

namespace Botble\Menu\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Models\BaseModel;
use Botble\Media\Facades\RvMedia;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\HtmlString;

class MenuNode extends BaseModel
{
    protected $table = 'menu_nodes';

    protected $fillable = [
        'menu_id',
        'parent_id',
        'reference_id',
        'reference_type',
        'url',
        'icon_font',
        'title',
        'css_class',
        'target',
        'has_child',
        'position',
    ];

    protected $casts = [
        'title' => SafeContent::class,
        'url' => SafeContent::class,
        'css_class' => SafeContent::class,
        'icon_font' => SafeContent::class,
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuNode::class, 'parent_id');
    }

    public function child(): HasMany
    {
        return $this->hasMany(MenuNode::class, 'parent_id')->orderBy('position');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo()->with(['slugable']);
    }

    protected function url(): Attribute
    {
        return Attribute::get(function ($value) {
            $value = html_entity_decode(BaseHelper::clean($value));

            if ($value) {
                return apply_filters(MENU_FILTER_NODE_URL, $value);
            }

            if (! $this->reference_type) {
                return '/';
            }

            if (! $this->reference) {
                return '/';
            }

            return (string) $this->reference->url;
        });
    }

    protected function title(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($value) {
                    return $value;
                }

                if (! $this->reference_type || ! $this->reference) {
                    return $value;
                }

                return $this->reference->name;
            },
            set: fn ($value) => str_replace('&amp;', '&', $value),
        );
    }

    protected function iconHtml(): Attribute
    {
        return Attribute::make(
            get: function () {
                $iconImage = $this->getMetaData('icon_image', true);

                if ($iconImage) {
                    return RvMedia::image($iconImage, 'icon', attributes: ['class' => 'menu-icon-image' . ($this->title ? ' me-1' : '')]);
                }

                $icon = $this->icon_font;

                if (! $icon) {
                    return null;
                }

                if (BaseHelper::hasIcon($icon)) {
                    $icon = BaseHelper::renderIcon($icon, attributes: ['class' => $this->title ? ' me-1' : '']);
                } else {
                    $icon = BaseHelper::clean(sprintf('<i class="%s"></i>', $icon . ($this->title ? ' me-1' : '')));
                }

                return new HtmlString($icon);
            },
        );
    }

    protected function active(): Attribute
    {
        return Attribute::get(fn () => rtrim(url($this->url), '/') == rtrim(Request::url(), '/'));
    }
}
