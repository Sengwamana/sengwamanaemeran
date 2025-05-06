<ul {{ $attributes->merge(['data-bs-toggle' => 'tabs', 'class' => 'nav nav-tabs']) }}>
    {{ $slot }}
</ul>
