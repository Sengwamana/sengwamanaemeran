<x-core::alert
    type="info"
    class="resume-setup-wizard-wrapper"
>
    {!! BaseHelper::clean(
        trans('packages/get-started::get-started.setup_wizard_button', [
            'link' => Html::link(
                '#',
                trans('packages/get-started::get-started.click_here'),
                ['class' => 'resume-setup-wizard'],
                null,
                false,
            )->toHtml(),
        ]),
    ) !!}
</x-core::alert>
