<section id="contact" class="section-contact-1 bg-900 position-relative pt-150 pb-lg-250 pb-150 overflow-hidden">
    <div class="container position-relative z-1">
        @if($shortcode->title)
            <h3 class="ds-3 mt-3 mb-3 text-primary">{!! BaseHelper::clean($shortcode->title) !!}</h3>
        @endif
        @if($shortcode->subtitle)
            <span class="fs-5 fw-medium text-200">
                {!! BaseHelper::clean(nl2br($shortcode->subtitle)) !!}
            </span>
        @endif
        @if($contactInfo)
            <div class="row mt-8">
                <div class="col-lg-4 d-flex flex-column">
                    @foreach($contactInfo as $item)
                        <div class="d-flex align-items-center mb-4 position-relative d-inline-flex">
                            @if ($item['icon'])
                                <div class="bg-white icon-flip position-relative icon-shape icon-xxl border-linear-2 border-2 rounded-4">
                                    <x-core::icon :name="$item['icon']" class="text-primary" />
                                </div>
                            @endif
                            <div class="ps-3">
                                @if ($item['label'])
                                    <span class="text-400 fs-5">{{ $item['label'] }}</span>
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
                <div @class(['ps-lg-0 pt-5 pt-lg-0', 'col-lg-7 offset-lg-1' => $contactInfo])>
                    <div class="position-relative">
                        <div class="position-relative z-2">
                            <h3>{{ __('Leave a message') }}</h3>
                            {!!
                                /** @var \Botble\Contact\Forms\Fronts\ContactForm $form **/
                                $form
                                    ->setFormInputClass('form-control border rounded-3')
                                    ->setFormLabelClass('mb-1 mt-3 text-dark')
                                    ->remove('submit')
                                    ->add(
                                        'submit',
                                        'submit',
                                        \Botble\Base\Forms\FieldOptions\ButtonFieldOption::make()
                                            ->cssClass('btn btn-gradient mt-3')
                                            ->label(__('Send Message') . '<i class="ri-arrow-right-up-line"></i>')
                                    )
                                    ->renderForm()
                            !!}
                        </div>
                        <div class="z-0 bg-primary-dark rectangle-bg z-1 rounded-3"></div>
                    </div>
                </div>
                @if($contactInfo)
            </div>
        @endif
    </div>
    @if ($siteName = theme_option('site_name'))
        <div class="scroll-move-right position-absolute bottom-0 start-50 translate-middle-x bg-900 overflow-hidden" style="translate: none; rotate: none; scale: none; transform: translate(-925.328px, 0px);">
            <div class="wow img-custom-anim-top" style="visibility: visible; animation-name: img-anim-top;">
                <h3 class="stroke fs-280 text-lowercase text-900 mb-0 lh-1">{{ $siteName }}</h3>
            </div>
        </div>
    @endif
</section>
