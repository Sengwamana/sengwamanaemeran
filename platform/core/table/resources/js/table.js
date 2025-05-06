;(($, DataTable) => {
    'use strict'

    let _buildParams = (dt, action) => {
        let params = dt.ajax.params()
        params.action = action
        params._token = $('meta[name="csrf-token"]').attr('content')

        return params
    }

    let _downloadFromUrl = function (url, params) {
        let postUrl = url + '/export'
        let xhr = new XMLHttpRequest()
        xhr.open('POST', postUrl, true)
        xhr.responseType = 'arraybuffer'
        xhr.onload = function () {
            if (this.status === 200) {
                let filename = ''
                let disposition = xhr.getResponseHeader('Content-Disposition')
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    let filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/
                    let matches = filenameRegex.exec(disposition)
                    if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '')
                }
                let type = xhr.getResponseHeader('Content-Type')

                let blob = new Blob([this.response], { type: type })
                if (typeof window.navigator.msSaveBlob !== 'undefined') {
                    // IE workaround for "HTML7007: One or more blob URLs were revoked by closing the blob for which they were created. These URLs will no longer resolve as the data backing the URL has been freed."
                    window.navigator.msSaveBlob(blob, filename)
                } else {
                    let URL = window.URL || window.webkitURL
                    let downloadUrl = URL.createObjectURL(blob)

                    if (filename) {
                        // use HTML5 a[download] attribute to specify filename
                        let a = document.createElement('a')
                        // safari doesn't support this yet
                        if (typeof a.download === 'undefined') {
                            window.location = downloadUrl
                        } else {
                            a.href = downloadUrl
                            a.download = filename
                            document.body.appendChild(a)
                            a.click()
                        }
                    } else {
                        window.location = downloadUrl
                    }

                    setTimeout(() => {
                        URL.revokeObjectURL(downloadUrl)
                    }, 100) // cleanup
                }
            }
        }
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded')
        xhr.send($.param(params))
    }

    let _buildUrl = (dt, action) => {
        let url = dt.ajax.url() || ''
        let params = dt.ajax.params()
        params.action = action

        if (url.indexOf('?') > -1) {
            return url + '&' + $.param(params)
        }

        return url + '?' + $.param(params)
    }

    DataTable.ext.buttons.excel = {
        className: 'buttons-excel',

        text: (dt) => {
            return `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                    <path d="M8 11h8v7h-8z"></path>
                    <path d="M8 15h8"></path>
                    <path d="M11 11v7"></path>
                </svg>
            ${dt.i18n(
                'buttons.excel',
                BotbleVariables.languages.tables.excel ? BotbleVariables.languages.tables.excel : 'Excel'
            )}`
        },

        action: (e, dt) => {
            window.location = _buildUrl(dt, 'excel')
        },
    }

    DataTable.ext.buttons.postExcel = {
        className: 'buttons-excel',

        text: (dt) => {
            return `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                <path d="M8 11h8v7h-8z"></path>
                <path d="M8 15h8"></path>
                <path d="M11 11v7"></path>
            </svg>
            ${dt.i18n(
                'buttons.excel',
                BotbleVariables.languages.tables.excel ? BotbleVariables.languages.tables.excel : 'Excel'
            )}`
        },

        action: (e, dt) => {
            let url = dt.ajax.url() || window.location.href
            let params = _buildParams(dt, 'excel')

            _downloadFromUrl(url, params)
        },
    }

    DataTable.ext.buttons.export = {
        extend: 'collection',

        className: 'buttons-export',

        text: (dt) => {
            return `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                <path d="M7 11l5 5l5 -5"></path>
                <path d="M12 4l0 12"></path>
            </svg>
            ${dt.i18n(
                'buttons.export',
                BotbleVariables.languages.tables.export ? BotbleVariables.languages.tables.export : 'Export'
            )}`
        },

        buttons: ['csv', 'excel'],
    }

    DataTable.ext.buttons.csv = {
        className: 'buttons-csv',

        text: (dt) => {
            return `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4"></path>
                <path d="M7 16.5a1.5 1.5 0 0 0 -3 0v3a1.5 1.5 0 0 0 3 0"></path>
                <path d="M10 20.25c0 .414 .336 .75 .75 .75h1.25a1 1 0 0 0 1 -1v-1a1 1 0 0 0 -1 -1h-1a1 1 0 0 1 -1 -1v-1a1 1 0 0 1 1 -1h1.25a.75 .75 0 0 1 .75 .75"></path>
                <path d="M16 15l2 6l2 -6"></path>
            </svg>
            ${dt.i18n(
                'buttons.csv',
                BotbleVariables.languages.tables.csv ? BotbleVariables.languages.tables.csv : 'CSV'
            )}`
        },

        action: (e, dt) => {
            window.location = _buildUrl(dt, 'csv')
        },
    }

    DataTable.ext.buttons.postCsv = {
        className: 'buttons-csv',

        text: (dt) => {
            return `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4"></path>
                <path d="M7 16.5a1.5 1.5 0 0 0 -3 0v3a1.5 1.5 0 0 0 3 0"></path>
                <path d="M10 20.25c0 .414 .336 .75 .75 .75h1.25a1 1 0 0 0 1 -1v-1a1 1 0 0 0 -1 -1h-1a1 1 0 0 1 -1 -1v-1a1 1 0 0 1 1 -1h1.25a.75 .75 0 0 1 .75 .75"></path>
                <path d="M16 15l2 6l2 -6"></path>
            </svg>
            ${dt.i18n(
                'buttons.csv',
                BotbleVariables.languages.tables.csv ? BotbleVariables.languages.tables.csv : 'CSV'
            )}`
        },

        action: (e, dt) => {
            let url = dt.ajax.url() || window.location.href
            let params = _buildParams(dt, 'csv')

            _downloadFromUrl(url, params)
        },
    }

    DataTable.ext.buttons.pdf = {
        className: 'buttons-pdf',

        text: (dt) => {
            return `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4"></path>
                <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6"></path>
                <path d="M17 18h2"></path>
                <path d="M20 15h-3v6"></path>
                <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z"></path>
            </svg>
            ${dt.i18n('buttons.pdf', 'PDF')}`
        },

        action: (e, dt) => {
            window.location = _buildUrl(dt, 'pdf')
        },
    }

    DataTable.ext.buttons.postPdf = {
        className: 'buttons-pdf',

        text: (dt) => {
            return `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4"></path>
                <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6"></path>
                <path d="M17 18h2"></path>
                <path d="M20 15h-3v6"></path>
                <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z"></path>
            </svg>
            ${dt.i18n('buttons.pdf', 'PDF')}`
        },

        action: (e, dt) => {
            let url = dt.ajax.url() || window.location.href
            let params = _buildParams(dt, 'pdf')

            _downloadFromUrl(url, params)
        },
    }

    DataTable.ext.buttons.print = {
        className: 'buttons-print',

        text: (dt) => {
            return `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                    <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                    <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
                </svg>
                ${dt.i18n(
                    'buttons.print',
                    BotbleVariables.languages.tables.print ? BotbleVariables.languages.tables.print : 'Print'
                )}`
        },

        action: (e, dt) => {
            window.location = _buildUrl(dt, 'print')
        },
    }

    DataTable.ext.buttons.reset = {
        className: 'buttons-reset',

        text: (dt) => {
            return `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M9 11l-4 4l4 4m-4 -4h11a4 4 0 0 0 0 -8h-1"></path>
                </svg>
                ${dt.i18n(
                    'buttons.reset',
                    BotbleVariables.languages.tables.reset ? BotbleVariables.languages.tables.reset : 'Reset'
                )}`
        },

        action: () => {
            $('.table thead input').val('').keyup()
            $('.table thead select').val('').change()
        },
    }

    DataTable.ext.buttons.reload = {
        className: 'buttons-reload',

        text: (dt) => {
            return `
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                </svg>
                ${dt.i18n(
                    'buttons.reload',
                    BotbleVariables.languages.tables.reload ? BotbleVariables.languages.tables.reload : 'Reload'
                )}`
        },

        action: (e, dt) => {
            dt.draw(false)
        },
    }

    DataTable.ext.buttons.create = {
        className: 'buttons-create',

        text: (dt) => {
            return `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M12 5l0 14"></path>
                <path d="M5 12l14 0"></path>
            </svg>
            ${dt.i18n('buttons.create', 'Create')}`
        },

        action: () => {
            window.location = window.location.href.replace(/\/+$/, '') + '/create'
        },
    }

    if (typeof DataTable.ext.buttons.copyHtml5 !== 'undefined') {
        $.extend(DataTable.ext.buttons.copyHtml5, {
            text: (dt) => {
                return `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>
                    <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>
                </svg>
                ${dt.i18n('buttons.copy', 'Copy')}`
            },
        })
    }

    class TableManagement {
        constructor() {
            this.currentTableHash = window.DATATABLES_RANDOM_HASH || ""
            this.init()
            this.handleActionsRow()
            this.handleActionsExport()
        }

        init() {
            $(document).on('change', '.table-check-all', (event) => {
                let _self = $(event.currentTarget)
                let set = _self.attr('data-set')
                let checked = _self.prop('checked')

                $(set).each((index, el) => {
                    if (checked) {
                        $(el).prop('checked', true).trigger('change')
                    } else {
                        $(el).prop('checked', false).trigger('change')
                    }
                })
            })

            $(document).find('.table-check-all').closest('th').removeAttr('title')

            $(document).on('change', '.checkboxes', (event) => {
                let _self = $(event.currentTarget)
                let table = _self.closest('.table-wrapper').find('.table').prop('id')
                let checkboxAll = _self.closest('.table-wrapper').find('.table-check-all')

                let ids = []
                let $table = $('#' + table)

                $table.find('.checkboxes:checked').each((i, el) => {
                    ids[i] = $(el).val()
                })

                let row = _self.closest('tr')

                if (_self.prop('checked')) {
                    row.addClass('selected')
                } else {
                    row.removeClass('selected')
                }

                if (ids.length !== $table.find('.checkboxes').length) {
                    checkboxAll.prop('checked', false)

                    if (ids.length > 0) {
                        checkboxAll.prop('indeterminate', true)
                    } else {
                        checkboxAll.prop('indeterminate', false)
                    }
                } else {
                    checkboxAll.prop('checked', true)
                    checkboxAll.prop('indeterminate', false)
                }
            })

            $(document).on('click', '.btn-show-table-options', (event) => {
                event.preventDefault()
                $(event.currentTarget).closest('.table-wrapper').find('.table-configuration-wrap').slideToggle(500)
            })

            $(document).on('click', '.action-item', (event) => {
                event.preventDefault()
                let span = $(event.currentTarget).find('span[data-href]')
                let action = span.data('action')
                let url = span.data('href')
                if (action && url !== '#') {
                    window.location.href = url
                }
            })

            this.initRandomHash()
        }

        initRandomHash() {
            let localRandomHash = localStorage.getItem('DataTables_Random_Hash')

            if (! localRandomHash && ! this.currentTableHash) {
                return;
            }

            if (localRandomHash !== this.currentTableHash) {
                Object.keys(localStorage).filter(key => key.startsWith('DataTables_')).forEach(key => {
                    localStorage.removeItem(key)
                })

                localStorage.setItem('DataTables_Random_Hash', this.currentTableHash)

                window.location.reload()
            }
        }

        handleActionsRow() {
            let that = this
            $(document).on('click', '.deleteDialog', (event) => {
                event.preventDefault()
                let _self = $(event.currentTarget)

                let url = _self.data('section')

                if (!url) {
                    url = _self.prop('href')
                }

                $('.delete-crud-entry').data('section', url).data('parent-table', _self.closest('.table').prop('id'))
                $('.modal-confirm-delete').modal('show')
            })

            $('.delete-crud-entry').on('click', (event) => {
                event.preventDefault()
                let _self = $(event.currentTarget)

                Botble.showButtonLoading(_self)

                let deleteURL = _self.data('section')

                $httpClient
                    .make()
                    .delete(deleteURL)
                    .then(({ data }) => {
                        window.LaravelDataTables[_self.data('parent-table')]
                            .row($(`a[data-section="${deleteURL}"]`).closest('tr'))
                            .remove()
                            .draw()

                        Botble.showSuccess(data.message)

                        _self.closest('.modal').modal('hide')
                    })
                    .catch(() => {
                        $('.modal-confirm-delete').modal('hide')
                    })
                    .finally(() => {
                        Botble.hideButtonLoading(_self)
                    })
            })

            $(document).on('click', '.delete-many-entry-trigger', (event) => {
                event.preventDefault()
                let _self = $(event.currentTarget)

                let table = _self.closest('.table-wrapper').find('.table').prop('id')

                let ids = []

                $(`#${table}`)
                    .find('.checkboxes:checked')
                    .each((i, el) => {
                        ids[i] = $(el).val()
                    })

                if (ids.length === 0) {
                    Botble.showError(
                        BotbleVariables.languages.tables.please_select_record
                            ? BotbleVariables.languages.tables.please_select_record
                            : 'Please select at least one record to perform this action!'
                    )
                    return false
                }

                $('.delete-many-entry-button')
                    .data('href', _self.prop('href'))
                    .data('parent-table', table)
                    .data('class-item', _self.data('class-item'))

                $('.delete-many-modal').modal('show')
            })

            $('.delete-many-entry-button').on('click', (event) => {
                event.preventDefault()

                let _self = $(event.currentTarget)

                Botble.showButtonLoading(_self)

                let $table = $(`#${_self.data('parent-table')}`)

                let ids = []
                $table.find('.checkboxes:checked').each((i, el) => {
                    ids[i] = $(el).val()
                })

                $httpClient
                    .make()
                    .delete(_self.data('href'), {
                        ids: ids,
                        class: _self.data('class-item'),
                    })
                    .then(({ data }) => {
                        Botble.showSuccess(data.message)

                        $table.find('.table-check-all').prop('checked', false).prop('indeterminate', false)

                        window.LaravelDataTables[_self.data('parent-table')].draw()

                        _self.closest('.modal').modal('hide')
                    })
                    .finally(() => {
                        Botble.hideButtonLoading(_self)
                    })
            })

            $(document).on('click', '[data-trigger-bulk-action]', (event) => {
                event.preventDefault()

                const _self = $(event.currentTarget)

                const tableId = _self.closest('.table-wrapper').find('.table').prop('id')

                const ids = []

                $(`#${tableId}`)
                    .find('.checkboxes:checked')
                    .each((i, el) => ids.push($(el).val()))

                if (ids.length === 0) {
                    Botble.showError(
                        BotbleVariables.languages.tables.please_select_record
                            ? BotbleVariables.languages.tables.please_select_record
                            : 'Please select at least one record to perform this action!'
                    )

                    return false
                }

                $('.confirm-trigger-bulk-actions-button')
                    .data('href', _self.prop('href'))
                    .data('method', _self.data('method'))
                    .data('table-id', tableId)
                    .data('table-target', _self.data('table-target'))
                    .data('target', _self.data('target'))

                const modal = $('.bulk-action-confirm-modal')

                modal.find('h3').text(_self.data('confirmation-modal-title'))
                modal.find('.modal-body > .text-muted').text(_self.data('confirmation-modal-message'))
                modal.find('button.btn[data-bs-dismiss="modal"]').text(_self.data('confirmation-modal-cancel-button'))
                modal.find('button.confirm-trigger-bulk-actions-button').text(_self.data('confirmation-modal-button'))

                modal.modal('show')
            })

            $(document).on('click', '.confirm-trigger-bulk-actions-button', (event) => {
                event.preventDefault()

                const _self = $(event.currentTarget)

                Botble.showButtonLoading(_self)

                const tableId = _self.data('table-id')
                const method = _self.data('method').toLowerCase() || 'post'

                const $table = $(`#${tableId}`)

                const ids = []

                $table.find('.checkboxes:checked').each((i, el) => ids.push($(el).val()))

                $httpClient
                    .make()
                    [method](_self.data('href'), {
                        ids: ids,
                        bulk_action: 1,
                        bulk_action_table: _self.data('table-target'),
                        bulk_action_target: _self.data('target'),
                    })
                    .then(({ data }) => {
                        Botble.showSuccess(data.message)

                        $table.find('.table-check-all').prop('checked', false).prop('indeterminate', false)

                        window.LaravelDataTables[tableId].draw()

                        _self.closest('.modal').modal('hide')
                    })
                    .finally(() => {
                        Botble.hideButtonLoading(_self)
                    })
            })

            $(document).on('click', '[data-dt-single-action]', (event) => {
                event.preventDefault()

                const _self = $(event.currentTarget)

                const tableId = _self.closest('.table-wrapper').find('.table').prop('id')

                if (_self.data('confirmation-modal')) {
                    $('.confirm-trigger-single-action-button')
                        .data('href', _self.prop('href'))
                        .data('method', _self.data('method'))
                        .data('table-id', tableId)

                    const modal = $('.single-action-confirm-modal')

                    modal.find('.modal-body > h3').text(_self.data('confirmation-modal-title'))
                    modal.find('.modal-body > .text-muted').text(_self.data('confirmation-modal-message'))
                    modal
                        .find('button.btn[data-bs-dismiss="modal"]')
                        .text(_self.data('confirmation-modal-cancel-button'))
                    modal
                        .find('button.confirm-trigger-single-action-button')
                        .text(_self.data('confirmation-modal-button'))

                    modal.modal('show')
                } else {
                    triggerSingleAction(tableId, _self.prop('href'), _self.data('method'))
                }
            })

            $(document).on('click', '.confirm-trigger-single-action-button', (event) => {
                event.preventDefault()

                const _self = $(event.currentTarget)

                Botble.showButtonLoading(_self)

                triggerSingleAction(
                    _self.data('table-id'),
                    _self.data('href'),
                    _self.data('method'),
                    () => {
                        _self.closest('.modal').modal('hide')

                        Botble.hideButtonLoading(_self)
                    },
                    () => {
                        Botble.hideButtonLoading(_self)
                    }
                )
            })

            const triggerSingleAction = (tableId, url, method, onSuccess, onError) => {
                const $table = $(`#${tableId}`)
                const $method = method.toLowerCase() || 'post'

                $httpClient
                    .make()
                    [$method](url)
                    .then(({ data }) => {
                        Botble.showSuccess(data.message)

                        $table.find('.table-check-all').prop('checked', false).prop('indeterminate', false)

                        window.LaravelDataTables[tableId].draw()

                        typeof onSuccess === 'function' && onSuccess.apply(this, data)
                    })
                    .catch((error) => {
                        typeof onError === 'function' && onError.apply(this, error)
                    })
            }

            $(document).on('click', '.bulk-change-item', (event) => {
                event.preventDefault()
                let _self = $(event.currentTarget)

                let table = _self.closest('.table-wrapper').find('.table').prop('id')

                let ids = []
                $('#' + table)
                    .find('.checkboxes:checked')
                    .each((i, el) => {
                        ids[i] = $(el).val()
                    })

                if (ids.length === 0) {
                    Botble.showError(
                        BotbleVariables.languages.tables.please_select_record
                            ? BotbleVariables.languages.tables.please_select_record
                            : 'Please select at least one record to perform this action!'
                    )
                    return false
                }

                that.loadBulkChangeData(_self)

                $('.confirm-bulk-change-button')
                    .data('parent-table', table)
                    .data('class-item', _self.data('class-item'))
                    .data('key', _self.data('key'))
                    .data('url', _self.data('save-url'))
                $('.modal-bulk-change-items').modal('show')
            })

            $(document).on('click', '.confirm-bulk-change-button', (event) => {
                event.preventDefault()
                let _self = $(event.currentTarget)
                let value = _self.closest('.modal').find('.input-value').val()
                let inputKey = _self.data('key')

                let $table = $('#' + _self.data('parent-table'))

                let ids = []
                $table.find('.checkboxes:checked').each((i, el) => {
                    ids[i] = $(el).val()
                })

                Botble.showButtonLoading(_self)

                $httpClient
                    .make()
                    .post(_self.data('url'), {
                        ids: ids,
                        key: inputKey,
                        value: value,
                        class: _self.data('class-item'),
                    })
                    .then(({ data }) => {
                        Botble.showSuccess(data.message)

                        $table.find('.table-check-all').prop('checked', false).prop('indeterminate', false)

                        $.each(ids, (index, item) => {
                            window.LaravelDataTables[_self.data('parent-table')]
                                .row($table.find('.checkboxes[value="' + item + '"]').closest('tr'))
                                .remove()
                                .draw()
                        })
                        _self.closest('.modal').modal('hide')
                    })
                    .finally(() => {
                        Botble.hideButtonLoading(_self)
                    })
            })

            $(document).on('keyup', '.table-search-input input[type=search]', (event) => {
                const $searchInput = $(event.currentTarget)

                setTimeout(() => {
                    const searchValue = $searchInput.val()

                    if (searchValue) {
                        $searchInput.closest('label').find('.search-icon').hide()
                        $searchInput.closest('label').find('.search-reset-icon').show()
                    } else {
                        $searchInput.closest('label').find('.search-icon').show()
                        $searchInput.closest('label').find('.search-reset-icon').hide()
                    }

                    $searchInput.closest('.table-wrapper').find('table').DataTable().search(searchValue).draw()
                }, 200)
            })

            $(document).on('click', '.table-search-input .search-reset-icon', (event) => {
                const $searchInput = $(event.currentTarget).closest('.table-search-input').find('input[type=search]')

                $searchInput.val('')

                $searchInput.closest('.table-wrapper').find('table').DataTable().search('').draw()
            })

            $(document).on('click', '[data-bb-toggle="dt-buttons"]', (event) => {
                const target = $(event.currentTarget)
                const tableId = target.attr('aria-controls')
                const buttonTarget = target.data('bb-target')

                $(`${buttonTarget}[aria-controls="${tableId}"]`).trigger('click')
            })

            $(document).on('click', '[data-bb-toggle="dt-exports"]', (event) => {
                const target = $(event.currentTarget)
                const tableId = target.attr('aria-controls')
                const value = target.data('bb-target')

                const dt = window.LaravelDataTables[tableId]

                let url = dt.ajax.url() || window.location.href
                let params = _buildParams(dt, value)

                _downloadFromUrl(url, params)
            })

            const $columnsVisibleDropdowns = document.querySelectorAll('[data-bb-toggle="dt-columns-visibility-dropdown"]')

            let $formDirty = {}

            if ($columnsVisibleDropdowns.length) {
                for (const $dropdown of $columnsVisibleDropdowns) {
                    $dropdown.addEventListener('hidden.bs.dropdown', function (event) {
                        const target = $(event.currentTarget)
                        const tableId = target.attr('aria-controls')
                        const form = target.find('form[data-bb-toggle="dt-columns-visibility"]')

                        if (! $formDirty[tableId]) {
                            return
                        }

                        $httpClient
                            .make()
                            .putForm(form.prop('action'), new FormData(form[0]))
                            .then(() => {
                                const dt = window.LaravelDataTables[tableId]

                                dt.state.clear()

                                location.reload()

                                $formDirty[tableId] = false
                            })
                    })
                }
            }

            $(document).on('change', '[data-bb-toggle="dt-columns-visibility-toggle"]', (event) => {
                const target = $(event.currentTarget).closest('.dropdown')
                const tableId = target.attr('aria-controls')

                $formDirty[tableId] = true
            })
        }

        loadBulkChangeData($element) {
            let $modal = $('.modal-bulk-change-items')

            $httpClient
                .make()
                .get($modal.find('.confirm-bulk-change-button').data('load-url'), {
                    class: $element.data('class-item'),
                    key: $element.data('key'),
                })
                .then((response) => {
                    let data = $.map(response.data.data, (value, key) => {
                        return { id: key, name: value }
                    })

                    let $parent = $('.modal-bulk-change-content')

                    $parent.html(response.data.html)

                    let $input = $modal.find('input[type=text].input-value')
                    if ($input.length) {
                        $input.typeahead({ source: data })
                        $input.data('typeahead').source = data
                    }

                    Botble.initResources()
                })
        }

        handleActionsExport() {
            $(document).on('click', '.export-data', (event) => {
                let _self = $(event.currentTarget)
                let table = _self.closest('.table-wrapper').find('.table').prop('id')

                let ids = []
                $('#' + table)
                    .find('.checkboxes:checked')
                    .each((i, el) => {
                        ids[i] = $(el).val()
                    })

                event.preventDefault()

                $httpClient
                    .make()
                    .post(_self.prop('href'), {
                        'ids-checked': ids,
                    })
                    .then(({ data }) => {
                        let a = document.createElement('a')
                        a.href = data.file
                        a.download = data.name
                        document.body.appendChild(a)
                        a.trigger('click')
                        a.remove()
                    })
            })
        }
    }

    $.fn.dataTable.Buttons.defaults.dom.container.className = 'dt-buttons btn-group w-100 w-sm-auto'
    $.fn.dataTable.Buttons.defaults.dom.button.className = 'btn'

    $(() => {
        new TableManagement()
    })
})(jQuery, jQuery.fn.dataTable)
