class ContactPluginManagement {
    init() {
        $(document).on('click', '.answer-trigger-button', (event) => {
            event.preventDefault()
            event.stopPropagation()

            const answerWrapper = $('.answer-wrapper')
            if (answerWrapper.is(':visible')) {
                answerWrapper.fadeOut()
            } else {
                answerWrapper.fadeIn()
            }

            window.EDITOR = new EditorManagement().init()
        })

        $(document).on('click', '.answer-send-button', (event) => {
            event.preventDefault()
            event.stopPropagation()

            const _self = $(event.currentTarget)

            Botble.showButtonLoading(_self)

            let message = $('#message').val()
            if (typeof tinymce != 'undefined') {
                message = tinymce.get('message').getContent()
            }

            $httpClient
                .make()
                .post(_self.data('url'), {
                    message,
                })
                .then(({ data }) => {
                    $('.answer-wrapper').fadeOut()

                    if (typeof tinymce != 'undefined') {
                        tinymce.get('message').setContent('')
                    } else {
                        $('#message').val('')
                        const domEditableElement = document.querySelector('.answer-wrapper .ck-editor__editable')
                        if (domEditableElement) {
                            const editorInstance = domEditableElement.ckeditorInstance

                            if (editorInstance) {
                                editorInstance.setData('')
                            }
                        }
                    }

                    Botble.showSuccess(data.message)

                    $('#reply-wrapper').load(window.location.href + ' #reply-wrapper > *')
                })
                .finally(() => {
                    Botble.hideButtonLoading($(event.currentTarget))
                })
        })
    }
}

$(() => {
    new ContactPluginManagement().init()
})
