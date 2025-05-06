@php
    $isDefaultLanguage = !defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME') || !request()->input('ref_lang') || request()->input('ref_lang') == Language::getDefaultLocaleCode();
@endphp

<div
    class="col-md-12"
    id="custom-field-options"
>
    <table class="table table-bordered setting-option">
        <thead>
            <tr>
                @if($isDefaultLanguage)
                    <th scope="col">#</th>
                @endif
                <th scope="col">{{ trans('plugins/portfolio::portfolio.custom_field.option.label') }}</th>
                <th
                    scope="col"
                    colspan="2"
                >{{ trans('plugins/portfolio::portfolio.custom_field.option.value') }}</th>
            </tr>
        </thead>
        <tbody class="option-sortable">
            @if ($options->count())
                @foreach ($options as $key => $value)
                    <tr
                        class="option-row ui-state-default"
                        data-index="{{ $value->id }}"
                    >
                        <input
                            name="options[{{ $key }}][id]"
                            type="hidden"
                            value="{{ $value->id }}"
                        >
                        <input
                            name="options[{{ $key }}][order]"
                            type="hidden"
                            class="option-order"
                            value="{{ $value->order !== 999 ? $value->order : $key }}"
                        >
                        @if($isDefaultLanguage)
                            <td class="text-center">
                                <i class="fa fa-sort"></i>
                            </td>
                        @endif
                        <td>
                            <input
                                class="form-control option-label"
                                name="options[{{ $key }}][label]"
                                type="text"
                                value="{{ $value->label }}"
                                placeholder="{{ trans('plugins/portfolio::portfolio.custom_field.option.label') }}"
                            />
                        </td>
                        <td>
                            <input
                                class="form-control option-value"
                                name="options[{{ $key }}][value]"
                                type="text"
                                value="{{ $value->value }}"
                                placeholder="{{ trans('plugins/portfolio::portfolio.custom_field.option.value') }}"
                            />
                        </td>
                        @if($isDefaultLanguage)
                            <td style="width: 50px">
                                <button
                                    class="btn btn-default remove-row"
                                    data-index="0"
                                    type="button"
                                >
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @else
                <tr
                    class="option-row"
                    data-index="0"
                >
                    @if($isDefaultLanguage)
                        <td class="text-center">
                            <i class="fa fa-sort"></i>
                        </td>
                    @endif
                    <td>
                        <input
                            class="form-control option-label"
                            name="options[0][label]"
                            type="text"
                            placeholder="{{ trans('plugins/portfolio::portfolio.custom_field.option.label') }}"
                        />
                    </td>
                    <td>
                        <input
                            class="form-control option-value"
                            name="options[0][value]"
                            type="text"
                            placeholder="{{ trans('plugins/portfolio::portfolio.custom_field.option.value') }}"
                        />
                    </td>
                    @if($isDefaultLanguage)
                        <td style="width: 50px">
                            <button
                                class="btn btn-default remove-row"
                                data-index="0"
                                type="button"
                            >
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    @endif
                </tr>
            @endif
        </tbody>
    </table>

    @if($isDefaultLanguage)
        <button
            class="btn btn-info mt-3"
            id="add-new-row"
            type="button"
        >{{ trans('plugins/portfolio::portfolio.custom_field.option.add_row') }}</button>
    @endif
</div>
