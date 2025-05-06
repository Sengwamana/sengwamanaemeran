<div id="contact" class="contact">
    @if($shortcode->title)
        <h3>{!! BaseHelper::clean($shortcode->title) !!}</h3>
    @endif
    <div class="d-sm-flex align-items-center gap-5 mt-4">
        @foreach(collect($contactInfo)->split(2) as $row)
            <div class="d-sm-flex flex-column gap-2 position-relative z-1">
                @foreach($row as $item)
                    <a class="d-block mb-3 mb-sm-0 d-sm-inline-block" href="{{ $item['url'] }}">
                        @if ($item['icon'])
                            <x-core::icon :name="$item['icon']" class="text-primary-3 h6 fw-medium" />
                        @endif

                        @if ($item['content'])
                            <span class="text-300 fs-6 ms-2">{{ $item['content'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endforeach
    </div>
    <div class="position-relative z-2 mt-4">
        @if($shortcode->subtitle)
            <h5 class="text-dark mb-3">{!! BaseHelper::clean($shortcode->subtitle) !!}</h5>
        @endif
        {!!
            /** @var \Botble\Contact\Forms\Fronts\ContactForm $form **/
            $form
                ->setFormInputClass('form-control bg-3 border border-secondary-3 rounded-3')
                ->setFormLabelClass('mb-1 mt-3 text-dark')
                ->remove('submit')
                ->add(
                    'submit',
                    'submit',
                    \Botble\Base\Forms\FieldOptions\ButtonFieldOption::make()
                        ->cssClass('btn btn-secondary-3 fw-medium mt-2')
                        ->label(__('Send Message') . '<i class="ri-arrow-right-up-line fw-medium"></i>')
                )
                ->renderForm()
        !!}
    </div>
</div>
