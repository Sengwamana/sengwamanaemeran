<div {{ $attributes->merge(['class' => 'datagrid-item']) }}>
    <div class="datagrid-title">{{ $title }}</div>
    <div class="datagrid-content">{{ $slot }}</div>
</div>
