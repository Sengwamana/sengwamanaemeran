<?php

namespace Botble\Table\Abstracts\Concerns;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Models\BaseModel;
use Botble\Table\Abstracts\TableBulkActionAbstract;
use Botble\Table\Abstracts\TableBulkChangeAbstract;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Throwable;

trait HasBulkActions
{
    /**
     * @var \Botble\Table\Abstracts\TableBulkActionAbstract[]|class-string<\Botble\Table\Abstracts\TableBulkActionAbstract>[]
     */
    protected array $bulkActions = [];

    /**
     * @var \Botble\Table\Abstracts\TableBulkActionAbstract[]
     */
    protected array $bulkActionsCaches;

    /**
     * @var array[] $bulkActions
     */
    protected array $bulkChanges = [];

    protected string $bulkChangeUrl = '';

    protected string $bulkChangeDataUrl = '';

    protected string $bulkActionDispatchUrl = '';

    protected Closure $onSavingBulkChangeItemCallback;

    public function bulkActions(): array
    {
        return [];
    }

    public function addBulkAction(TableBulkActionAbstract $bulkAction): static
    {
        $this->bulkActions[] = $bulkAction;

        return $this;
    }

    /**
     * @param \Botble\Table\Abstracts\TableBulkActionAbstract[] $bulkActions
     */
    public function addBulkActions(array $bulkActions): static
    {
        foreach ($bulkActions as $bulkAction) {
            $this->addBulkAction($bulkAction);
        }

        return $this;
    }

    public function hasBulkActions(): bool
    {
        return ! empty($this->getBulkActions());
    }

    public function getBulkActions(): array
    {
        return $this->bulkActionsCaches ??= collect()
            ->when(
                $bulkChanges = $this->getAllBulkChanges(),
                function (Collection $collection) use ($bulkChanges) {
                    return $collection->merge([
                        -1 => view('core/table::bulk-changes', [
                            'bulk_changes' => $bulkChanges,
                            'class' => $this::class,
                            'url' => $this->getBulkChangeUrl(),
                        ])->render(),
                    ]);
                }
            )
            ->merge(array_merge($this->bulkActions(), $this->bulkActions))
            ->mapWithKeys(function ($action, $key) {
                if (is_string($action) && class_exists($action) || $action instanceof TableBulkActionAbstract) {
                    $action = $action instanceof TableBulkActionAbstract ? $action : app($action);
                    $action->table($this);
                    $key = $action::class;
                }

                return [$key => $action];
            })
            ->reject(function ($action) {
                if ($action instanceof TableBulkActionAbstract) {
                    return ! $action->currentUserHasAnyPermissions();
                }

                return false;
            })
            ->sortBy(function ($action, $key) {
                if ($action instanceof TableBulkActionAbstract) {
                    return $action->getPriority();
                }

                return $key;
            })
            ->toArray();
    }

    protected function determineIfBulkActionsRequest(): bool
    {
        $request = $this->request();

        try {
            return $request->ajax()
                && $request->validate([
                    'bulk_action' => ['sometimes', 'required', 'boolean'],
                    'bulk_action_target' => ['required_with:bulk_action' , 'string'],
                    'ids' => ['required_with:bulk_action' , 'array'],
                    'ids.*' => ['required'],
                ])
                && class_exists($request->input('bulk_action_target'));
        } catch (ValidationException) {
            return false;
        }
    }

    protected function findBulkAction(string $bulkAction): TableBulkActionAbstract|false
    {
        if (class_exists($bulkAction) && key_exists($bulkAction, $this->getBulkActions())) {
            return $this->bulkActionsCaches[$bulkAction];
        }

        return false;
    }

    public function dispatchBulkAction(): BaseHttpResponse
    {
        $bulkAction = $this->findBulkAction(
            $this->request()->input('bulk_action_target')
        );

        $ids = Arr::wrap($this->request()->input('ids'));

        if (! $bulkAction) {
            return (new BaseHttpResponse())
                ->setError()
                ->setMessage(trans('core/table::invalid_bulk_action'));
        }

        if (empty($ids)) {
            return (new BaseHttpResponse())
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        try {
            $model = $this->getModel();

            $bulkAction->handleBeforeDispatch($model, $ids);

            $response = $bulkAction->dispatch($model, $ids);

            return tap($response, fn () => $bulkAction->handleAfterDispatch($model, $ids));
        } catch (Throwable $exception) {
            return (new BaseHttpResponse())
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getBulkChanges(): array
    {
        return [];
    }

    public function addBulkChange(TableBulkChangeAbstract $bulkChange): static
    {
        $this->bulkChanges[] = $bulkChange;

        return $this;
    }

    /**
     * @param array<int|string, TableBulkChangeAbstract> $bulkChanges
     */
    public function addBulkChanges(array $bulkChanges): static
    {
        foreach ($bulkChanges as $bulkChange) {
            $this->addBulkChange($bulkChange);
        }

        return $this;
    }

    public function getAllBulkChanges(): array
    {
        $bulkChanges = array_merge($this->getBulkChanges(), $this->bulkChanges);

        foreach ($bulkChanges as $key => $bulkChange) {
            if ($bulkChange instanceof TableBulkChangeAbstract) {
                if ($bulkChange->getName()) {
                    $bulkChanges[$bulkChange->getName()] = $bulkChange->toArray();
                    Arr::forget($bulkChanges, $key);
                } else {
                    $bulkChanges[$key] = $bulkChange->toArray();
                }
            }
        }

        return apply_filters('base_filter_table_bulk_changes', $bulkChanges, $this);
    }

    public function saveBulkChanges(array $ids, string $inputKey, ?string $inputValue): bool
    {
        if (! in_array($inputKey, array_keys($this->getAllBulkChanges()))) {
            return false;
        }

        $request = request();

        foreach ($ids as $id) {
            $item = $this->getModel()->query()->findOrFail($id);

            /**
             * @var BaseModel $item
             */
            $item = $this->saveBulkChangeItem($item, $inputKey, $inputValue);

            event(new UpdatedContentEvent($this->getModel(), $request, $item));
        }

        return true;
    }

    public function onSavingBulkChangeItem(Closure $onSavingBulkChangeItemCallback): static
    {
        $this->onSavingBulkChangeItemCallback = $onSavingBulkChangeItemCallback;

        return $this;
    }

    public function saveBulkChangeItem(Model $item, string $inputKey, ?string $inputValue): Model|bool
    {
        if (isset($this->onSavingBulkChangeItemCallback)) {
            $result = call_user_func_array($this->onSavingBulkChangeItemCallback, [$item, $inputKey, $inputValue]);

            if ($result) {
                return $result;
            }
        }

        $item->{Auth::guard()->check() ? 'forceFill' : 'fill'}([$inputKey => $this->prepareBulkChangeValue($inputKey, $inputValue)]);

        $item->save();

        return $item;
    }

    public function prepareBulkChangeValue(string $key, ?string $value): string
    {
        if (strpos($key, '.') !== -1) {
            $key = Arr::last(explode('.', $key));
        }

        switch ($key) {
            case 'created_at':
            case 'updated_at':
                $value = BaseHelper::formatDateTime($value);

                break;
        }

        return (string) $value;
    }

    public function bulkChangeUrl(string $url): static
    {
        $this->bulkChangeUrl = $url;

        return $this;
    }

    public function getBulkChangeUrl(): string
    {
        return $this->bulkChangeUrl ?: route('table.bulk-change.save');
    }

    public function bulkChangeDataUrl(string $url): static
    {
        $this->bulkChangeDataUrl = $url;

        return $this;
    }

    public function getBulkChangeDataUrl(): string
    {
        return $this->bulkChangeDataUrl ?: route('table.bulk-change.data');
    }

    public function bulkActionDispatchUrl(string $url): static
    {
        $this->bulkActionDispatchUrl = $url;

        return $this;
    }

    public function getBulkActionDispatchUrl(): string
    {
        return $this->bulkActionDispatchUrl ?: route('table.bulk-action.dispatch');
    }

    public function removeAllBulkActions(): static
    {
        $this->bulkActions = [];

        return $this;
    }

    public function removeAllBulkChanges(): static
    {
        $this->bulkChanges = [];

        return $this;
    }
}
