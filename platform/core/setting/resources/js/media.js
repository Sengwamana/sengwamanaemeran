$(() => {
    $('.generate-thumbnails-trigger-button').on('click', (event) => {
        event.preventDefault()

        const currentTarget = $(event.currentTarget)

        const $form = currentTarget.closest('form')

        $httpClient
            .make()
            .withButtonLoading(currentTarget)
            .postForm($form.prop('action'), new FormData($form[0]))
            .then(({ data }) => {
                const $modal = $('#generate-thumbnails-modal')

                $modal.modal('show')
                $modal.data('total-files', data.data.files_count)
            })
    })

    $('#generate-thumbnails-button').on('click', (event) => {
        event.preventDefault()

        const currentTarget = $(event.currentTarget)

        const $modal = currentTarget.closest('.modal')
        const $form = currentTarget.closest('form')

        const totalFiles = $modal.data('total-files')
        let message = null

        Botble.showButtonLoading(currentTarget)

        function sendRequest(offset = 0, limit = $modal.data('chunk-limit')) {
            if (offset > totalFiles) {
                Botble.hideButtonLoading(currentTarget)
                $modal.modal('hide')

                Botble.showSuccess(message)

                return
            }

            $httpClient
                .make()
                .post($form.prop('action'), { total: totalFiles, offset, limit })
                .then(({ data }) => {
                    message = data.message

                    if (data.data.next) {
                        sendRequest(data.data.next, limit)
                    }
                })
                .finally(() => {
                    if (offset > totalFiles) {
                        Botble.hideButtonLoading(currentTarget)
                        $modal.modal('hide')
                    }
                });
        }

        sendRequest()
    })

    $(document).on('change', '.check-all', (event) => {
        const currentTarget = $(event.currentTarget)
        const set = currentTarget.attr('data-set')
        const checked = currentTarget.prop('checked')

        $(set).each((index, el) => {
            $(el).prop('checked', checked)
        })
    })
})
