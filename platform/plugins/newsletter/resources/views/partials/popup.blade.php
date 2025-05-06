@php
    $title = theme_option('newsletter_popup_title');
    $image = theme_option('newsletter_popup_image');
@endphp

<link rel="stylesheet" href="{{ asset('vendor/core/plugins/newsletter/css/newsletter.css') }}?v=1.2.6">

<div @class(['modal-dialog', 'modal-lg' => $image])>
    <div @class(['modal-content border-0', 'd-flex flex-md-col flex-lg-row' => $image])>
        @if ($image)
            <div class="d-none d-md-block col-6 newsletter-popup-bg">
                {!! RvMedia::image($image, $title, attributes: ['loading' => 'eager']) !!}
            </div>
        @endif

        <button type="button" class="btn-close position-absolute" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>

        <div class="newsletter-popup-content">
            <div class="modal-header flex-column align-items-start border-0 p-0">
                @if ($subtitle = theme_option('newsletter_popup_subtitle'))
                    <span class="modal-subtitle">{!! BaseHelper::clean($subtitle) !!}</span>
                @endif

                @if ($title)
                    <h5 class="modal-title fs-2" id="newsletterPopupModalLabel">{!! BaseHelper::clean($title) !!}</h5>
                @endif

                @if ($description = theme_option('newsletter_popup_description'))
                    <p class="modal-text text-muted">{!! BaseHelper::clean($description) !!}</p>
                @endif
            </div>
            <div class="modal-body p-0">
                {!! $newsletterForm->setFormOption('class', 'bb-newsletter-popup-form')->renderForm() !!}
            </div>
        </div>
    </div>
</div>
