class WidgetManagement {
    init() {
        let listWidgets = [
            {
                name: 'wrap-widgets',
                pull: 'clone',
                put: false,
            },
        ]

        $.each($('.sidebar-item'), () => {
            listWidgets.push({ name: 'wrap-widgets', pull: true, put: true })
        })

        let saveWidget = (parentElement) => {
            if (parentElement.length > 0) {
                let items = []
                $.each(parentElement.find('li[data-id]'), (index, widget) => {
                    items.push($(widget).find('form').serialize())
                })

                $httpClient
                    .make()
                    .post(BWidget.routes.save_widgets_sidebar, {
                        items: items,
                        sidebar_id: parentElement.data('id'),
                    })
                    .then(({ data }) => {
                        parentElement.find('ul').html(data.data)
                        Botble.callScroll($('.list-page-select-widget'))
                        Botble.initResources()
                        Botble.initMediaIntegrate()
                        Botble.initTreeCheckboxes()
                        Botble.showSuccess(data.message)
                    })
                    .finally(() => {
                        parentElement.find('.widget-save i').remove()
                    })
            }
        }

        listWidgets.forEach((groupOpts, i) => {
            Sortable.create(document.getElementById('wrap-widget-' + (i + 1)), {
                sort: i !== 0,
                group: groupOpts,
                delay: 0, // time in milliseconds to define when the sorting should start
                disabled: false, // Disables the sortable if set to true.
                store: null, // @see Store
                animation: 150, // ms, animation speed moving items when sorting, `0` — without animation
                handle: '.card-header',
                ghostClass: 'sortable-ghost', // Class name for the drop placeholder
                chosenClass: 'sortable-chosen', // Class name for the chosen item
                dataIdAttr: 'data-id',

                forceFallback: false, // ignore the HTML5 DnD behaviour and force the fallback to kick in
                fallbackClass: 'sortable-fallback', // Class name for the cloned DOM Element when using forceFallback
                fallbackOnBody: false, // Appends the cloned DOM Element into the Document's Body

                scroll: true, // or HTMLElement
                scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
                scrollSpeed: 10, // px

                // Changed sorting within list
                onUpdate: (evt) => {
                    if (evt.from !== evt.to) {
                        saveWidget($(evt.from).closest('.sidebar-item'))
                    }
                    saveWidget($(evt.item).closest('.sidebar-item'))
                },
                onAdd: (evt) => {
                    if (evt.from !== evt.to) {
                        saveWidget($(evt.from).closest('.sidebar-item'))
                    }
                    saveWidget($(evt.item).closest('.sidebar-item'))
                },
            })
        })

        $('#wrap-widgets')
            .on('click', '.widget-control-delete', (event) => {
                event.preventDefault()
                let _self = $(event.currentTarget)

                let widget = _self.closest('li')

                Botble.showButtonLoading(_self)

                $httpClient
                    .make()
                    .delete(BWidget.routes.delete, {
                        widget_id: widget.data('id'),
                        position: widget.data('position'),
                        sidebar_id: _self.closest('.sidebar-item').data('id'),
                    })
                    .then(({ data }) => {
                        Botble.showSuccess(data.message)
                        _self.closest('.sidebar-item').find('ul').html(data.data)
                    })
                    .finally(() => {
                        Botble.showButtonLoading(widget.find('.widget-control-delete'))
                    })
            })
            .on('click', '.widget-item .card-header', (event) => {
                let _self = $(event.currentTarget)
                _self.closest('.widget-item').find('.widget-content').slideToggle(300)
                if (_self.find('.ti').hasClass('ti-chevron-up')) {
                    setTimeout(function () {
                        _self.closest('.card').toggleClass('card-no-border-bottom-radius')
                    }, 300)
                } else {
                    _self.closest('.card').toggleClass('card-no-border-bottom-radius')
                }
                _self.find('.ti').toggleClass('ti-chevron-down').toggleClass('ti-chevron-up')
            })
            .on('click', '.sidebar-item .card-header .button-sidebar.btn-action', (event) => {
                let _self = $(event.currentTarget)
                _self.closest('.card').find('.card-body').slideToggle(300)
                _self.find('.ti').toggleClass('ti-chevron-down').toggleClass('ti-chevron-up')
            })
            .on('click', '.widget-save', (event) => {
                event.preventDefault()
                let _self = $(event.currentTarget)
                Botble.showButtonLoading(_self)
                saveWidget(_self.closest('.sidebar-item'))
            })
    }
}

$(() => {
    new WidgetManagement().init()
})
