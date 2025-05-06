'use strict'

$(() => {
    $(document).on('change', '.shortcode-tabs-quantity-select', function () {
        const $this = $(this)
        const quantity = parseInt($this.val()) || 1
        const key = $this.data('key')

        $this.val(quantity)

        const $section = $this.closest('.shortcode-admin-config')
        const $template = $section.find('.shortcode-template').first().clone().removeClass('shortcode-template');

        for (let index = 1; index <= $this.data('max'); index++) {
            const $el = key ? $section.find(`[data-tab-id=${key}_${index}]`) : $section.find(`[data-tab-id=${index}]`)
            if (index <= quantity) {
                if (! $el.is(':visible')) {
                    $el.slideDown()
                    $el.find('[data-name]').map((i, e) => $(e).prop('name', $(e).data('name')))
                }
            } else {
                $el.slideUp()
                $el.find('[name]').map(function (i, e) {
                    $(e).data('name', $(e).prop('name'))
                    $(e).removeProp('name')
                })
            }
        }
    })
})
