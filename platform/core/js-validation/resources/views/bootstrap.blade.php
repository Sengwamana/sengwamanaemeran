<script>
    jQuery(document).ready(function() {
        'use strict';
        $("{{ $validator['selector'] }}").each(function() {
            $(this).validate({
                errorElement: 'div',
                errorClass: 'invalid-feedback',

                errorPlacement: function(error, element) {
                    if (element.closest('[data-bb-toggle="tree-checkboxes"]').length) {
                        error.insertAfter(element.closest('[data-bb-toggle="tree-checkboxes"]'));
                    } else if (element.parent('.input-group').length || element.prop('type') === 'checkbox' || element.prop('type') === 'radio') {
                        error.insertAfter(element.parent());
                    } else if ($(element).data('select2')) {
                        error.insertAfter(element.next('span'));
                    } else {
                        error.insertAfter(element);
                    }
                },

                highlight: function(element) {
                    $(element).closest('.form-control').removeClass('is-valid').addClass('is-invalid');
                },

                @if (isset($validator['ignore']) && is_string($validator['ignore']))
                    ignore: "{{ $validator['ignore'] }}",
                @endif

                unhighlight: function(element) {
                    $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid');
                },

                success: function(element) {
                    $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid');
                },

                focusInvalid: false,
                @if (config('core.js-validation.js-validation.focus_on_error'))
                    invalidHandler: function(form, validator) {
                        if (!validator.numberOfInvalids()) {
                            return;
                        }

                        $('html, body').animate({
                            scrollTop: $(validator.errorList[0].element).offset().top
                        }, {{ config('core.js-validation.js-validation.duration_animate') }});
                        $(validator.errorList[0].element).focus();
                    },
                @endif

                rules: {{ Js::from($validator['rules']) }}
            });
        });
    });
</script>
