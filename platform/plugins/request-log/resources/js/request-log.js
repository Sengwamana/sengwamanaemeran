$(() => {
    if (typeof BDashboard !== 'undefined') {
        BDashboard.loadWidget(
            $('#widget_request_errors').find('.widget-content'),
            $('#widget_request_errors').data('url')
        )
    }

    $(document).on('click', '.empty-request-logs-button', function (event) {
        event.preventDefault()
        const $modal = $('#modal-confirm-delete-records')

        $modal.modal('show')

        $modal.on('click', '.button-delete-records', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)

            $httpClient
                .make()
                .withButtonLoading(_self)
                .delete(_self.data('url'))
                .then(({ data }) => {
                    _self.closest('.modal').modal('hide')
                    $('#botble-request-log-tables-request-log-table').DataTable().draw()
                    Botble.showSuccess(data.message)
                })
        })
    })
})
