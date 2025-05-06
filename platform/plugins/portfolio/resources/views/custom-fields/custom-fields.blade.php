@php
    $isDefaultLanguage = !defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME') || !request()->input('ref_lang') || request()->input('ref_lang') == Language::getDefaultLocaleCode();
@endphp
<div class="job-custom-fields-wrap">
    <div id="custom-field-list"></div>

    @if ($isDefaultLanguage)
        <div class="d-flex justify-content-between">
            <button
                class="btn btn-info"
                data-bs-toggle="modal"
                data-bs-target="#addNewCustomFieldModal"
                type="button"
            >{{ trans('plugins/portfolio::portfolio.custom_field.add_a_new_custom_field') }}</button>
        </div>
    @endif

    <div
        class="modal fade"
        id="addNewCustomFieldModal"
        tabindex="-1"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h4 class="modal-title">
                        <i class="til_img"></i>
                        <strong>{{ trans('plugins/portfolio::portfolio.custom_field.modal.heading') }}</strong>
                    </h4>
                    <button
                        class="btn-close"
                        data-bs-dismiss="modal"
                        type="button"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div>
                        <label
                            for="custom-field-id">{{ trans('plugins/portfolio::portfolio.custom_field.modal.select_field') }}</label>
                        <div class="ui-select-wrapper">
                            <select
                                class="form-control ui-select"
                                id="custom-field-id"
                            >
                                <option value="">
                                    {{ trans('plugins/portfolio::portfolio.custom_field.add_a_new_custom_field') }}
                                </option>
                                @foreach ($customFields as $customField)
                                    <option value="{{ $customField->id }}">{{ $customField->name }}
                                        ({{ is_string($customField->type) ? $customField->type : $customField->type->label() }})
                                    </option>
                                @endforeach
                            </select>
                            <svg class="svg-next-icon svg-next-icon-size-16">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M10 16l-4-4h8l-4 4zm0-12L6 8h8l-4-4z"></path>
                                </svg>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                        type="button"
                    >{{ trans('core/base::forms.cancel') }}</button>
                    <button
                        class="btn btn-primary"
                        id="add-new"
                        type="button"
                    >{{ trans('plugins/portfolio::portfolio.custom_field.modal.button') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('footer')
    <script id="custom-field-dropdown-template" type="text/x-custom-template">
        <div class="mb-3 position-relative custom-field-item" data-index="__index__">
            <div class="row">
                <input type="hidden" name="custom_fields[__index__][id]" value="__id__">
                <input type="hidden" name="custom_fields[__index__][custom_field_id]" value="__custom_field_id__">
                <div class="col">
                    <label class="form-label">{{ trans('core/base::forms.name') }}</label>
                    <input type="text" name="custom_fields[__index__][name]" class="form-control custom-field-name __custom_field_input_class__" value="__name__" placeholder="{{ trans('core/base::forms.name') }}">
                </div>
                <div class="col">
                    <label class="form-label">{{ trans('core/base::forms.value') }}</label>
                    <select name="custom_fields[__index__][value]" class="form-control custom-field-value" id="custom-field-options">__selectOptions__</select>
                </div>
                @if ($isDefaultLanguage)
                    <div>
                        <button type="button" data-index="__index__" role="button" class="position-absolute top-0 right-0 remove-custom-field btn btn-default">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </script>

    <script id="custom-field-template" type="text/x-custom-template">
        <div class="mb-3 position-relative custom-field-item" data-index="__index__">
            <div class="row">
                <input type="hidden" name="custom_fields[__index__][id]" value="__id__">
                <input type="hidden" name="custom_fields[__index__][custom_field_id]" value="__custom_field_id__">
                <div class="col">
                    <label class="form-label">{{ trans('core/base::forms.name') }}</label>
                    <input type="text" name="custom_fields[__index__][name]" class="form-control custom-field-name __custom_field_input_class__" value="__name__" placeholder="{{ trans('core/base::forms.name') }}">
                </div>
                <div class="col">
                    <label class="form-label">{{ trans('core/base::forms.value') }}</label>
                    <input type="text" name="custom_fields[__index__][value]" class="form-control custom-field-value" value="__value__" placeholder="{{ trans('core/base::forms.value') }}">
                </div>
                @if ($isDefaultLanguage)
                    <div>
                        <button type="button" data-index="__index__" role="button" class="position-absolute top-0 right-0 remove-custom-field btn btn-default">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </script>

    <style>
        .custom-field-item {
            border: 1px dashed;
            padding: 10px;
        }

        .custom-field-item .remove-custom-field {
            right: 0;
        }
    </style>
    <script>
        window.jobCustomFields = @json([
            'ajax' => $ajax,
            'customFields' => $model->custom_fields_array,
        ]);
    </script>
@endpush
