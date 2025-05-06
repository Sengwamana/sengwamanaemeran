<div class="page-header d-print-none">
    <div class="{{ AdminAppearance::getContainerWidth() }}">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ Breadcrumb::default() }}
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    @stack('header-action')
                </div>
            </div>
        </div>
    </div>
</div>
