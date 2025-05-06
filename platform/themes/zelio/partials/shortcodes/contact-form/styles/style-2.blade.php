<section id="contact" class="section-contact-2 position-relative pb-60 overflow-hidden">
    <div class="container position-relative z-1">
        <div class="row align-items-center">
            <div @class(['pb-5 pb-lg-0', 'col-lg-7' => $contactInfo, 'col-lg-12' => ! $contactInfo])>
                <div class="position-relative">
                    <div class="position-relative z-2">
                        @if($shortcode->title)
                            <h3 class="text-primary-2 mb-3">{!! BaseHelper::clean($shortcode->title) !!}</h3>
                        @endif
                        @if($shortcode->subtitle)
                            <span class="fs-5 fw-medium text-200">
                                {!! BaseHelper::clean(nl2br($shortcode->subtitle)) !!}
                            </span>
                        @endif
                        {!!
                            /** @var \Botble\Contact\Forms\Fronts\ContactForm $form **/
                            $form
                                ->setFormInputClass('form-control bg-3 border border-1 rounded-3')
                                ->setFormLabelClass('mb-1 mt-3 text-dark')
                                ->remove('submit')
                                ->add(
                                    'submit',
                                    'submit',
                                    \Botble\Base\Forms\FieldOptions\ButtonFieldOption::make()
                                        ->cssClass('btn btn-primary-2 rounded-2 mt-3')
                                        ->label(__('Send Message') . '<i class="ri-arrow-right-up-line"></i>')
                                )
                                ->renderForm()
                        !!}
                    </div>
                    <div class="z-0 bg-primary-dark rectangle-bg z-1 rounded-3"></div>
                </div>
            </div>
            @if($contactInfo)
                <div class="col-lg-5 d-flex flex-column ps-lg-8">
                    @foreach($contactInfo as $item)
                        <div class="d-flex align-items-center mb-3 position-relative d-inline-flex">
                            @if ($item['icon'])
                                <div class="d-inline-block">
                                    <div class="icon-flip flex-nowrap icon-shape icon-xxl border border-1 rounded-3 bg-3">
                                        <x-core::icon :name="$item['icon']" class="text-primary-2" />
                                    </div>
                                </div>
                            @endif
                            <div class="ps-3 h-100">
                                @if ($item['label'])
                                    <span class="text-400 fs-6">{{ $item['label'] }}</span>
                                @endif
                                @if ($item['content'])
                                    <h6 class="mb-0">{{ $item['content'] }}</h6>
                                @endif
                            </div>
                            @if ($item['url'])
                                <a href="{{ $item['url'] }}" class="position-absolute top-0 start-0 w-100 h-100" title="{{ $item['label'] }}"></a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
