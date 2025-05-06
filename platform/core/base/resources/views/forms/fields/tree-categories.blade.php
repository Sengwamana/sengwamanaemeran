@if (
    isset($options['choices'])
    && (is_array($options['choices']) || $options['choices'] instanceof \Illuminate\Support\Collection)
)
    @if(count($options['choices']) < 50)
        <div class="mb-3">
            <div class="input-icon">
                <input
                    type="text"
                    id="search-category-input-{{ $inputSearchId = mt_rand() }}"
                    class="form-control"
                    placeholder="{{ trans('core/base::forms.search_input_placeholder') }}"
                    onkeyup="filter_categories_{{ $inputSearchId }}({{ $inputSearchId }})"
                    formnovalidate
                />
                <span class="input-icon-addon">
                  <x-core::icon name="ti ti-search" />
                </span>
            </div>
        </div>

        <div data-bb-toggle="tree-checkboxes" class="tree-categories-list-{{ $inputSearchId }}">
            @include('core/base::forms.partials.tree-categories-checkbox-options', [
                'categories' => $options['choices'],
                'selected' => $options['selected'],
                'currentId' => null,
                'name' => $name,
            ])
        </div>

        <script>
            function filter_categories_{{ $inputSearchId }}(inputSearchId) {
                const searchInput = document.getElementById('search-category-input-' + inputSearchId).value.toLowerCase();
                const categories = document.querySelectorAll('.tree-categories-list-' + inputSearchId + ' label');

                categories.forEach(category => {
                    const text = category.textContent.toLowerCase();
                    category.style.display = text.includes(searchInput) ? '' : 'none';
                });
            }
        </script>
    @else
        <x-core::form.select
            :multiple="true"
            :name="$name"
            data-bb-toggle="tree-categories-select"
            :data-placeholder="trans('core/base::forms.select_placeholder')"
        >
            @include('core/base::forms.partials.tree-categories-select-options', [
                'categories' => $options['choices'],
                'selected' => $options['selected'],
                'currentId' => null,
                'name' => $name,
                'indent' => null,
            ])
        </x-core::form.select>
    @endif
@endif
