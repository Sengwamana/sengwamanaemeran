<?php

namespace Botble\Base\Models\Concerns;

use Botble\Base\Facades\MetaBox as MetaBoxSupport;
use Botble\Base\Models\MetaBox;
use Botble\Media\Facades\RvMedia;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait HasMetadata
{
    public function metadata(): MorphMany
    {
        return $this
            ->morphMany(MetaBox::class, 'reference')
            ->select([
                'reference_id',
                'reference_type',
                'meta_key',
                'meta_value',
            ]);
    }

    public function getMetaData(string $key, bool $single = false): array|string|null
    {
        $field = $this->metadata
            ->where('meta_key', apply_filters('stored_meta_box_key', $key, $this))
            ->first();

        if (! $field) {
            $field = $this->metadata->where('meta_key', $key)->first();
        }

        if (! $field) {
            return $single ? '' : [];
        }

        return MetaBoxSupport::getMetaData($field, $key, $single);
    }

    public function saveMetaDataFromFormRequest(array|string $fields, Request $request): void
    {
        $fields = is_array($fields) ? $fields : [$fields];

        foreach ($fields as $field) {
            if (! $request->has($field)) {
                continue;
            }

            if ($request->hasFile($field . '_input')) {
                $uploadFolder = $this->upload_folder ?: Str::plural(Str::slug(class_basename($this)));

                $result = RvMedia::handleUpload($request->file($field . '_input'), 0, $uploadFolder);

                if (! $result['error']) {
                    $request->merge([$field => $result['data']->url]);
                }
            }

            if ($request->filled($field)) {
                MetaBoxSupport::saveMetaBoxData($this, $field, $request->input($field));
            } else {
                MetaBoxSupport::deleteMetaData($this, $field);
            }
        }
    }
}
