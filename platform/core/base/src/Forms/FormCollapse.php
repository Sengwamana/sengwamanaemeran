<?php

namespace Botble\Base\Forms;

use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Closure;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;

/**
 * @deprecated Use `collapsible()` in FormFieldOptions::class instead.
 */
class FormCollapse
{
    use Conditionable;
    use Tappable;

    protected string $targetFieldName;

    protected string $targetFieldType = OnOffCheckboxField::class;

    protected array $targetFieldOption = [];

    protected bool $targetFieldModify = false;

    protected string $targetFieldValue = '1';

    protected array $fieldsetCallback = [];

    protected ?Closure $beforeRegisterFieldset = null;

    protected ?Closure $afterRegisterFieldset = null;

    protected bool $isOpened = false;

    public function __construct()
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function targetField(
        string $fieldName,
        string $fieldType = OnOffCheckboxField::class,
        array|FormFieldOptions $fieldOptions = [],
        bool $fieldModify = false
    ): static {
        $this->targetFieldName = $fieldName;
        $this->targetFieldType = $fieldType;
        $this->targetFieldOption = is_array($fieldOptions)
            ? $fieldOptions
            : $fieldOptions->toArray();

        $this->targetFieldModify = $fieldModify;

        return $this;
    }

    public function fieldset(
        Closure $callback,
        ?string $targetFieldName = null,
        ?string $targetFieldValue = null,
        ?bool $isOpened = null
    ): static {
        $this->fieldsetCallback[] = [
            'callback' => $callback,
            'targetFieldName' => $targetFieldName,
            'targetFieldValue' => $targetFieldValue,
            'isOpened' => $isOpened,
        ];

        return $this;
    }

    public function targetValue(string|bool $targetValue): static
    {
        $this->targetFieldValue = $targetValue;

        return $this;
    }

    public function isOpened(bool $isOpened = true): static
    {
        $this->isOpened = $isOpened;

        return $this;
    }

    public function build(FormAbstract $form): void
    {
        $form->add($this->targetFieldName, $this->targetFieldType, $this->targetFieldOption, $this->targetFieldModify);

        foreach ($this->fieldsetCallback as $fieldsetCallback) {
            $this->buildFieldset($form, $fieldsetCallback);
        }
    }

    protected function buildFieldset(FormAbstract $form, array $fieldset): void
    {
        $form->addOpenCollapsible(
            $fieldName = $fieldset['targetFieldName'] ?? $this->targetFieldName,
            $fieldValue = $fieldset['targetFieldValue'] ?? $this->targetFieldValue,
            $fieldset['isOpened'] ? $fieldValue : null
        );

        if (isset($fieldset['callback']) && is_callable($fieldset['callback'])) {
            call_user_func($fieldset['callback'], $form);
        }

        $form->addCloseCollapsible($fieldName, $fieldValue);
    }
}
