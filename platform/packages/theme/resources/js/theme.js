class ThemeManagement {
    init() {
        $(document).on('click', '.btn-trigger-active-theme', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)
            Botble.showButtonLoading(_self)

            $httpClient
                .make()
                .post(_self.data('url'))
                .then(({ data }) => {
                    Botble.showSuccess(data.message)
                    window.location.reload()
                })
                .finally(() => {
                    Botble.hideButtonLoading(_self)
                })
        })

        $(document).on('click', '.btn-trigger-remove-theme', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)
            $('#confirm-remove-theme-button')
                .data('theme', _self.data('theme'))
                .data('url', _self.data('url'))

            $('#remove-theme-modal').modal('show')
        })

        $(document).on('click', '#confirm-remove-theme-button', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)
            Botble.showButtonLoading(_self)

            $httpClient
                .make()
                .post(_self.data('url'))
                .then(({ data }) => {
                    Botble.showSuccess(data.message)
                    window.location.reload()
                })
                .finally(() => {
                    Botble.hideButtonLoading(_self)
                    $('#remove-theme-modal').modal('hide')
                })
        })
    }
}

$(() => {
    new ThemeManagement().init()
})
