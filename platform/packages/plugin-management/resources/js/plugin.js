class PluginManagement {
    init() {
        $(document).on('click', '.btn-trigger-remove-plugin', (event) => {
            event.preventDefault()

            $('#confirm-remove-plugin-button').data('url', $(event.currentTarget).data('url'))
            $('#remove-plugin-modal').modal('show')
        })

        $(document).on('click', '#confirm-remove-plugin-button', (event) => {
            event.preventDefault()

            const _self = $(event.currentTarget)

            $httpClient
                .make()
                .withButtonLoading(_self)
                .delete(_self.data('url'))
                .then(({ data }) => {
                    Botble.showSuccess(data.message)
                    window.location.reload()
                })
                .finally(() => $('#remove-plugin-modal').modal('hide'))
        })

        $(document).on('click', '.btn-trigger-update-plugin', (event) => {
            event.preventDefault()

            const currentTarget = $(event.currentTarget)
            const url = currentTarget.data('update-url')

            currentTarget.prop('disabled', true)

            $httpClient
                .make()
                .withButtonLoading(currentTarget)
                .post(url)
                .then(({ data }) => {
                    Botble.showSuccess(data.message)

                    setTimeout(() => window.location.reload(), 2000)
                })
                .finally(() => currentTarget.prop('disabled', false))
        })

        $(document).on('click', '.btn-trigger-change-status', async (event) => {
            event.preventDefault()

            const _self = $(event.currentTarget)

            const pluginName = _self.data('plugin')

            const changeStatusUrl = _self.data('change-status-url')

            if (_self.data('status') === 1) {
                Botble.showButtonLoading(_self)
                await this.activateOrDeactivatePlugin(changeStatusUrl)
                Botble.hideButtonLoading(_self)
                return
            }

            $httpClient
                .makeWithoutErrorHandler()
                .withButtonLoading(_self)
                .post(_self.data('check-requirement-url'))
                .then(() => this.activateOrDeactivatePlugin(changeStatusUrl))
                .catch((e) => {
                    const { data, message } = e.response.data

                    if (data && data.existing_plugins_on_marketplace) {
                        const $modal = $('#confirm-install-plugin-modal')
                        $modal.find('.modal-body #requirement-message').html(message)
                        $modal.find('input[name="plugin_name"]').val(pluginName)
                        $modal.find('input[name="ids"]').val(data.existing_plugins_on_marketplace)
                        $modal.modal('show')

                        return
                    }

                    Botble.showError(message)
                })
        })

        if ($('button[data-check-update]').length) {
            this.checkUpdate()
        }

        this.handleFilters()
    }

    handleFilters() {
        let search = $('[data-bb-toggle="change-search"]').val().toLowerCase()
        let status = $('[data-bb-toggle="change-filter-plugin-status"]:checked').val()

        $('button[data-bb-toggle="change-filter-plugin-status"]').each((index, element) => {
            const status = $(element).data('value') || $(element).val()
            const $visiblePluginItems =
                status === 'all' ? $('.plugin-item:visible') : $(`.plugin-item[data-status="${status}"]:visible`)
            $(`[data-bb-toggle="plugins-count"][data-status="${status}"]`).text($visiblePluginItems.length)
        })

        const applyFilters = () => {
            const $pluginItems = $('.plugin-item')

            $pluginItems.each((index, element) => {
                const $element = $(element)
                const name = $element.data('name').toLowerCase()
                const description = $element.data('description').toLowerCase()
                const author = $element.data('author').toLowerCase()

                const nameMatch = name.includes(search)
                const authorMatch = author.includes(search)
                const descriptionMatch = description.includes(search)
                const statusMatch =
                    status === 'all' ||
                    $element.data('status') === status ||
                    (status === 'updates-available' && $element.data('available-for-updates'))

                if ((nameMatch || descriptionMatch || authorMatch) && statusMatch) {
                    $element.show()
                } else {
                    $element.hide()
                }
            })

            const $visiblePluginItems = $('.plugin-item:visible')

            if ($visiblePluginItems.length === 0) {
                $('.empty').show()
            } else {
                $('.empty').hide()
            }
        }

        $(document).on('keyup', '[data-bb-toggle="change-search"]', (event) => {
            event.preventDefault()

            search = $(event.currentTarget).val().toLowerCase()
            applyFilters()
        })

        $(document).on('change', 'input[data-bb-toggle="change-filter-plugin-status"]', (event) => {
            status = $(event.currentTarget).val()
            applyFilters()
        })

        $(document).on('click', 'button[data-bb-toggle="change-filter-plugin-status"]', (event) => {
            const newValue = $(event.target).data('value')
            $('[data-bb-toggle="status-filter-label"]').text($(event.target).text())

            $('.dropdown-item').removeClass('active')

            $(event.target).addClass('active')

            status = newValue
            applyFilters()
        })
    }

    checkUpdate() {
        $httpClient
            .make()
            .post($('button[data-check-update]').data('check-update-url'))
            .then(({ data }) => {
                if (!data.data) {
                    return
                }

                Object.keys(data.data).forEach((key) => {
                    const plugin = data.data[key]

                    const $button = $(`button[data-check-update="${plugin.name}"]`)

                    const url = $button.data('update-url').replace('__id__', plugin.id)

                    $button.data('update-url', url).show()

                    const $parent = $button.closest('.plugin-item')

                    $parent.data('available-for-updates', true).trigger('change')

                    $('[data-bb-toggle="plugins-count"][data-status="updates-available"]').text(data.data.length)
                })
            })
    }

    async activateOrDeactivatePlugin(url, reload = true) {
        return $httpClient
            .make()
            .put(url)
            .then(({ data }) => {
                Botble.showSuccess(data.message)

                if (reload) {
                    window.location.reload()
                }
            })
    }
}

$(() => {
    new PluginManagement().init()
})
