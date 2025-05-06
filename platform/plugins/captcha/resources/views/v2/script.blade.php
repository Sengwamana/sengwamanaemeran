@if (! $isRendered || request()->ajax())
    <script
        src="{{ $url }}"
        async
        defer
    ></script>

    <script>
        'use strict';

        window.recaptchaInputs = window.recaptchaInputs || [];

        var refreshRecaptcha = function() {
            window.recaptchaInputs.forEach(function(item, index) {
                grecaptcha.reset(index);
            });
        };

        var onloadCallback = function() {
            window.recaptchaInputs.forEach(function(item) {
                if (document.getElementById(item)) {
                    grecaptcha.render(item);
                } else {
                    console.warn('reCAPTCHA placeholder element not found for ID:', item);
                }
            });
        };
    </script>
@endif

<script>
    window.recaptchaInputs.push('{{ $name }}');
</script>
