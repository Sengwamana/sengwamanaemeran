<div
    @class([
        'row',
        "row-cols-{$form->getColumns('default')}" => $form->getColumns('default'),
        "row-cols-sm-{$form->getColumns('sm')}" => $form->getColumns('sm'),
        "row-cols-md-{$form->getColumns('md')}" => $form->getColumns('md'),
        "row-cols-lg-{$form->getColumns('lg')}" => $form->getColumns('lg'),
        "row-cols-xl-{$form->getColumns('xl')}" => $form->getColumns('xl'),
        "row-cols-xxl-{$form->getColumns('xxl')}" => $form->getColumns('xxl'),
    ])
>
