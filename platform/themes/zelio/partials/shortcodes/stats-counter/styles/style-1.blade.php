<div class="section-static-1 position-relative overflow-hidden z-0 py-8 bg-900">
    <div class="container">
        <div class="inner">
            <div class="row align-items-center justify-content-between">
                @foreach($tabs as $tab)
                    <div class="col-lg-auto col-md-6">
                        <div class="counter-item-cover counter-item">
                            <div class="content text-center mx-auto d-flex align-items-center">
                                <span class="ds-3 count text-primary fw-medium my-0 counter-wrapper"><span class="plus-icon-counter">+</span><span class="odometer ds-1 text-dark fw-semibold number-counter">{{ $tab['count'] }}</span></span>
                                <div class="text-start ms-2">
                                    @if($tab['label'])
                                        <p class="fs-5 mb-0 text-300">{{ $tab['label'] }}</p>
                                    @endif
                                    @if($tab['highlight_text'])
                                        <p class="fs-5 mb-0 fw-bold">{{ $tab['highlight_text'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
