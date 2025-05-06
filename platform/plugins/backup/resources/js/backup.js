class BackupManagement {
    init() {
        let backupTable = $('#table-backups')
        backupTable.on('click', '.deleteDialog', (event) => {
            event.preventDefault()

            $('.delete-crud-entry').data('section', $(event.currentTarget).data('section'))
            $('.modal-confirm-delete').modal('show')
        })

        backupTable.on('click', '.restoreBackup', (event) => {
            event.preventDefault()
            $('#restore-backup-button').data('section', $(event.currentTarget).data('section'))
            $('#restore-backup-modal').modal('show')
        })

        $('.delete-crud-entry').on('click', (event) => {
            event.preventDefault()
            $('.modal-confirm-delete').modal('hide')

            let deleteURL = $(event.currentTarget).data('section')

            $httpClient
                .make()
                .delete(deleteURL)
                .then(({ data }) => {
                    if (backupTable.find('tbody tr').length <= 1) {
                        backupTable.load(window.location.href + ' #table-backups > *')
                    }

                    backupTable.find(`button[data-section="${deleteURL}"]`).closest('tr').remove()

                    Botble.showSuccess(data.message)
                })
        })

        $('#restore-backup-button').on('click', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)
            Botble.showButtonLoading(_self)

            $httpClient
                .make()
                .get(_self.data('section'))
                .then(({ data }) => {
                    _self.closest('.modal').modal('hide')
                    Botble.showSuccess(data.message)
                    window.location.reload()
                })
                .finally(() => {
                    Botble.hideButtonLoading(_self)
                })
        })

        $(document).on('click', '#generate_backup', (event) => {
            event.preventDefault()
            $('#name').val('')
            $('#description').val('')
            $('#create-backup-modal').modal('show')
        })

        $('#create-backup-modal').on('click', '#create-backup-button', (event) => {
            event.preventDefault()

            const _self = $(event.currentTarget)
            const $form = _self.closest('form')

            $httpClient
                .make()
                .withButtonLoading(_self)
                .post($form.prop('action'), new FormData($form[0]))
                .then(({ data }) => {
                    backupTable.find('.no-backup-row').remove()
                    backupTable.find('tbody').append(data.data)
                    Botble.showSuccess(data.message)
                })
                .finally(() => {
                    _self.closest('.modal').modal('hide')
                })
        })
    }
}

$(() => {
    new BackupManagement().init()
})
