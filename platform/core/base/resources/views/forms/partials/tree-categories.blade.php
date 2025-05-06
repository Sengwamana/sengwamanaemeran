@php
    $updateTreeRoute ??= null;
@endphp

<div class="dd" data-depth="0" data-empty-text="{{ trans('core/base::tree-category.empty_text') }}">
    @include('core/base::forms.partials.tree-category', compact('updateTreeRoute'))
</div>
