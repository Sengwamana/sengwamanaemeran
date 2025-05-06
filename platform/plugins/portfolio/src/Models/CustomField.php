<?php

namespace Botble\Portfolio\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\Portfolio\Enums\CustomFieldType;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustomField extends BaseModel
{
    protected $table = 'pf_custom_fields';

    protected $fillable = [
        'author_type',
        'author_id',
        'name',
        'placeholder',
        'required',
        'type',
        'order',
        'is_global',
    ];

    protected $casts = [
        'type' => CustomFieldType::class,
        'is_global' => 'bool',
        'order' => 'int',
        'name' => SafeContent::class,
        'required' => 'bool',
    ];

    protected static function booted(): void
    {
        static::deleting(function (CustomField $model): void {
            $model->options()->delete();
        });
    }

    public function author(): MorphTo
    {
        return $this->morphTo();
    }

    public function options(): HasMany
    {
        return $this->hasMany(CustomFieldOption::class, 'custom_field_id');
    }

    public function saveOptions(array $options): void
    {
        if (in_array($this->type, [CustomFieldType::TEXT, CustomFieldType::NUMBER, CustomFieldType::TEXTAREA])) {
            $options = [];
        }

        $formattedOptions = [];

        $this
            ->options()
            ->whereNotIn('id', array_column($options, 'id'))
            ->delete();

        foreach ($options as $item) {
            $option = null;

            if (isset($item['id'])) {
                $option = $this->options()->find($item['id']);
                $option->fill($item);
            }

            if (! $option) {
                $option = new CustomFieldOption($item);
            }

            if ($option->isDirty()) {
                $formattedOptions[] = $option;
            }
        }

        if (! empty($formattedOptions)) {
            $this->options()->saveMany($formattedOptions);
        }
    }
}
