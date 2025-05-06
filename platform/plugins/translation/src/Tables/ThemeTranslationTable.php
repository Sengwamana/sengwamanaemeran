<?php

namespace Botble\Translation\Tables;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\Html;
use Botble\Base\Supports\Language;
use Botble\DataSynchronize\Table\HeaderActions\ExportHeaderAction;
use Botble\DataSynchronize\Table\HeaderActions\ImportHeaderAction;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Columns\Column;
use Botble\Translation\Manager;
use Illuminate\Support\Arr;

class ThemeTranslationTable extends TableAbstract
{
    protected string $locale = 'en';

    public function setup(): void
    {
        parent::setup();

        $this->setView('core/table::base-table');
        $this->pageLength = 100;
        $this->hasOperations = false;

        Assets::addScripts(['bootstrap-editable'])
            ->addStyles(['bootstrap-editable']);

        $this
            ->addHeaderActions([
                ExportHeaderAction::make()
                    ->route('tools.data-synchronize.export.theme-translations.index')
                    ->permission('theme-translations.export'),
                ImportHeaderAction::make()
                    ->route('tools.data-synchronize.import.theme-translations.index')
                    ->permission('other-translations.import'),
            ])
            ->onAjax(function () {
                $translations = collect(app(Manager::class)->getThemeTranslations($this->locale))
                    ->transform(fn ($value, $key) => compact('key', 'value'));

                $table = $this->table
                    ->of($translations)
                    ->editColumn('key', fn (array $item) => $this->formatKeyAndValue($item['key']))
                    ->editColumn(
                        $this->locale,
                        fn (array $item) => Html::link('#edit', $this->formatKeyAndValue($item['value']), [
                            'class' => 'editable' . ($item['key'] === $item['value'] ? ' text-info' : ''),
                            'data-locale' => $this->locale,
                            'data-name' => $item['key'],
                            'data-type' => 'textarea',
                            'data-pk' => $this->locale,
                            'data-title' => trans('plugins/translation::translation.edit_title'),
                            'data-url' => route('translations.theme-translations.post'),
                        ])
                    );

                return $this->toJson($table);
            });
    }

    public function columns(): array
    {
        return [
            Column::make('key')
                ->alignStart(),
            Column::make($this->locale)
                ->title(Arr::get(Language::getAvailableLocales(), $this->locale . '.name', $this->locale))
                ->alignStart(),
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

    public function isSimpleTable(): bool
    {
        return false;
    }
}
