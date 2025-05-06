'use strict'

$(() => {
    $.fn.serializeObject = function () {
        let o = {}
        let a = this.serializeArray()
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]]
                }
                o[this.name].push(this.value || '')
            } else {
                o[this.name] = this.value || ''
            }
        })

        return o
    }

    const $shortcodeListModal = $('#shortcode-list-modal')
    const $shortcodeFormModal = $('#shortcode-modal')

    $('[data-bb-toggle="shortcode-item-radio"]').on('change', () => {
        $('[data-bb-toggle="shortcode-use"]').prop('disabled', false).removeClass('disabled')
    })

    $('[data-bb-toggle="shortcode-add-single"]').on('click', function (event) {
        event.preventDefault()

        let formElement = $('.shortcode-modal').find('.shortcode-data-form')
        let formData = formElement.serializeObject()
        let attributes = ''

        $.each(formData, function (name, value) {
            let element = formElement.find('*[name="' + name + '"]')
            let shortcodeAttribute = element.data('shortcode-attribute')
            if ((!shortcodeAttribute || shortcodeAttribute !== 'content') && value) {
                name = name.replace('[]', '')
                if (value && typeof value === 'string') {
                    value = value.replace(/"([^"]*)"/g, '“$1”')
                    value = value.replace(/"/g, '“')
                }

                if (element.data('shortcode-attribute') !== 'content') {
                    name = name.replace('[]', '')
                    attributes += ' ' + name + '="' + value + '"'
                }
            }
        })

        let content = ''
        let contentElement = formElement.find('*[data-shortcode-attribute=content]')
        if (contentElement != null && contentElement.val() != null && contentElement.val() !== '') {
            content = contentElement.val()
        }

        const $shortCodeKey = $(this).closest('.shortcode-modal').find('.shortcode-input-key').val()

        const editorInstance = $('.add_shortcode_btn_trigger').data('result')

        const shortcode = '[' + $shortCodeKey + attributes + ']' + content + '[/' + $shortCodeKey + ']'

        if (window.EDITOR && window.EDITOR.CKEDITOR && $('.editor-ckeditor').length > 0) {
            window.EDITOR.CKEDITOR[editorInstance].commands.execute('shortcode', shortcode)
        } else if ($('.editor-tinymce').length > 0) {
            tinymce.get(editorInstance).execCommand('mceInsertContent', false, shortcode)
        } else {
            const coreInsertShortCodeEvent = new CustomEvent('core-insert-shortcode', {
                detail: { shortcode: shortcode },
            })
            document.dispatchEvent(coreInsertShortCodeEvent)
        }

        $(this).closest('.modal').modal('hide')
    })

    $(document).on('click', '[data-bb-toggle="shortcode-list-modal"]', () => {
        $shortcodeListModal.modal('show')
    })

    $('[data-bb-toggle="shortcode-select"]').on('dblclick', (event) => {
        const $currentTarget = $(event.currentTarget)

        triggerShortcode($currentTarget)
    })

    $('[data-bb-toggle="shortcode-use"]').on('click', () => {
        const $shortcodeSelected = $shortcodeListModal
            .find('.shortcode-item-input:checked')
            .closest('.shortcode-item-wrapper')

        triggerShortcode($shortcodeSelected)

        $('[data-bb-toggle="shortcode-item-radio"]').prop('checked', false)
        $('[data-bb-toggle="shortcode-use"]').prop('disabled', true).addClass('disabled')
    })

    $('[data-bb-toggle="shortcode-button-use"]').on('click', (event) => {
        const $shortcodeSelected = $(event.currentTarget).closest('.shortcode-item-wrapper')

        triggerShortcode($shortcodeSelected)
    })

    function triggerShortcode(el) {
        shortcodeCallback({
            href: el.attr('href'),
            key: el.data('key'),
            name: el.data('name'),
            description: el.data('description'),
        })
    }

    function shortcodeCallback(params = {}) {
        const { href, key, name, description = null, data = {}, update = false, previewImage = null } = params

        $('.shortcode-admin-config').html('')

        let $addShortcodeButton = $('.shortcode-modal button[data-bb-toggle="shortcode-add-single"]')

        $addShortcodeButton.text($addShortcodeButton.data(update ? 'update-text' : 'add-text'))

        $('.shortcode-modal .modal-title').text(name)

        if (previewImage != null && previewImage !== '') {
            $('.shortcode-modal .shortcode-preview-image-link').attr('href', previewImage).show()
        } else {
            $('.shortcode-modal .shortcode-preview-image-link').hide()
        }

        $('.shortcode-modal').modal('show')

        const $modalLoading = $shortcodeFormModal.find('.modal-content')
        Botble.showLoading($modalLoading)

        $httpClient
            .make()
            .post(href, data)
            .then(({ data }) => {
                $('.shortcode-data-form').trigger('reset')
                $('.shortcode-input-key').val(key)
                $('.shortcode-admin-config').html(data.data)
                Botble.hideLoading($modalLoading)

                Botble.initResources()
                Botble.initMediaIntegrate()
                Botble.initFieldCollapse()

                document.dispatchEvent(new CustomEvent('core-shortcode-config-loaded'))
            })
    }

    $shortcodeFormModal.on('show.bs.modal', () => {
        $shortcodeListModal.modal('hide')
        $('[data-bb-toggle="shortcode-item-radio"]').prop('checked', false)
        $('[data-bb-toggle="shortcode-use"]').prop('disabled', true).addClass('disabled')
    })

    $(document).on('ckeditor-bb-shortcode-callback', (e) => {
        const { shortcode, options } = e.detail

        shortcodeCallback({
            key: shortcode,
            href: options.url,
            previewImage: '',
        })
    })

    $(document).on('ckeditor-bb-shortcode-edit', (e) => {
        const { shortcode, name } = e.detail
        const $shortcodeItem = $(`[data-bb-toggle="shortcode-select"][data-key="${name}"]`)
        const description = $shortcodeItem.length > 0 ? $shortcodeItem.data('description') : ''

        shortcodeCallback({
            key: name,
            href: $shortcodeItem.data('url'),
            data: {
                key: name,
                code: shortcode,
            },
            name: $shortcodeItem.data('name'),
            description: description,
            previewImage: '',
            update: true,
        })
    })

    $('.shortcode-list-modal')
        .on('keyup', 'input[type="search"]', function (e) {
            e.preventDefault()

            const search = $(this).val().toLowerCase()

            $('.shortcode-item-wrapper').each((index, element) => {
                const $element = $(element)
                const name = $element.data('name').toLowerCase()
                const description = $element.data('description').toLowerCase()

                if (name.includes(search) || description.includes(search)) {
                    $element.parent().show()
                } else {
                    $element.parent().hide()
                }
            })

            if ($('.shortcode-item-wrapper:visible').length === 0) {
                $('.shortcode-empty').show()
            } else {
                $('.shortcode-empty').hide()
            }
        })
        .on('click', '[data-bb-toggle="shortcode-clear-search"]', function (e) {
            e.preventDefault()

            $(this)
                .closest('.shortcode-list-modal')
                .find('input[type="search"]')
                .val('')
                .trigger('keyup')
                .trigger('focus')
        })
})
