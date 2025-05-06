@if($socials = Theme::getSocialLinks())
    @if ($sidebar === 'sidebar_panel_sidebar')
        <div class="contact-list">
            @if ($config['name'])
                <p class="text-400 fs-5 mb-2">{{ $config['name'] }}</p>
            @endif
            <div class="d-md-flex d-none gap-3">
                @foreach($socials as $social)
                    <a href="{{ $social->getUrl() }}" {{ $social->getAttributes() }}>
                        <x-core::icon :name="$social->getIcon()" />
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="navbar-social d-flex justify-content-center gap-3 my-2">
            @foreach($socials as $social)
                <a href="{{ $social->getUrl() }}" {{ $social->getAttributes() }}>
                    <x-core::icon :name="$social->getIcon()" />
                </a>
            @endforeach
        </div>
    @endif
@endif
