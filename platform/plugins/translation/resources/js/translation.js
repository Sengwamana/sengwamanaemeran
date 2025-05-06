$(() => {
    $(document).on('click', '.button-import-groups, .button-re-import', (event) => {
        event.preventDefault()

        const $button = $(event.currentTarget)

        $httpClient
            .make()
            .withButtonLoading($button)
            .postForm($button.data('url'))
            .then(({ data }) => {
                Botble.showSuccess(data.message)

                if ($button.closest('.modal').length) {
                    $button.closest('.modal').modal('hide')

                    const $table = $('.translations-table .table')

                    if ($table.length) {
                        $table.DataTable().ajax.url(window.location.href).load()
                    } else {
                        setTimeout(() => {
                            window.location.reload()
                        }, 1000)
                    }
                } else {
                    setTimeout(() => {
                        window.location.reload()
                    }, 1000)
                }
            })
    })
})
