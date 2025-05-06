<?php

namespace Botble\Setting\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Setting\Models\Setting;
use LogicException;

abstract class SettingForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Setting::class)
            ->template('core/setting::forms.form')
            ->setActionButtons(view('core/setting::forms.partials.action', ['form' => $this->getFormOption('id')])->render())
            ->setMethod('PUT');
    }

    protected function setSectionTitle(string $title): static
    {
        $this->setFormOption('section_title', $title);

        return $this;
    }

    protected function setSectionDescription(string $description): static
    {
        $this->setFormOption('section_description', $description);

        return $this;
    }

    public function getValidatorClass(): string
    {
        return $this->validatorClass ?: throw new LogicException(
            sprintf('Validator class is not defined for %s::class', $this::class)
        );
    }

    public function contentOnly(): static
    {
        $this->template('core/setting::forms.form-content-only');

        return $this;
    }
}
