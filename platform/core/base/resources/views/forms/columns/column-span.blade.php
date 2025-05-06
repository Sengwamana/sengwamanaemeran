@if(($columns = $field->getParent()->getFormOption('columns')) && $hasColspan =  $field->getOption('colspan'))
    <div
        @class([
            "col-{$field->getColumnSpan('default')}" => $field->getColumnSpan('default'),
            "col-sm-{$field->getColumnSpan('sm')}" => $field->getColumnSpan('sm'),
            "col-md-{$field->getColumnSpan('md')}" => $field->getColumnSpan('md'),
            "col-lg-{$field->getColumnSpan('lg')}" => $field->getColumnSpan('lg'),
            "col-xl-{$field->getColumnSpan('xl')}" => $field->getColumnSpan('xl'),
            "col-xxl-{$field->getColumnSpan('xxl')}" => $field->getColumnSpan('xxl'),
        ])
    >
        @endif
        {!! $html !!}
        @if($columns && $hasColspan)
    </div>
@endif
