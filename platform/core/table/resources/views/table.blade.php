@extends($layout ?? BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @include('core/table::base-table')
@endsection
