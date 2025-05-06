$(() => {
    $('.custom-field-options').sortable({
        cursor: 'move',
    })

    $(document)
        .on('change', '.custom-field-form select[name="type"]', (e) => {
            const $currentTarget = $(e.currentTarget)
            const $customFieldOptionsBox = $currentTarget.closest('form').find('.custom-field-options-box')

            if ($currentTarget.val() === 'dropdown' || $currentTarget.val() === 'radio') {
                $customFieldOptionsBox.show()
            } else {
                $customFieldOptionsBox.hide()
            }
        })
        .on('click', '[data-bb-toggle="add-option"]', (e) => {
            e.preventDefault()

            const $currentTarget = $(e.currentTarget)
            const $table = $currentTarget.closest('.card').find('.custom-field-options')

            const $tr = $table.find('tr').last().clone()
            const index = $table.find('tr').length

            $tr.find('[data-bb-toggle="option-label"]').val('').prop('name', `options[${index}][label]`)
            $tr.find('[data-bb-toggle="option-value"]').val('').prop('name', `options[${index}][value]`)

            $table.append($tr)
        })
        .on('click', '[data-bb-toggle="remove-option"]', (e) => {
            e.preventDefault()

            const $currentTarget = $(e.currentTarget)
            const $tr = $currentTarget.closest('tr')

            $tr.remove()
        })
})
