@props([
    'variables' => [],
    'functions' => [],
    'value' => null,
    'name' => null,
    'mode' => 'html',
    'helperText' => null,
])

<div class="twig-template">
    <div class="mb-3 btn-list">
        @if (! empty($variables))
            <x-core::dropdown
                :label="__('Variables')"
                icon="ti ti-code"
            >
                @foreach ($variables as $key => $label)
                    <x-core::dropdown.item
                        data-bb-toggle="twig-variable"
                        :data-key="$key"
                    >
                        <span class="text-danger">{{ $key }}</span>: {{ trans($label) }}
                    </x-core::dropdown.item>
                @endforeach
            </x-core::dropdown>
        @endif

        @if (! empty($functions))
            <x-core::dropdown
                :label="__('Functions')"
                icon="ti ti-code"
            >
                @foreach ($functions as $key => $function)
                    <x-core::dropdown.item
                        data-bb-toggle="twig-function"
                        :data-key="$key"
                        :data-sample="$function['sample']"
                    >
                        <span class="text-danger">{{ $key }}</span>: {{ trans($function['label']) }}
                    </x-core::dropdown.item>
                @endforeach
            </x-core::dropdown>
        @endif
    </div>

    <x-core::form-group>
        <x-core::form.code-editor
            :name="$name"
            :value="$value"
            :mode="$mode"
        >
            <x-slot:helper-text>
                @if($helperText)
                    {{ $helperText }}
                @else
                    {!! BaseHelper::clean(
                        __('Learn more about Twig template: :url', [
                            'url' => Html::link('https://twig.symfony.com/doc/3.x/', null, ['target' => '_blank']),
                        ]),
                    ) !!}
                @endif
            </x-slot:helper-text>
        </x-core::form.code-editor>
    </x-core::form-group>
</div>
