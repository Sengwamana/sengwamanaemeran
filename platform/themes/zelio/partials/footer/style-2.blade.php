@if($footerSidebar = dynamic_sidebar('footer_sidebar'))
    <footer>
        <div class="section-footer-2 position-relative">
            <div class="container position-relative z-1 border-top border-1 pb-2 pt-4 text-center">
                {!! $footerSidebar !!}
            </div>
        </div>
    </footer>
@endif

<div class="btn-scroll-top style-btn-2">
    <svg class="progress-square svg-content" width="100%" height="100%" viewBox="0 0 40 40">
        <path d="M8 1H32C35.866 1 39 4.13401 39 8V32C39 35.866 35.866 39 32 39H8C4.13401 39 1 35.866 1 32V8C1 4.13401 4.13401 1 8 1Z" />
    </svg>
</div>
