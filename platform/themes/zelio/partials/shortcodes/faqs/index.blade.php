<section class="section-pricing-1">
    <div class="container">
        <div class="row mt-8">
            <div @class(['mx-md-auto text-center', 'col-lg-6' => Theme::getLayoutName() !== 'sidebar', 'col-lg-10' => Theme::getLayoutName() === 'sidebar'])>
                @if($shortcode->title)
                    <h2 class="text-300 mb-8">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                @endif
                <div class="accordion">
                    @php
                        $keyId = 'faq-' . uniqid();
                    @endphp

                    @foreach($faqs as $faq)
                        <div class="mb-3 card border-2 rounded-4">
                            <div class="card-header p-0 border-0">
                                <a class="p-3 collapsed text-900 fw-bold d-flex align-items-center" data-bs-toggle="collapse" href="#{{ $keyId }}-{{ $loop->iteration }}">
                                    <p class="fs-5 mb-0 text-dark">{!! BaseHelper::clean($faq->question) !!}</p>
                                    <span class="ms-auto arrow me-2 icon-shape">
                                    <i class="ri-add-line"></i>
                                </span>
                                </a>
                            </div>
                            <div id="{{ $keyId }}-{{ $loop->iteration }}" class="collapse" data-bs-parent=".accordion">
                                <p class="px-4 pt-0 text-start card-body">
                                    {!! BaseHelper::clean($faq->answer) !!}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
