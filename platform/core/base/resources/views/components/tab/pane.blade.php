@props(['id', 'isActive' => false])

<div {{ $attributes->merge(['class' => 'tab-pane' . ($isActive ? ' active show' : ''), 'id' => $id]) }}>
    {{ $slot }}
</div>
