class LanguageManagement {
    init() {
        Botble.select($('.select-search-language'), {
            templateResult: LanguageManagement.formatState,
            templateSelection: LanguageManagement.formatState,
        })

        let languageTable = $('.table-language')

        $(document).on('change', '#language_id', (event) => {
            let language = $(event.currentTarget).find('option:selected').data('language')
            if (typeof language != 'undefined' && language.length > 0) {
                $('#lang_name').val(language[2]).trigger('change')
                $('#lang_locale').val(language[0]).trigger('change')
                $('#lang_code').val(language[1]).trigger('change')
                $(`input[name=lang_rtl][value="${language[3] === 'rtl' ? 1 : 0}"]`).prop('checked', true)
                $('#flag_list').val(language[4]).trigger('change')
                $('#btn-language-submit-edit')
                    .prop('id', 'btn-language-submit')
                    .text($('#btn-language-submit').data('add-language-text'))
            }
        })

        $(document).on('click', '#btn-language-submit', (event) => {
            event.preventDefault()
            let name = $('#lang_name').val()
            let locale = $('#lang_locale').val()
            let code = $('#lang_code').val()
            let flag = $('#flag_list').val()
            let order = $('#lang_order').val()
            let isRTL = $('input[name=lang_rtl]:checked').val()
            LanguageManagement.createOrUpdateLanguage(0, name, locale, code, flag, order, isRTL, 0)
        })

        $(document).on('click', '#btn-language-submit-edit', (event) => {
            event.preventDefault()
            let id = $('#lang_id').val()
            let name = $('#lang_name').val()
            let locale = $('#lang_locale').val()
            let code = $('#lang_code').val()
            let flag = $('#flag_list').val()
            let order = $('#lang_order').val()
            let isRTL = $('input[name=lang_rtl]:checked').val()

            const $button = $(event.currentTarget)

            LanguageManagement.createOrUpdateLanguage(id, name, locale, code, flag, order, isRTL, 1, $button)
        })

        languageTable.on('click', '.deleteDialog', (event) => {
            event.preventDefault()

            $('.delete-crud-entry').data('section', $(event.currentTarget).data('section'))
            $('.modal-confirm-delete').modal('show')
        })

        $('.delete-crud-entry').on('click', (event) => {
            event.preventDefault()
            $('.modal-confirm-delete').modal('hide')

            let deleteURL = $(event.currentTarget).data('section')
            Botble.showButtonLoading($(this))

            $httpClient
                .make()
                .delete(deleteURL)
                .then(({ data }) => {
                    if (data.data) {
                        languageTable.find(`i[data-id=${data.data}]`).unwrap()
                        $('.tooltip').remove()
                    }
                    languageTable.find(`button[data-section="${deleteURL}"]`).closest('tr').remove()
                    Botble.showSuccess(data.message)
                })
                .finally(() => {
                    Botble.hideButtonLoading($(this))
                })
        })

        languageTable.on('click', '.set-language-default', (event) => {
            event.preventDefault()
            const _self = $(event.currentTarget)

            $httpClient
                .make()
                .get(_self.data('section'))
                .then(({ data }) => {
                    const icon = languageTable.find('td > svg')

                    icon.closest('td svg').removeClass('text-yellow')

                    icon.replaceWith(
                        `<a href="javascript:void(0);" data-section="${route(
                            'languages.set.default'
                        )}?lang_id=${icon.data(
                            'id'
                        )}" class="set-language-default text-decoration-none" data-bs-toggle="tooltip" data-bs-original-title="Choose ${icon.data(
                            'name'
                        )} as default language">${icon.closest('td').html()}</a>`
                    )
                    _self.find('svg').unwrap().addClass('text-yellow')

                    $('.tooltip').remove()

                    Botble.showSuccess(data.message)
                })
        })

        languageTable.on('click', '.edit-language-button', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)

            $httpClient
                .make()
                .get(_self.data('url'))
                .then(({ data }) => {
                    let language = data.data

                    $('#lang_id').val(language.lang_id)
                    $('#lang_name').val(language.lang_name)
                    $('#lang_locale').val(language.lang_locale).trigger('change')
                    $('#lang_code').val(language.lang_code).trigger('change')
                    $('#flag_list').val(language.lang_flag).trigger('change')
                    $(`input[name=lang_rtl][value="${language.lang_is_rtl ? 1 : 0}"]`).prop('checked', true)
                    $('#lang_order').val(language.lang_order)

                    $('#btn-language-submit')
                        .prop('id', 'btn-language-submit-edit')
                        .text($('#btn-language-submit-edit').data('update-language-text'))
                })
        })

        $(document).on('submit', 'form.language-settings-form', (event) => {
            event.preventDefault()

            const form = $(event.currentTarget)
            const button = form.find('button[type=submit]')

            Botble.showButtonLoading(button)

            $httpClient
                .make()
                .postForm(form.prop('action'), new FormData(form[0]))
                .then(({ data }) => {
                    Botble.showSuccess(data.message)
                    form.removeClass('dirty')
                })
                .finally(() => {
                    Botble.hideButtonLoading(button)
                })
        })
    }

    static formatState(state) {
        if (!state.id || state.element.value.toLowerCase().includes('...')) {
            return state.text
        }

        return $(
            `<div>
                <span class="dropdown-item-indicator">
                    <img src="${$(
                        '#language_flag_path'
                    ).val()}${state.element.value.toLowerCase()}.svg" class="flag" style="height: 16px;" alt="${
                        state.text
                    }">
                </span>
                <span>${state.text}</span>
            </div
        `
        )
    }

    static createOrUpdateLanguage(id, name, locale, code, flag, order, isRTL, edit, button = null) {
        let $buttonSubmit = $('#btn-language-submit')

        if (button) {
            $buttonSubmit = button;
        }

        let url = $buttonSubmit.data('store-url')

        if (edit) {
            url = $('#btn-language-submit-edit').data('update-url') + `?lang_code=${code}`
        }

        Botble.showButtonLoading($buttonSubmit, true)

        $httpClient
            .make()
            .post(url, {
                lang_id: id.toString(),
                lang_name: name,
                lang_locale: locale,
                lang_code: code,
                lang_flag: flag,
                lang_order: order,
                lang_is_rtl: isRTL,
            })
            .then(({ data }) => {
                if (edit) {
                    $('.table-language')
                        .find('tr[data-id=' + id + ']')
                        .replaceWith(data.data)
                } else {
                    $('.table-language').append(data.data)
                }
                Botble.showSuccess(data.message)
            })
            .finally(() => {
                $('#language_id').val('').trigger('change')
                $('#lang_name').val('')
                $('#lang_locale').val('').trigger('change')
                $('#lang_code').val('').trigger('change')
                $('input[name=lang_rtl][value="0"]').prop('checked', true)
                $('#flag_list').val('').trigger('change')

                $('#btn-language-submit-edit')
                    .prop('id', 'btn-language-submit')
                    .text($('#btn-language-submit').data('add-language-text'))
                Botble.hideButtonLoading($buttonSubmit)
            })
    }
}

$(() => {
    new LanguageManagement().init()
})
