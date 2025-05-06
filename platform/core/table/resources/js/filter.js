class TableFilter {
    $filterForm = $(document).find('form.filter-form')
    $table = this.$filterForm.closest('.table-wrapper').find('table')

    loadData($element) {
        $httpClient
            .make()
            .get($('.filter-data-url').val(), {
                class: $('.filter-data-class').val(),
                key: $element.val(),
                value: $element.closest('.filter-item').find('.filter-column-value').val(),
            })
            .then(({ data: res }) => {
                const data = $.map(res.data, (value, key) => {
                    return { id: key, name: value }
                })
                $element.closest('.filter-item').find('.filter-column-value-wrap').html(res.html)

                const $input = $element.closest('.filter-item').find('.filter-column-value')
                if ($input.length && $input.prop('type') === 'text') {
                    $input.typeahead({ source: data })
                    $input.data('typeahead').source = data
                }

                Botble.initResources()
            })
    }

    init() {
        const that = this

        $.each($('.filter-items-wrap .filter-column-key'), (index, element) => {
            if ($(element).val()) {
                that.loadData($(element))
            }
        })

        this.$filterForm
            .on('change', '.filter-column-key', (event) => {
                that.loadData($(event.currentTarget))
            })
            .on('click', '.add-more-filter', () => {
                const $template = $(document).find('.sample-filter-item-wrap')
                const html = $template.html()

                $(document).find('.filter-items-wrap').append(html.replace('<script>', '').replace('<\\/script>', ''))
                Botble.initResources()

                const element = $(document)
                    .find('.filter-items-wrap .filter-item:last-child')
                    .find('.filter-column-key')
                if ($(element).val()) {
                    that.loadData(element)
                }
            })
            .on('click', '.btn-remove-filter-item', (event) => {
                event.preventDefault()

                const $currentTarget = $(event.currentTarget)

                $currentTarget.closest('.filter-item').remove()
                $currentTarget.tooltip('hide')
            })
            .on('click', '.btn-apply', (event) => {
                event.preventDefault()

                const button = $(event.currentTarget)
                const form = button.closest('form.filter-form')

                this.$filterForm.find('[data-bb-toggle="datatable-reset-filter"]').show()

                const url = new URL(window.location.href)

                const params = new URLSearchParams(url.search)
                const data = form.serializeArray()
                const paramsKey = {}

                $.each(data, (index, item) => {
                    let keyName = item.name

                    if (typeof keyName === 'string' && keyName.endsWith('[]')) {
                        let keyValue = (paramsKey[keyName] = paramsKey[keyName] || 0)

                        params.set(`${keyName.replace('[]', `[${keyValue}]`)}`, item.value)

                        paramsKey[keyName]++
                    } else {
                        params.set(item.name, item.value)
                    }
                })

                window.history.pushState({}, '', `${url.pathname}?${params.toString()}`)

                this.reloadDatatable(this.$table.DataTable())
            })
            .on('click', '[data-bb-toggle="datatable-reset-filter"]', (event) => {
                event.preventDefault()

                this.$filterForm.find('.form-filter:not(.filter-item-default)').remove()
                this.$filterForm.find('.filter-item').find('.filter-column-key').val('').trigger('change')
                this.$filterForm.find('.filter-item').find('.filter-column-operator').val('=')
                this.$filterForm.find('.filter-item').find('.filter-column-value').val('')

                $(event.currentTarget).hide()

                const url = new URL(window.location.href)

                window.history.pushState({}, '', url.pathname)

                this.reloadDatatable(this.$table.DataTable())
            })
    }

    reloadDatatable(datatable) {
        datatable.ajax.url(window.location.href).load()
    }
}

$(() => {
    new TableFilter().init()
})
