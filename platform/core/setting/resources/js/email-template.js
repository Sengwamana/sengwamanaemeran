$(() => {
    $('[data-bb-toggle="email-status-toggle"]').on('change', (event) => {
        const _self = $(event.currentTarget)
        const key = _self.prop('name')
        const url = _self.data('change-url')

        _self.closest('tr').find('td.template-name > span').toggleClass('text-muted text-decoration-line-through')

        $httpClient
            .make()
            .post(url, { key: key, value: _self.prop('checked') ? 1 : 0 })
            .then(({ data }) => Botble.showSuccess(data.message))
    })

    $(document).on('click', '[data-bb-toggle="reset-default"]', (event) => {
        event.preventDefault()
        $('#reset-template-to-default-button').data('target', $(event.currentTarget).prop('href'))
        $('#reset-template-to-default-modal').modal('show')
    })

    $(document).on('click', '[data-bb-toggle="twig-variable"]', (event) => {
        event.preventDefault()
        const $this = $(event.currentTarget)
        const doc = $('.CodeMirror')[0].CodeMirror
        const key = '{{ ' + $this.data('key') + ' }}'

        // If there's a selection, replace the selection.
        if (doc.somethingSelected()) {
            doc.replaceSelection(key)
            return
        }

        // Otherwise, we insert at the cursor position.
        const cursor = doc.getCursor()
        const pos = {
            line: cursor.line,
            ch: cursor.ch,
        }
        doc.replaceRange(key, pos)
    })

    $(document).on('click', '[data-bb-toggle="twig-function"]', (event) => {
        event.preventDefault()
        const $this = $(event.currentTarget)

        $this.closest('.twig-template').find('.CodeMirror')

        const CodeMirror = $this.closest('.twig-template').find('.CodeMirror')[0].CodeMirror

        const key = $this.data('sample')

        // If there's a selection, replace the selection.
        if (CodeMirror.somethingSelected()) {
            CodeMirror.replaceSelection(key)
            return
        }

        // Otherwise, we insert at the cursor position.
        const cursor = CodeMirror.getCursor()
        const position = {
            line: cursor.line,
            ch: cursor.ch,
        }
        CodeMirror.replaceRange(key, position)
    })

    $(document).on('click', '#reset-template-to-default-button', (event) => {
        event.preventDefault()
        const _self = $(event.currentTarget)

        Botble.showButtonLoading(_self)

        $httpClient
            .make()
            .post(_self.data('target'), {
                email_subject_key: $('input[name=email_subject_key]').val(),
                module: $('input[name=module]').val(),
                template_file: $('input[name=template_file]').val(),
            })
            .then(({ data }) => {
                Botble.showSuccess(data.message)

                setTimeout(() => {
                    window.location.reload()
                }, 1000)

                $('#reset-template-to-default-modal').modal('hide')
            })
            .finally(() => {
                Botble.hideButtonLoading(_self)
            })
    })
})
