class PermalinkField {
    constructor() {
        let $slugBox = $(document).find('.slug-field-wrapper')

        $(document).on('blur', `.js-base-form input[name=${$slugBox.data('field-name')}]`, (e) => {
            $slugBox = $(document).find('.slug-field-wrapper')

            if ($slugBox.find('input[name="slug"]').is('[readonly]')) {
                return
            }

            const value = $(e.currentTarget).val()

            if (value !== null && value !== '' && !$slugBox.find('input[name="slug"]').val()) {
                createSlug(value, 0)
            }
        })

        let timeoutId

        $(document).on('keyup', 'input[name="slug"]', (event) => {
            clearTimeout(timeoutId)

            timeoutId = setTimeout(() => {
                const input = $(event.currentTarget)

                $slugBox = $(document).find('.slug-field-wrapper')

                if ($slugBox.has('.slug-data').length === 0) {
                    return
                }

                const value = input.val()

                if (value !== null && value !== '') {
                    createSlug(value, $slugBox.find('.slug-data').data('id') || 0)
                } else {
                    input.addClass('is-invalid')
                }
            }, 700)
        })

        $(document).on('click', '[data-bb-toggle="generate-slug"]', (e) => {
            e.preventDefault()

            const $fromField = $(e.currentTarget)
                .closest('.js-base-form')
                .find(`input[name=${$slugBox.data('field-name')}]`)

            if ($fromField.val() !== null && $fromField.val() !== '') {
                createSlug($fromField.val(), $slugBox.find('.slug-data').data('id') || 0)
            }
        })

        const toggleInputSlugState = (isShow = false) => {
            const $icon = $slugBox.find('.slug-actions a')
            const $spinner = $('<div class="spinner-border spinner-border-sm" role="status"></div>')

            if (isShow) {
                $icon.removeClass('d-none')
                $slugBox.find('.spinner-border').remove()
            } else {
                $icon.addClass('d-none')
                $icon.after($spinner)
            }
        }

        /**
         * @param {string} value
         * @param {number} id
         */
        const createSlug = (value, id) => {
            $slugBox = $(document).find('.slug-field-wrapper')

            const form = $slugBox.closest('form')
            const $slugId = $slugBox.find('.slug-data')

            if (! $slugBox.length || ! $slugId.length || ! form.length) {
                return
            }

            toggleInputSlugState()

            $.ajax({
                type: 'POST',
                url: $slugId.data('url'),
                data: {
                    value: value,
                    slug_id: id.toString(),
                    model: form.find('input[name="model"]').val(),
                    _token: form.find('input[name="_token"]').val(),
                },
                success: function(data) {
                    toggleInputSlugState(true);

                    const url = `${$slugId.data('view')}${data.toString().replace('/', '')}`;

                    $slugBox.find('input[name="slug"]').val(data);
                    form.find('.page-url-seo p').text(url);
                    $slugBox.find('.slug-current').val(data);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    }
}

$(() => {
    new PermalinkField()
})
