@foreach($customFields as $customField)
    @if($loop->index % 2 == 0)
        <div class="row">
    @endif
        @if($customField->type == \Botble\Contact\Enums\CustomFieldType::TEXT)
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <input type="text" class="form-control" name="contact_custom_fields[{{ $customField->getKey() }}]" placeholder="{{ $customField->name }}">
                </div>
            </div>
        @elseif($customField->type == \Botble\Contact\Enums\CustomFieldType::NUMBER)
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <input type="number" class="form-control" name="contact_custom_fields[{{ $customField->getKey() }}]" placeholder="{{ $customField->name }}">
                </div>
            </div>
        @elseif($customField->type == \Botble\Contact\Enums\CustomFieldType::TEXTAREA)
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <textarea class="form-control" name="contact_custom_fields[{{ $customField->getKey() }}]" rows="5" placeholder="{{ $customField->name }}"></textarea>
                </div>
            </div>
        @elseif($customField->type == \Botble\Contact\Enums\CustomFieldType::DROPDOWN)
            @continue(! $customField->options->filter(fn ($option) => ! empty($option->label)))
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <select class="form-control form-select" name="contact_custom_fields[{{ $customField->getKey() }}]">
                        <option value="">{{ $customField->name }}</option>
                        @foreach($customField->options as $option)
                            <option value="{{ $option->label }}">{{ $option->label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @elseif($customField->type == \Botble\Contact\Enums\CustomFieldType::CHECKBOX)
            @continue(! $customField->options->filter(fn ($option) => ! empty($option->label)))
            <div class="col-md-12">
                <div class="form-group mb-3">
                    <strong class="font-sm-bold color-grey-900">{{ $customField->name }}</strong>
                    <div class="row mt-10 box-cb-form">
                        @foreach($customField->options as $option)
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group mb-3">
                                    @php($id = Str::kebab("{$customField->name}-{$loop->index}"))
                                    <input
                                        class="cd-form"
                                        type="checkbox"
                                        name="contact_custom_fields[{{ $customField->getKey() }}][]"
                                        value="{{ $option->value }}"
                                        id="cb-{{ $id }}"
                                    />
                                    <label for="cb-{{ $id }}">
                                        {{ $option->label }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @if($loop->index % 2 != 0)
        </div>
    @endif
@endforeach
