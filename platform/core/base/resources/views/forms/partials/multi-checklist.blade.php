@if (! $asDropdown && $choices)
    <x-core::form.fieldset class="fieldset-for-multi-check-list">
        <div class="multi-check-list-wrapper">
            @php
                if (isset($attributes['class'])) {
                    $attributes['class'] = str_replace('form-control', '', $attributes['class']);
                }

                if (is_string($value) && str_contains($value, ',')) {
                    $value = explode(',', $value);
                }
            @endphp

            @foreach ($choices as $key => $item)
                @php
                    $checked = is_array($value) ? in_array($key, $value) : ($key == $value);
                @endphp
                <x-core::form.checkbox
                    :id="sprintf('%s-item-%s', Str::slug($name), $key)"
                    :name="$name"
                    :value="$key"
                    :label="$item"
                    :checked="$checked"
                    :inline="$inline"
                    :attributes="new Illuminate\View\ComponentAttributeBag((array) $attributes)"
                />
            @endforeach
        </div>
    </x-core::form.fieldset>
@else
    @php
        $countSelected = count($value);
        $placeholder = Arr::get($attributes, 'placeholder') ?: trans('core/base::forms.select_placeholder');

        if ($countSelected && $countSelected > 3) {
            $placeholder = trans('core/base::forms.count_selected', ['count' => $countSelected]);
        }
    @endphp

    <div
        class="position-relative"
        data-bb-toggle="dropdown-checkboxes"
        @if($ajaxUrl)
            data-ajax-url="{{ $ajaxUrl }}"
        @endif
        data-name="{{ $name }}"
        data-selected-text="{{ trans('core/base::forms.selected') }}"
        data-placeholder="{{ $placeholder }}"
    >
        <span class="form-select text-truncate">{{ $placeholder }}</span>

        @if($ajaxUrl)
            <div class="d-none multi-checklist-selected">
                @foreach($value as $item)
                    <input type="hidden" name="{{ $name }}" value="{{ $item }}">
                @endforeach
            </div>
        @endif

        <input type="text" class="form-select" placeholder="{{ trans('core/table::table.search') }}" style="display: none">

        <div class="dropdown-menu dropdown-menu-end w-100">
            <div data-bb-toggle="tree-checkboxes">
                <ul class="list-unstyled p-3 pb-0">
                    @if(! $ajaxUrl && ! empty($choices))
                        @foreach ($choices as $key => $item)
                            <li>
                                <x-core::form.checkbox
                                    :id="sprintf('%s-item-%s', Str::slug($name), $key)"
                                    :name="$name"
                                    :value="$key"
                                    :label="$item"
                                    :checked="in_array($key, $value)"
                                    :inline="$inline"
                                    :attributes="new Illuminate\View\ComponentAttributeBag((array) $attributes)"
                                />
                            </li>
                        @endforeach
                    @else
                        <div class="py-5"></div>
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endif
