<?php

namespace Botble\Base\Traits\Forms;

use Illuminate\Support\HtmlString;

trait HasColumns
{
    public function columns(int|array $columns = 2): static
    {
        if (! is_array($columns)) {
            $columns = [
                'lg' => $columns,
            ];
        }

        $this->setFormOption('columns', [
            ...($this->columns ?? []),
            ...$columns,
        ]);

        return $this;
    }

    public function getColumns(?string $breakpoint = null): array|string|null
    {
        $columns = $this->getFormOption('columns', [
            'default' => 1,
            'sm' => null,
            'md' => null,
            'lg' => null,
            'xl' => null,
            'xxl' => null,
        ]);

        if ($breakpoint !== null) {
            return $columns[$breakpoint] ?? null;
        }

        return $columns;
    }

    public function getOpenWrapperFormColumns(): ?HtmlString
    {
        $columns = $this->getFormOption('columns');

        if (! $columns) {
            return null;
        }

        return new HtmlString(view('core/base::forms.columns.form-open-wrapper', [
            'form' => $this,
        ]));
    }

    public function getCloseWrapperFormColumns(): ?HtmlString
    {
        $columns = $this->getFormOption('columns');

        if (! $columns) {
            return null;
        }

        return new HtmlString('</div>');
    }
}
