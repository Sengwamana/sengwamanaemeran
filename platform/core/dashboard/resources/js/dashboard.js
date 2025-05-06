let callbackWidgets = {}

class BDashboard {
    static loadWidget($widget, url, data, callback) {
        const widget = $widget.closest('.widget-item')
        const widgetId = widget.prop('id')
        const loading = widget.find('.card')

        if (typeof callback !== 'undefined') {
            callbackWidgets[widgetId] = callback
        }

        const $collapseExpand = widget.find('a.collapse-expand')

        if ($collapseExpand.length && $collapseExpand.hasClass('collapse')) {
            return
        }

        Botble.showLoading(loading)

        if (typeof data === 'undefined' || data == null) {
            data = {}
        }

        const predefinedRange = widget.find('.dropdown.predefined_range .dropdown-item.active')

        if (predefinedRange.length) {
            data.predefined_range = predefinedRange.data('key')
        }

        $httpClient
            .makeWithoutErrorHandler()
            .get(url, data)
            .then(({ data }) => {
                $widget.html(data.data)

                if (typeof callback !== 'undefined') {
                    callback()
                } else if (callbackWidgets[widgetId]) {
                    callbackWidgets[widgetId]()
                }

                BDashboard.initSortable()
            })
            .catch(({ response, message }) => {
                let content = response?.data?.data

                if (!content && message) {
                    content = `<div class="empty"><p class="empty-subtitle text-muted">${message}</p></div>`
                }

                $widget.html(content)
            })
            .finally(() => {
                Botble.hideLoading(loading)
            })
    }

    static initSortable() {
        const $widgetsList = $('[data-bb-toggle="widgets-list"]')

        if ($widgetsList.length > 0) {
            Sortable.create($widgetsList[0], {
                group: 'widgets',
                sort: true,
                delay: 0,
                disabled: false,
                store: null,
                animation: 150,
                handle: '.card-header',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dataIdAttr: 'data-id',
                forceFallback: false,
                fallbackClass: 'sortable-fallback',
                fallbackOnBody: false,
                scroll: true,
                scrollSensitivity: 30,
                scrollSpeed: 10,
                onUpdate: () => {
                    let items = []

                    $.each($('.widget-item'), (index, widget) => {
                        items.push($(widget).prop('id'))
                    })

                    $httpClient
                        .makeWithoutErrorHandler()
                        .post($widgetsList.data('url'), { items: items })
                        .then(({ data }) => {
                            Botble.showSuccess(data.message)
                        })
                },
            })
        }
    }

    init() {
        $('[data-bb-toggle="widgets-list"]').on('click', '.page-link', (e) => {
            e.preventDefault()
            const $this = $(e.currentTarget)
            const href = $this.prop('href')

            if (href) {
                BDashboard.loadWidget($this.closest('.widget-item').find('.widget-content'), href)
            }
        })

        $(document).on('click', '.card-actions .dropdown.predefined_range .dropdown-item', (e) => {
            e.preventDefault()

            const $this = $(e.currentTarget)

            $this.closest('.dropdown').find('.dropdown-toggle').text($this.data('label'))
            $this.closest('.dropdown').find('.dropdown-item').removeClass('active')
            $this.addClass('active')

            BDashboard.loadWidget(
                $this.closest('.widget-item').find('.widget-content'),
                $this.closest('.widget-item').data('url'),
                { changed_predefined_range: 1 }
            )
        })
    }
}

$(() => {
    new BDashboard().init()
    window.BDashboard = BDashboard

    $(document)
        .on('submit', '[data-bb-toggle="widgets-management-modal"] form', function (event) {
            event.preventDefault()
            const form = $(event.currentTarget)
            const modal = $(this).closest('.modal')

            $httpClient
                .make()
                .withButtonLoading(form.find('button[type="submit"]'))
                .postForm(form.prop('action'), new FormData(form[0]))
                .then(({ data }) => {
                    Botble.showSuccess(data.message)

                    setTimeout(() => {
                        window.location.reload()
                    }, 1000)
                })
                .finally(() => {
                    modal.modal('hide')
                })
        })
        .on('change', '[data-bb-toggle="widgets-management-item"]', function (event) {
            const $this = $(event.currentTarget)

            if ($this.prop('checked')) {
                $this.closest('td').removeClass('text-decoration-line-through text-muted')
            } else {
                $this.closest('td').addClass('text-decoration-line-through text-muted')
            }
        })
})
