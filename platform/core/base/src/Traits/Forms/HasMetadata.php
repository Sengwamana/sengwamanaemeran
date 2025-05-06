<?php

namespace Botble\Base\Traits\Forms;

use Botble\Base\Models\BaseModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Kris\LaravelFormBuilder\Fields\FormField;

trait HasMetadata
{
    protected array $metadataFields;

    public function isMetadataField(FormField $field): bool
    {
        $options = $field->getOptions();

        if (! $options) {
            return false;
        }

        return (bool) Arr::get($options, 'metadata', false);
    }

    public function getMetadataFields(): array
    {
        return $this->metadataFields ??= collect($this->fields)
            ->filter(fn (FormField $field) => $this->isMetadataField($field))
            ->all();
    }

    public function hasMetadataFields(): bool
    {
        return count($this->getMetadataFields()) > 0;
    }

    public function setupMetadataFields(): void
    {
        $model = $this->model;

        if (! $model instanceof BaseModel || ! $model->exists) {
            return;
        }

        if (! $this->hasMetadataFields()) {
            return;
        }

        $model->loadMissing('metadata');

        foreach ($this->getMetadataFields() as $field) {
            $field->setValue(
                $model->getMetaData($this->getMetadataFieldName($field), true)
            );
        }
    }

    public function saveMetadataFields(): void
    {
        if (! $this->model instanceof BaseModel) {
            return;
        }

        if (! $this->hasMetadataFields()) {
            return;
        }

        foreach ($this->getMetadataFields() as $field) {
            $name = $this->getMetadataFieldName($field);

            $this->model->saveMetaDataFromFormRequest($name, $this->getRequest());
        }
    }

    protected function getMetadataFieldName(FormField $field): string
    {
        return Str::before($field->getName(), '[');
    }
}
