@if($footerSidebar = dynamic_sidebar('footer_sidebar'))
    <footer>
        <div class="section-footer position-relative pt-60 pb-60 bg-secondary-1">
            <div class="text-center container position-relative z-1">
                {!! $footerSidebar !!}
            </div>
            @if ($footerBackground = theme_option('footer_background'))
                <div class="position-absolute top-0 start-0 w-100 h-100 z-0" data-background="{{ RvMedia::getImageUrl($footerBackground) }}"></div>
            @endif
        </div>
    </footer>
@endif

<div class="btn-scroll-top style-btn-2">
    <svg class="progress-square svg-content" width="100%" height="100%" viewBox="0 0 40 40">
        <path d="M8 1H32C35.866 1 39 4.13401 39 8V32C39 35.866 35.866 39 32 39H8C4.13401 39 1 35.866 1 32V8C1 4.13401 4.13401 1 8 1Z" />
    </svg>
</div>
