@php
    /** @var \Botble\Table\Abstracts\TableAbstract $table */
    /** @var \Botble\Table\Abstracts\TableActionAbstract[] $actions */
    /** @var \Illuminate\Database\Eloquent\Model $model */
@endphp

<div class="table-actions">
    @if(! $table->hasDisplayActionsAsDropdown())
        @foreach ($actions as $action)
            {{ $action->setItem($model) }}
        @endforeach
    @else
        <div class="dropdown">
            <button class="btn dropdown-toggle" type="button" id="{{ $id = sprintf('dropdown-actions-%s-%s', md5($model::class), $model->getKey()) }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ trans('core/base::tables.action') }}
            </button>
            <div class="dropdown-menu" aria-labelledby="{{ $id }}">
                @foreach ($actions as $action)
                    {{ $action->setItem($model)->displayAsDropdownItem() }}
                @endforeach
            </div>
        </div>
    @endif
</div>
