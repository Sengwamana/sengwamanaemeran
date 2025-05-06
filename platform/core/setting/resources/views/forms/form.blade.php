@extends($layout ?? BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
   @include('core/setting::forms.form-content-only')
@stop
