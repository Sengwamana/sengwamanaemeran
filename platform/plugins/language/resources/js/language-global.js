class LanguageGlobalManagement {
    init() {
        let languageChoiceSelect = $('#post_lang_choice')
        languageChoiceSelect.data('prev', languageChoiceSelect.val())

        $(document).on('change', '#post_lang_choice', (event) => {
            $('.change_to_language_text').text($(event.currentTarget).find('option:selected').text())
            $('#confirm-change-language-modal').modal('show')
        })

        $(document).on('click', '#confirm-change-language-modal .btn-warning.float-start', (event) => {
            event.preventDefault()
            languageChoiceSelect = $('#post_lang_choice')
            languageChoiceSelect.val(languageChoiceSelect.data('prev')).trigger('change')
            $('#confirm-change-language-modal').modal('hide')
        })

        $(document).on('click', '#confirm-change-language-button', (event) => {
            event.preventDefault()
            let _self = $(event.currentTarget)
            let flagPath = $('#language_flag_path').val()

            Botble.showButtonLoading(_self)
            languageChoiceSelect = $('#post_lang_choice')

            $httpClient
                .make()
                .post($('div[data-change-language-route]').data('change-language-route'), {
                    lang_meta_current_language: languageChoiceSelect.val(),
                    reference_id: $('#reference_id').val(),
                    reference_type: $('#reference_type').val(),
                    lang_meta_created_from: $('#lang_meta_created_from').val(),
                })
                .then(({ data }) => {
                    $('#select-post-language img').replaceWith(
                        `<img src="${flagPath}${languageChoiceSelect
                            .find('option:selected')
                            .data('flag')}.svg" class="flag" style="height: 24px" title="${languageChoiceSelect
                            .find('option:selected')
                            .text()}" alt="${languageChoiceSelect.find('option:selected').text()}" />`
                    )
                    if (!data.error) {
                        $('.current_language_text').text(languageChoiceSelect.find('option:selected').text())
                        let html = ''
                        $.each(data.data, (index, el) => {
                            const flag = `<img src="${flagPath}${el.lang_flag}.svg" class="flag" style="height: 16px" title="${el.lang_name}" alt="${el.lang_name}">`
                            if (el.reference_id) {
                                html += `<a href="${$(
                                    '#route_edit'
                                ).val()}" class="gap-2 d-flex align-items-center text-decoration-none">${flag}
                                        <span>
                                            ${el.lang_name}
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"></path>
                                                <path d="M16 5l3 3"></path>
                                            </svg>
                                    </span>
                                </a>`
                            } else {
                                html += `<a href="${$('#route_create').val()}?ref_from=${$(
                                    `#content_id`
                                ).val()}&ref_lang=${index}" class="gap-2 d-flex align-items-center text-decoration-none">${flag}
                                    <span>
                                        ${el.lang_name}
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 5l0 14"></path>
                                            <path d="M5 12l14 0"></path>
                                        </svg>
                                    </span>
                                </a>`
                            }
                        })

                        $('#list-others-language').html(html)
                        $('#confirm-change-language-modal').modal('hide')
                        languageChoiceSelect.data('prev', languageChoiceSelect.val()).trigger('change')
                    }
                })
                .finally(() => Botble.hideButtonLoading(_self))
        })

        $(document).on('click', '.change-data-language-item', (event) => {
            event.preventDefault()
            window.location.href = $(event.currentTarget).find('span[data-href]').data('href')
        })
    }
}

$(() => {
    new LanguageGlobalManagement().init()

    $httpClient.setup(function (request) {
        request.axios.interceptors.request.use(function (config) {
            const refFrom = $('meta[name="ref_from"]').attr('content')
            const refLang = $('meta[name="ref_lang"]').attr('content')

            if (!refFrom && !refLang) {
                return config
            }

            if (config.data instanceof FormData) {
                config.data.set('ref_from', refFrom)
                config.data.set('ref_lang', refLang)
            } else if (typeof config.data === 'object') {
                config.data.ref_from = refFrom
                config.data.ref_lang = refLang
            }

            return config
        })
    })
})
