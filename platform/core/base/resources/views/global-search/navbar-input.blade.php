<x-core::form.text-input
    name="keyword"
    :label="$name = trans('core/base::base.global_search.search')"
    :label-sr-only="true"
    :placeholder="$name"
    :input-group="true"
    :group-flat="true"
    tabindex="0"
    id="global-search-input"
    wrapper-class-default=""
    data-bb-toggle="gs-navbar-input"
    autocomplete="off"
>
    <x-slot:append>
        <div class="input-group-text">
            <kbd>ctrl/cmd + k</kbd>
        </div>
    </x-slot:append>
</x-core::form.text-input>
