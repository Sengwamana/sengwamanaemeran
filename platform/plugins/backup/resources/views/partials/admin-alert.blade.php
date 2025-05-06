<x-core::alert
    type="info"
    icon="ti ti-alert-circle"
>
    {!! BaseHelper::clean(trans('plugins/backup::backup.demo_alert', ['link' => route('backups.index')])) !!}
</x-core::alert>
