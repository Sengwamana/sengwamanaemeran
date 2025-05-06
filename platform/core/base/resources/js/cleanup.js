'use strict'
$(() => {
    $(document).on('click', '.btn-trigger-cleanup', (event) => {
        event.preventDefault()
        $('#cleanup-modal').modal('show')
    })

    $(document).on('click', '#cleanup-submit-action', (event) => {
        event.preventDefault()
        event.stopPropagation()
        const _self = $(event.currentTarget)

        Botble.showButtonLoading(_self)

        const $form = $('#form-cleanup-database')
        const $modal = $('#cleanup-modal')

        $httpClient
            .make()
            .post($form.prop('action'), new FormData($form[0]))
            .then(({ data }) => Botble.showSuccess(data.message))
            .finally(() => {
                Botble.hideButtonLoading(_self)
                $modal.modal('hide')
            })
    })
})
