import CKEditorUploadAdapter from './ckeditor-upload-adapter'

class EditorManagement {
    constructor() {
        this.CKEDITOR = {}
        this.ckEditorConfigCallbacks = []
        this.ckEditorInitialCallbacks = []
        this.ckFinderCallback = null
        this.tinyMceConfigCallbacks = []
        this.tinyMceInitialCallbacks = []

        document.dispatchEvent(new CustomEvent('core-editor-init', { detail: this }))
    }

    ckEditorConfigUsing(callback) {
        this.ckEditorConfigCallbacks.push(callback)
    }

    ckEditorInitialUsing(callback) {
        this.ckEditorInitialCallbacks.push(callback)
    }

    ckEditorConfig(config) {
        for (let callback of this.ckEditorConfigCallbacks) {
            config = callback(config)
        }

        return config
    }

    ckFinderUsing(callback) {
        this.ckFinderCallback = callback
    }

    async ckFinderInitial(editor, element) {
        if (this.ckFinderCallback) {
            return this.ckFinderCallback(editor, element)
        }

        const ckFileRepository = editor.plugins.get('FileRepository')

        if (ckFileRepository && typeof RV_MEDIA_URL != 'undefined' && RV_MEDIA_URL.media_upload_from_editor) {
            ckFileRepository.createUploadAdapter = (loader) => {
                return new CKEditorUploadAdapter(loader, RV_MEDIA_URL.media_upload_from_editor, editor.t)
            }
        }

        const ckfinder = editor.commands.get('ckfinder')
        const btnGalleries = $(`#${element}`).parent().find('.btn_gallery[data-action="media-insert-ckeditor"]')

        if (ckfinder && btnGalleries.length) {
            ckfinder.execute = () => btnGalleries.trigger('click')
        } else {
            ckfinder.execute = () => Botble.showError('Not available.')
        }
    }
    initCkEditor(element, extraConfig) {
        if (this.CKEDITOR[element] || !$('#' + element).is(':visible')) {
            return false
        }

        const editor = document.querySelector('#' + element)

        let config = {
            fontSize: {
                options: [9, 10, 11, 12, 13, 'default', 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
            },

            alignment: {
                options: ['left', 'right', 'center', 'justify'],
            },

            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading4' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading4' },
                ],
            },
            placeholder: ' ',
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'fontColor',
                    'fontSize',
                    'fontBackgroundColor',
                    'fontFamily',
                    'bold',
                    'italic',
                    'underline',
                    'link',
                    'strikethrough',
                    'bulletedList',
                    'numberedList',
                    '|',
                    'alignment',
                    'direction',
                    'shortcode',
                    'outdent',
                    'indent',
                    '|',
                    'htmlEmbed',
                    'imageInsert',
                    'ckfinder',
                    'blockQuote',
                    'insertTable',
                    'mediaEmbed',
                    'undo',
                    'redo',
                    'findAndReplace',
                    'removeFormat',
                    'sourceEditing',
                    'codeBlock',
                    'fullScreen',
                ],
                shouldNotGroupWhenFull: true,
            },
            language: {
                ui: window.siteEditorLocale || 'en',

                content: window.siteEditorLocale || 'en',
            },
            image: {
                toolbar: [
                    'imageTextAlternative',
                    'imageStyle:inline',
                    'imageStyle:block',
                    'imageStyle:side',
                    'imageStyle:wrapText',
                    'imageStyle:breakText',
                    'toggleImageCaption',
                    'ImageResize',
                ],
                upload: {
                    types: ['jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff', 'svg+xml'],
                },
            },
            codeBlock: {
                languages: [
                    { language: 'plaintext', label: 'Plain text' },
                    { language: 'c', label: 'C' },
                    { language: 'cs', label: 'C#' },
                    { language: 'cpp', label: 'C++' },
                    { language: 'css', label: 'CSS' },
                    { language: 'diff', label: 'Diff' },
                    { language: 'html', label: 'HTML' },
                    { language: 'java', label: 'Java' },
                    { language: 'javascript', label: 'JavaScript' },
                    { language: 'php', label: 'PHP' },
                    { language: 'python', label: 'Python' },
                    { language: 'ruby', label: 'Ruby' },
                    { language: 'typescript', label: 'TypeScript' },
                    { language: 'xml', label: 'XML' },
                    { language: 'dart', label: 'Dart', class: 'language-dart' },
                ],
            },
            link: {
                defaultProtocol: 'http://',
                decorators: {
                    openInNewTab: {
                        mode: 'manual',
                        label: 'Open in a new tab',
                        attributes: {
                            target: '_blank',
                            rel: 'noopener noreferrer',
                        },
                    },
                },
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells',
                    'tableCellProperties',
                    'tableProperties',
                ],
            },
            htmlSupport: {
                allow: [
                    {
                        name: /.*/,
                        attributes: true,
                        classes: true,
                        styles: true,
                    },
                ],
            },
            mediaEmbed: {
                extraProviders: [
                    {
                        name: 'tiktok',
                        url: '^.*https:\\/\\/(?:m|www|vm)?\\.?tiktok\\.com\\/((?:.*\\b(?:(?:usr|v|embed|user|video)\\/|\\?shareId=|\\&item_id=)(\\d+))|\\w+)',
                        html: (match) => {
                            return `<iframe src="https://www.tiktok.com/embed/v2/${match[1]}" width="100%" height="400" frameborder="0"></iframe>`
                        },
                    },
                ],
            },
            ...extraConfig,
        }

        config = this.ckEditorConfig(config)

        ClassicEditor.create(editor, config)
            .then(async (editor) => {
                // create function insert html
                editor.insertHtml = (html) => {
                    const viewFragment = editor.data.processor.toView(html)
                    const modelFragment = editor.data.toModel(viewFragment)
                    editor.model.insertContent(modelFragment)
                }

                window.editor = editor

                this.CKEDITOR[element] = editor

                const minHeight = $('#' + element).prop('rows') * 90
                const className = `ckeditor-${element}-inline`

                $(editor.ui.view.editable.element).addClass(className).after(`
                    <style>
                        .ck-editor__editable_inline {
                            min-height: ${minHeight - 100}px;
                            max-height: ${minHeight + 100}px;
                        }
                    </style>
                `)

                // debounce content for ajax ne
                let timeout
                editor.model.document.on('change:data', () => {
                    clearTimeout(timeout)
                    timeout = setTimeout(() => {
                        editor.updateSourceElement()
                    }, 150)
                })

                // insert media embed
                editor.commands._commands.get('mediaEmbed').execute = (url) => {
                    editor.execute('shortcode', `[media url="${url}"][/media]`)
                }

                await this.ckEditorInitialUsing(editor)

                await this.ckFinderInitial(editor, element)
            })
            .catch((error) => {
                console.error(error)
            })
    }

    uploadImageFromEditor(blobInfo, callback) {
        let formData = new FormData()
        if (typeof blobInfo.blob === 'function') {
            formData.append('upload', blobInfo.blob(), blobInfo.filename())
        } else {
            formData.append('upload', blobInfo)
        }

        $httpClient
            .make()
            .postForm(RV_MEDIA_URL.media_upload_from_editor, formData)
            .then(({ data }) => {
                if (data.uploaded) {
                    callback(data.url)
                }
            })
    }

    tinyMceConfigUsing(callback) {
        this.tinyMceConfigCallbacks.push(callback)
    }

    tinyMceInitialUsing(callback) {
        this.tinyMceInitialCallbacks.push(callback)
    }

    tinyMceConfig(config) {
        for (let callback of this.tinyMceConfigCallbacks) {
            config = callback(config)
        }

        return config
    }

    async tinyMceInitial(editor) {
        return editor
    }

    async initTinyMce(element) {
        let options = {
            menubar: true,
            selector: `#${element}`,
            min_height: $(`#${element}`).prop('rows') * 110,
            resize: 'vertical',
            plugins:
                'code autolink advlist visualchars link image media table charmap hr pagebreak nonbreaking anchor insertdatetime lists wordcount imagetools visualblocks',
            extended_valid_elements: 'input[id|name|value|type|class|style|required|placeholder|autocomplete|onclick]',
            toolbar:
                'formatselect | bold italic strikethrough forecolor backcolor | link image table | alignleft aligncenter alignright alignjustify  | numlist bullist indent  |  visualblocks code',
            convert_urls: false,
            image_caption: true,
            image_advtab: true,
            image_title: true,
            placeholder: '',
            contextmenu: 'link image inserttable | cell row column deletetable',
            images_upload_url: RV_MEDIA_URL.media_upload_from_editor,
            automatic_uploads: true,
            block_unsupported_drop: false,
            file_picker_types: 'file image media',
            images_upload_handler: this.uploadImageFromEditor.bind(this),
            file_picker_callback: (callback) => {
                let $input = $('<input type="file" accept="image/*" />').click()

                $input.on('change', (e) => {
                    this.uploadImageFromEditor(e.target.files[0], callback)
                })
            },
            directionality: $('body').prop('dir') || 'ltr',
        }

        if (localStorage.getItem('themeMode') === 'dark') {
            options.skin = 'oxide-dark'
            options.content_css = 'dark'
        }

        options = this.tinyMceConfig(options)

        const tinymceInstance = tinymce.init(options)

        await this.tinyMceInitial(tinymceInstance)
    }

    initEditor(element, extraConfig, type) {
        if (!element.length) {
            return false
        }

        let current = this
        switch (type) {
            case 'ckeditor':
                $.each(element, (index, item) => {
                    current.initCkEditor($(item).prop('id'), extraConfig)
                })
                break
            case 'tinymce':
                $.each(element, (index, item) => {
                    current.initTinyMce($(item).prop('id'))
                })
                break
        }
    }

    init() {
        let $ckEditor = $(document).find('.editor-ckeditor')
        let $tinyMce = $(document).find('.editor-tinymce')
        let current = this
        if ($ckEditor.length > 0) {
            current.initEditor($ckEditor, {}, 'ckeditor')
        }

        if ($tinyMce.length > 0) {
            current.initEditor($tinyMce, {}, 'tinymce')
        }

        $(document).off('click', '.show-hide-editor-btn').on('click', '.show-hide-editor-btn', (event) => {
            event.preventDefault()
            const editorInstance = $(event.currentTarget).data('result')

            let $result = $('#' + editorInstance)

            if ($result.hasClass('editor-ckeditor')) {
                const $editorActionItem = $('.editor-action-item')
                if (this.CKEDITOR[editorInstance] && typeof this.CKEDITOR[editorInstance] !== 'undefined') {
                    this.CKEDITOR[editorInstance].destroy()
                    this.CKEDITOR[editorInstance] = null
                    $editorActionItem.not('.action-show-hide-editor').hide()
                } else {
                    current.initCkEditor(editorInstance, {}, 'ckeditor')
                    $editorActionItem.not('.action-show-hide-editor').show()
                }
            } else if ($result.hasClass('editor-tinymce')) {
                tinymce.execCommand('mceToggleEditor', false, editorInstance)
            }
        })

        return this
    }
}

$(() => {
    window.EDITOR = new EditorManagement().init()
    window.EditorManagement = window.EditorManagement || EditorManagement

    $(document).on('shown.bs.modal', function () {
        window.EDITOR.init()
    })
})
