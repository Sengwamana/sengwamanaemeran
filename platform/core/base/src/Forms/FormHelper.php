<?php

namespace Botble\Base\Forms;

use Illuminate\Contracts\View\Factory as View;
use Illuminate\Translation\Translator;
use InvalidArgumentException;
use Kris\LaravelFormBuilder\Fields\FormField as BaseFormField;
use Kris\LaravelFormBuilder\FormHelper as BaseFormHelper;

class FormHelper extends BaseFormHelper
{
    protected array $customTypes = [];

    public function __construct(View $view, Translator $translator, array $config = [])
    {
        parent::__construct($view, $translator, $config);

        $this->loadCustomTypes();
    }

    protected function loadCustomTypes(): void
    {
        $customFields = (array) $this->getConfig('custom_fields');

        if (! empty($customFields)) {
            foreach ($customFields as $fieldName => $fieldClass) {
                $this->addCustomField($fieldName, $fieldClass);
            }
        }
    }

    public function getFieldType($type)
    {
        $types = array_keys(static::$availableFieldTypes);

        if (! $type || trim($type) == '') {
            throw new InvalidArgumentException('Field type must be provided.');
        }

        if ($this->hasCustomField($type)) {
            return $this->customTypes[$type];
        }

        if (in_array($type, $types, true)) {
            $namespace = __NAMESPACE__ . '\\FieldTypes\\';

            return $namespace . static::$availableFieldTypes[$type];
        }

        if (class_exists($type)) {
            if (! is_subclass_of($type, BaseFormField::class)) {
                throw new InvalidArgumentException(
                    sprintf('Could not load type "%s": class is not a subclass of "%s".', $type, BaseFormField::class)
                );
            }

            return $type;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Unsupported field type [%s]. Available types are: %s',
                $type,
                join(', ', array_merge($types, array_keys($this->customTypes)))
            )
        );
    }

    public function hasCustomField(string $name): bool
    {
        return array_key_exists($name, $this->customTypes);
    }

    public function addCustomField(string $name, string $class): string
    {
        if (! $this->hasCustomField($name)) {
            return $this->customTypes[$name] = $class;
        }

        throw new InvalidArgumentException('Custom field [' . $name . '] already exists on this form object.');
    }
}
