@extends(BaseHelper::getAdminMasterLayoutTemplate())

@push('header')
    {!! RvMedia::renderHeader() !!}
@endpush

@section('content')
    {!! RvMedia::renderContent() !!}
@endsection

@push('footer')
    {!! RvMedia::renderFooter() !!}
@endpush
