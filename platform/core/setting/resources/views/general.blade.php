@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @include('core/setting::partials.license')

    {!! $form->renderForm() !!}
@stop
