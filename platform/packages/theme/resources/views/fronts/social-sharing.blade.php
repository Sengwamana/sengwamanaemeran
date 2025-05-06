<style>
    .bb-social-sharing {
        display: inline-flex;
        gap: 0.25rem;
        margin-bottom: 0;
    }
    .bb-social-sharing .bb-social-sharing__item {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 38px;
        height: 38px;
        line-height: 36px;
        text-align: center;
        border: 1px solid #e6e7e8;
        border-radius: 50%;
    }
    .bb-social-sharing .bb-social-sharing__item a {
        line-height: 16px;
        color: var(--primary-color);
    }
    .bb-social-sharing .bb-social-sharing__item:last-child {
        margin-inline-end: 0;
    }
    .bb-social-sharing .bb-social-sharing__item:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: #fff;
    }
    .bb-social-sharing .bb-social-sharing__item:hover a, .bb-social-sharing .bb-social-sharing__item:hover button {
        color: #fff;
    }
    .bb-social-sharing .bb-social-sharing__item button {
        border: none;
        outline: none;
        background: transparent;
        color: var(--primary-color);
    }
    .bb-social-sharing .bb-social-sharing__item button:hover {
        cursor: pointer;
    }
    .bb-social-sharing .bb-social-sharing__item svg {
        width: 1.25rem;
        height: 1.25rem;
    }

    .bb-social-sharing .bb-social-sharing-text {
        display: none;
    }
</style>

<ul class="bb-social-sharing">
    @foreach($socials as $social)
        <li class="bb-social-sharing__item">
            <a
                href="{{ $social['url'] }}"
                target="_blank"
                title="{{ __('Share on :social', ['social' => $social['name']]) }}"
                @style(["background-color: {$social['background_color']}" => $social['background_color'], "color: {$social['color']}" => $social['color']])
            >
                {!! $social['icon'] !!}

                <span class="bb-social-sharing-text">{{ $social['name'] }}</span>
            </a>
        </li>
    @endforeach

    <li class="bb-social-sharing__item">
        <button title="{{ __('Copy link') }}" data-bb-toggle="social-sharing-clipboard" data-clipboard-text="{{ $url }}">
            <x-core::icon name="ti ti-copy" data-clipboard-icon="copy" />
            <x-core::icon name="ti ti-check" data-clipboard-icon="copied" style="display: none;" />
            <span class="bb-social-sharing-text">{{ __('Copy link') }}</span>
        </button>
    </li>
</ul>

@once
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            function toggleClipboardActionIcon(element) {
                const copiedState = element.querySelector('[data-clipboard-icon="copy"]');
                const copyState = element.querySelector('[data-clipboard-icon="copied"]');

                copiedState.style.display = 'none';
                copyState.style.display = 'inline-block';

                setTimeout(function () {
                    copiedState.style.display = 'inline-block';
                    copyState.style.display = 'none';
                }, 3000);
            }

            document.querySelectorAll('[data-bb-toggle="social-sharing-clipboard"]').forEach(function (element) {
                element.addEventListener('click', function (event) {
                    event.preventDefault();

                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(element.dataset.clipboardText).then(function () {
                            toggleClipboardActionIcon(element);
                        });
                    } else {
                        const input = document.createElement('input');
                        input.value = element.dataset.clipboardText;
                        document.body.appendChild(input);
                        input.select();
                        document.execCommand('copy');
                        document.body.removeChild(input);

                        toggleClipboardActionIcon(element);
                    }
                });
            });
        });
    </script>
@endonce
