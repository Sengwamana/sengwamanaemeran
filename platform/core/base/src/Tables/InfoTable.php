<?php

namespace Botble\Base\Tables;

use Botble\Base\Supports\SystemManagement;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Columns\FormattedColumn;

class InfoTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->setView($this->simpleTableView())
            ->addColumns([
                FormattedColumn::make('name')
                    ->title(trans('core/base::system.package_name') . ' : ' . trans('core/base::system.version'))
                    ->alignStart()
                    ->getValueUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();

                        return view('core/base::system.partials.info-package-line', compact('item'))->render();
                    }),
                FormattedColumn::make('dependencies')
                    ->title(trans('core/base::system.dependency_name') . ' : ' . trans('core/base::system.version'))
                    ->alignStart()
                    ->getValueUsing(function (FormattedColumn $column) {
                        $item = $column->getItem();

                        return view('core/base::system.partials.info-dependencies-line', compact('item'))->render();
                    }),
            ])
            ->onAjax(function () {
                $composerArray = SystemManagement::getComposerArray();
                $packages = SystemManagement::getPackagesAndDependencies($composerArray['require']);

                return $this->toJson($this->table->of(collect($packages)));
            });
    }
}
