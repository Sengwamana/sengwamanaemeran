<x-core::form.color-selector
    :name="$name"
    :choices="$options['choices']"
    :label="$options['label']"
    :selected="$options['selected']"
    :required="$options['required']"
    :wrapper-class="$options['wrapper'] ? $options['wrapper']['class'] : null"
    :helper-text="$options['help_block'] ? $options['help_block']['text'] : null"
></x-core::form.color-selector>
