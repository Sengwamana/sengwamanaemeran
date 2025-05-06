<section class="section-pricing-1">
    <div @class(['row row-cols-1 justify-content-center mt-8', 'row-cols-md-3' => $packages->count() >= 2 && Theme::getLayoutName() !== 'sidebar', 'row-cols-md-2' => $packages->count() >= 2 && Theme::getLayoutName() === 'sidebar'])>
        @foreach($packages as $package)
            <div class="col">
                <div class="card-pricing-1 p-6 rounded-4 h-100 d-flex flex-column">
                    <span class="text-uppercase fs-7">{{ $package->name }}</span> <br />
                    <h3 class="ds-3 fw-medium text-primary mb-5">{{ $package->price }}<span class="text-300 fs-4">/{{ $package->duration->label() }}</span></h3>
                    <ul class="ps-3 border-top border-600 pt-5 mb-auto">
                        @foreach($package->feature_list as $feature)
                            <li>
                                <p class="text-300">{{ $feature['value'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                    <a href="{{ $package->action_url }}" class="btn btn-gradient mt-5 w-100 justify-content-center">
                        {{ $package->action_label }}
                        <i class="ri-arrow-right-up-line"></i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</section>
