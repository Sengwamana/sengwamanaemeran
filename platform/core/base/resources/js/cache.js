class CacheManagement {
    init() {
        $(document).on('click', '.btn-clear-cache', (event) => {
            event.preventDefault()

            let _self = $(event.currentTarget)

            Botble.showButtonLoading(_self)

            $httpClient
                .make()
                .post(_self.data('url'), { type: _self.data('type') })
                .then(({ data }) => Botble.showSuccess(data.message))
                .finally(() => Botble.hideButtonLoading(_self))
        })
    }
}

$(() => {
    new CacheManagement().init()
})
