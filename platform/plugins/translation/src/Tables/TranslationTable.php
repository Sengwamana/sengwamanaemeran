<?php

namespace Botble\Translation\Tables;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\Html;
use Botble\Base\Supports\Language;
use Botble\DataSynchronize\Table\HeaderActions\ExportHeaderAction;
use Botble\DataSynchronize\Table\HeaderActions\ImportHeaderAction;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\BulkChanges\SelectBulkChange;
use Botble\Table\CollectionDataTable;
use Botble\Table\Columns\FormattedColumn;
use Botble\Translation\Services\GetGroupedTranslationsService;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TranslationTable extends TableAbstract
{
    protected string $locale = 'en';

    public function setup(): void
    {
        parent::setup();

        $this->pageLength = 100;

        Assets::addScripts(['bootstrap-editable'])
            ->addStyles(['bootstrap-editable']);

        $this->useDefaultSorting = false;

        $this
            ->setView('core/table::base-table')
            ->addHeaderActions([
                ExportHeaderAction::make()->route('tools.data-synchronize.export.other-translations.index')->permission('other-translations.export'),
                ImportHeaderAction::make()->route('tools.data-synchronize.import.other-translations.index')->permission('other-translations.import'),
            ])
            ->addFilters([
                SelectBulkChange::make()
                    ->name('group')
                    ->title(trans('plugins/translation::translation.group'))
                    ->choices((new GetGroupedTranslationsService())->getGroups()),
            ])
            ->onAjax(function () {
                $translations = (new GetGroupedTranslationsService())->handle();

                if ($this->isFiltering()) {
                    $translations = $translations->filter(function ($item) {
                        $filterColumns = $this->request()->query('filter_columns');
                        $filterOperator = $this->request()->query('filter_operators');
                        $filterValues = $this->request()->query('filter_values');

                        if (empty($filterColumns) || empty($filterOperator) || empty($filterValues)) {
                            return true;
                        }

                        foreach ($filterColumns as $index => $filterColumn) {
                            $filterOperator = $filterOperator[$index];
                            $filterValue = $filterValues[$index];

                            if ($filterOperator === '=') {
                                if (empty($filterValue) || empty($filterColumn)) {
                                    return true;
                                }

                                if ($filterValue === $item['group']) {
                                    return true;
                                }

                                return false;
                            }
                        }

                        return false;
                    });
                }

                if ($this->request()->filled('group')) {
                    $translations = $translations->filter(function ($item) {
                        return $item['group'] === $this->request()->query('group');
                    });
                }

                return $this->toJson(
                    $this
                        ->table
                        ->of($translations)
                        ->filter(function (CollectionDataTable $query) {
                            if ($keyword = $this->request->input('search.value')) {
                                $query->collection = $query->collection->filter(function ($item) use ($keyword) {
                                    return str_contains($item['value'], $keyword);
                                });
                            }

                            return $query;
                        })
                );
            });
    }

    public function columns(): array
    {
        return [
            FormattedColumn::make('group')
                ->title(trans('plugins/translation::translation.group'))
                ->alignStart()
                ->searchable(false)
                ->getValueUsing(function (FormattedColumn $column) {
                    $item = $column->getItem();

                    $group = $item->group;
                    $groupDisplay = $group;

                    if (Str::startsWith($group, 'core/') || Str::startsWith($group, 'packages/')) {
                        $name = Str::headline(Str::slug(Str::afterLast($group, '/')));

                        $groupDisplay = $name . ' (core)';
                    } elseif (Str::startsWith($group, 'plugins/')) {
                        $plugin = Str::beforeLast(Str::after($group, 'plugins/'), '/');

                        $name = Str::afterLast($group, '/');

                        if ($plugin !== $name) {
                            $name = Str::headline(Str::slug($name));

                            $groupDisplay = $name . ' (' . Str::beforeLast(Str::after($group, 'plugins/'), '/') . ')';
                        } else {
                            $groupDisplay = Str::headline(Str::slug($name));
                        }
                    }

                    return Html::tag(
                        'code',
                        $groupDisplay,
                        [
                            'data-bs-toggle' => 'tooltip',
                            'data-bs-original-title' => $group,
                        ]
                    );
                }),
            FormattedColumn::make('key')
                ->title(Arr::get(Language::getAvailableLocales(), 'en.name', 'en'))
                ->alignStart()
                ->searchable(false)
                ->getValueUsing(function (FormattedColumn $column) {
                    $item = $column->getItem();

                    $trans = trans(Str::of($item->group)->replaceLast(DIRECTORY_SEPARATOR, '::')->append(".$item->key")->toString(), [], 'en');

                    return $this->formatKeyAndValue(is_array($trans) ? $item->key : $trans);
                }),
            FormattedColumn::make('value')
                ->title(Arr::get(Language::getAvailableLocales(), "{$this->locale}.name", $this->locale))
                ->alignStart()
                ->getValueUsing(function (FormattedColumn $column) {
                    $item = $column->getItem();

                    $trans = trans(Str::of($item->group)->replaceLast(DIRECTORY_SEPARATOR, '::')->append(".$item->key")->toString(), [], $this->locale);

                    $value = $this->formatKeyAndValue(is_array($trans) ? $item->value : $trans);

                    return Html::link('#edit', $value, [
                        'class' => sprintf('editable locale-%s', $this->locale),
                        'data-locale' => $this->locale,
                        'data-name' => sprintf('%s|%s', $this->locale, $item->key),
                        'data-type' => 'textarea',
                        'data-pk' => $item->key,
                        'data-title' => trans('plugins/translation::translation.edit_title'),
                        'data-url' => route('translations.group.edit', ['group' => $item->group]),
                    ]);
                }),
        ];
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    protected function formatKeyAndValue(?string $value): ?string
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }

    public function htmlDrawCallbackFunction(): ?string
    {
        return parent::htmlDrawCallbackFunction() . 'Botble.initEditable()';
    }
}
