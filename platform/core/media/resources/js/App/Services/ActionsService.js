import Cropper from 'cropperjs'
import { RecentItems } from '../Config/MediaConfig'
import { Helpers } from '../Helpers/Helpers'
import { MessageService } from './MessageService'

export class ActionsService {
    static handleDropdown() {
        let selected = Helpers.size(Helpers.getSelectedItems())

        ActionsService.renderActions()

        if (selected > 0) {
            $('.rv-dropdown-actions > .dropdown-toggle').removeClass('disabled').prop('disabled', false)
        } else {
            $('.rv-dropdown-actions > .dropdown-toggle').addClass('disabled').prop('disabled', true)
        }
    }

    static handlePreview() {
        let selected = []

        Helpers.each(Helpers.getSelectedFiles(), (value) => {
            if (value.preview_url) {
                if (value.type === 'document') {
                    const iframe = document.createElement('iframe')
                    iframe.src = value.preview_url
                    iframe.allowFullscreen = true
                    iframe.style.width = '100vh'
                    iframe.style.height = '100vh'
                    selected.push(iframe)
                } else {
                    selected.push(value.preview_url)
                }

                RecentItems.push(value.id)
            }
        })

        if (Helpers.size(selected) > 0) {
            Botble.lightbox(selected)
            Helpers.storeRecentItems()
        } else {
            this.handleGlobalAction('download')
        }
    }

    static renderCropImage() {
        const html = $('#rv_media_crop_image').html()
        const modal = $('#modal_crop_image .crop-image').empty()
        const item = Helpers.getSelectedItems()[0]
        const form = $('#modal_crop_image .form-crop')
        let cropData

        const el = html.replace(/__src__/gi, item.full_url)
        modal.append(el)

        const image = modal.find('img')[0]

        const options = {
            minContainerWidth: 500,
            minContainerHeight: 550,
            dragMode: 'move',
            crop(event) {
                cropData = event.detail
                form.find('input[name="image_id"]').val(item.id)
                form.find('input[name="crop_data"]').val(JSON.stringify(cropData))
                setHeight(cropData.height)
                setWidth(cropData.width)
            },
        }
        let cropper = new Cropper(image, options)

        form.find('#aspectRatio').on('click', function () {
            cropper.destroy()
            if ($(this).is(':checked')) {
                options.aspectRatio = cropData.width / cropData.height
            } else {
                options.aspectRatio = null
            }
            cropper = new Cropper(image, options)
        })

        form.find('#dataHeight').on('change', function () {
            cropData.height = parseFloat($(this).val())
            cropper.setData(cropData)
            setHeight(cropData.height)
        })

        form.find('#dataWidth').on('change', function () {
            cropData.width = parseFloat($(this).val())
            cropper.setData(cropData)
            setWidth(cropData.width)
        })

        const setHeight = (height) => {
            form.find('#dataHeight').val(parseInt(height))
        }

        const setWidth = (width) => {
            form.find('#dataWidth').val(parseInt(width))
        }
    }

    static async handleCopyLink() {
        let links = ''

        Helpers.each(Helpers.getSelectedFiles(), (value) => {
            if (!Helpers.isEmpty(links)) {
                links += '\n'
            }
            links += value.full_url
        })

        await Botble.copyToClipboard(links)

        MessageService.showMessage(
            'success',
            Helpers.trans('clipboard.success'),
            Helpers.trans('message.success_header')
        )
    }

    static async handleCopyIndirectLink() {
        let links = ''

        Helpers.each(Helpers.getSelectedFiles(), (value) => {
            if (!Helpers.isEmpty(links)) {
                links += '\n'
            }

            links += value.indirect_url
        })

        await Botble.copyToClipboard(links)

        MessageService.showMessage(
            'success',
            Helpers.trans('clipboard.success'),
            Helpers.trans('message.success_header')
        )
    }

    static handleShare() {
        $('#modal_share_items').modal('show').find('form.form-alt-text').data('action', type)
    }

    static handleGlobalAction(type, callback) {
        let selected = []
        Helpers.each(Helpers.getSelectedItems(), (value) => {
            selected.push({
                is_folder: value.is_folder,
                id: value.id,
                full_url: value.full_url,
            })
        })

        switch (type) {
            case 'rename':
                $('#modal_rename_items').modal('show').find('form.form-rename').data('action', type)
                break
            case 'copy_link':
                ActionsService.handleCopyLink().then(() => {})
                break
            case 'copy_indirect_link':
                ActionsService.handleCopyIndirectLink().then(() => {})
                break
            case 'share':
                $('#modal_share_items').modal('show')
                break
            case 'preview':
                ActionsService.handlePreview()
                break
            case 'alt_text':
                $('#modal_alt_text_items').modal('show').find('form.form-alt-text').data('action', type)
                break
            case 'crop':
                $('#modal_crop_image').modal('show').find('form.rv-form').data('action', type)
                break
            case 'trash':
                $('#modal_trash_items').modal('show').find('form.form-delete-items').data('action', type)
                break
            case 'delete':
                $('#modal_delete_items').modal('show').find('form.form-delete-items').data('action', type)
                break
            case 'empty_trash':
                $('#modal_empty_trash').modal('show').find('form.form-empty-trash').data('action', type)
                break
            case 'download':
                let files = []
                Helpers.each(Helpers.getSelectedItems(), (value) => {
                    if (!Helpers.inArray(Helpers.getConfigs().denied_download, value.mime_type)) {
                        files.push({
                            id: value.id,
                            is_folder: value.is_folder,
                        })
                    }
                })

                if (files.length) {
                    ActionsService.handleDownload(files)
                } else {
                    MessageService.showMessage(
                        'error',
                        Helpers.trans('download.error'),
                        Helpers.trans('message.error_header')
                    )
                }
                break
            case 'properties':
                $('#modal-properties').modal('show')

                break
            default:
                ActionsService.processAction(
                    {
                        selected: selected,
                        action: type,
                    },
                    callback
                )
                break
        }
    }

    static processAction(data, callback = null) {
        Helpers.showAjaxLoading()

        $httpClient
            .make()
            .post(RV_MEDIA_URL.global_actions, data)
            .then(({ data }) => {
                Helpers.resetPagination()

                MessageService.showMessage('success', data.message, Helpers.trans('message.success_header'))

                callback && callback(data)
            })
            .catch(({ response }) => callback && callback(response.data))
            .finally(() => Helpers.hideAjaxLoading())
    }

    static renderRenameItems() {
        let VIEW = $('#rv_media_rename_item').html()
        let $itemsWrapper = $('#modal_rename_items .rename-items').empty()

        Helpers.each(Helpers.getSelectedItems(), (value) => {
            let item = VIEW.replace(
                /__icon__/gi,
                value.icon ||
                    `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"></path>
                </svg>`
            )
                .replace(/__placeholder__/gi, 'Input file name')
                .replace(/__value__/gi, value.name)

            let $item = $(item)
            $item.data('id', value.id.toString())
            $item.data('is_folder', value.is_folder)
            $item.data('name', value.name)

            const $renamePhysicalFile = $item.find('input[name="rename_physical_file"]')

            $renamePhysicalFile
                .closest('.form-check')
                .find('span')
                .text(
                    value.is_folder ? $renamePhysicalFile.data('folder-label') : $renamePhysicalFile.data('file-label')
                )

            $item.find('input[name="rename_physical_file"]').on('change', function () {
                $item.data('rename_physical_file', $(this).is(':checked'))
            })

            $itemsWrapper.append($item)

            Botble.initFieldCollapse()
        })
    }

    static renderAltTextItems() {
        let VIEW = $('#rv_media_alt_text_item').html()
        let $itemsWrapper = $('#modal_alt_text_items .alt-text-items').empty()

        Helpers.each(Helpers.getSelectedItems(), (value) => {
            let item = VIEW.replace(
                /__icon__/gi,
                value.icon ||
                    `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"></path>
                </svg>`
            )
                .replace(/__placeholder__/gi, 'Input file alt')
                .replace(/__value__/gi, value.alt === null ? '' : value.alt)

            let $item = $(item)
            $item.data('id', value.id)
            $item.data('alt', value.alt)
            $itemsWrapper.append($item)
        })
    }

    static renderShareItems() {
        const target = $('#modal_share_items [data-bb-value="share-result"]')
        const shareType =  $('#modal_share_items select[data-bb-value="share-type"]').val()
        target.val('')

        let results = []

        Helpers.each(Helpers.getSelectedItems(), (value) => {
            switch (shareType) {
                case 'html':
                    results.push(
                        value.type === 'image'
                            ? `<img src="${value.full_url}" alt="${value.alt}" />`
                            : `<a href="${value.full_url}" target="_blank">${value.alt}</a>`
                    )
                    break;
                case 'markdown':
                    results.push(
                        (value.type === 'image' ? '!' : '') +
                        `[${value.alt}](${value.full_url})`
                    )
                    break;
                case 'indirect_url':
                    results.push(value.indirect_url)
                    break;
                default:
                    results.push(value.full_url)
            }
        })

        target.val(results.join('\n'))
    }

    static renderActions() {
        let hasFolderSelected = Helpers.getSelectedFolder().length > 0

        let ACTION_TEMPLATE = $('#rv_action_item').html()
        let initializedItem = 0
        let $dropdownActions = $('.rv-dropdown-actions .dropdown-menu')
        $dropdownActions.empty()

        let actionsList = $.extend({}, true, Helpers.getConfigs().actions_list)

        if (hasFolderSelected) {
            const ignoreActions = ['preview', 'crop', 'alt_text', 'copy_link', 'copy_direct_link', 'share']

            actionsList.basic = Helpers.arrayReject(
                actionsList.basic, (item) => ignoreActions.includes(item.action)
            )

            if (!Helpers.hasPermission('folders.create')) {
                actionsList.file = Helpers.arrayReject(actionsList.file, (item) => {
                    return item.action === 'make_copy'
                })
            }

            if (!Helpers.hasPermission('folders.edit')) {
                actionsList.file = Helpers.arrayReject(actionsList.file, (item) => {
                    return Helpers.inArray(['rename'], item.action)
                })

                actionsList.user = Helpers.arrayReject(actionsList.user, (item) => {
                    return Helpers.inArray(['rename'], item.action)
                })
            }

            if (!Helpers.hasPermission('folders.trash')) {
                actionsList.other = Helpers.arrayReject(actionsList.other, (item) => {
                    return Helpers.inArray(['trash', 'restore'], item.action)
                })
            }

            if (!Helpers.hasPermission('folders.destroy')) {
                actionsList.other = Helpers.arrayReject(actionsList.other, (item) => {
                    return Helpers.inArray(['delete'], item.action)
                })
            }

            if (!Helpers.hasPermission('folders.favorite')) {
                actionsList.other = Helpers.arrayReject(actionsList.other, (item) => {
                    return Helpers.inArray(['favorite', 'remove_favorite'], item.action)
                })
            }
        }

        let selectedFiles = Helpers.getSelectedFiles()

        let canPreview = Helpers.arrayFilter(selectedFiles, function (value) {
            return value.preview_url
        }).length

        if (!canPreview) {
            actionsList.basic = Helpers.arrayReject(actionsList.basic, (item) => {
                return item.action === 'preview'
            })
        }

        let fileIsImage = Helpers.arrayFilter(selectedFiles, function (value) {
            return value.type === 'image'
        }).length

        if (!fileIsImage) {
            actionsList.basic = Helpers.arrayReject(actionsList.basic, (item) => {
                return item.action === 'crop'
            })

            actionsList.file = Helpers.arrayReject(actionsList.file, (item) => {
                return item.action === 'alt_text'
            })
        }

        if (selectedFiles.length > 0) {
            if (!Helpers.hasPermission('files.create')) {
                actionsList.file = Helpers.arrayReject(actionsList.file, (item) => {
                    return item.action === 'make_copy'
                })
            }

            if (!Helpers.hasPermission('files.edit')) {
                actionsList.file = Helpers.arrayReject(actionsList.file, (item) => {
                    return Helpers.inArray(['rename'], item.action)
                })
            }

            if (!Helpers.hasPermission('files.trash')) {
                actionsList.other = Helpers.arrayReject(actionsList.other, (item) => {
                    return Helpers.inArray(['trash', 'restore'], item.action)
                })
            }

            if (!Helpers.hasPermission('files.destroy')) {
                actionsList.other = Helpers.arrayReject(actionsList.other, (item) => {
                    return Helpers.inArray(['delete'], item.action)
                })
            }

            if (!Helpers.hasPermission('files.favorite')) {
                actionsList.other = Helpers.arrayReject(actionsList.other, (item) => {
                    return Helpers.inArray(['favorite', 'remove_favorite'], item.action)
                })
            }

            if (selectedFiles.length > 1) {
                actionsList.basic = Helpers.arrayReject(actionsList.basic, (item) => {
                    return item.action === 'crop'
                })
            }
        }

        if (!Helpers.hasPermission('folders.edit') || selectedFiles.length > 0) {
            actionsList.other = Helpers.arrayReject(actionsList.other, (item) => {
                return Helpers.inArray(['properties'], item.action)
            })
        }

        Helpers.each(actionsList, (action, key) => {
            Helpers.each(action, (item, index) => {
                let is_break = false
                switch (Helpers.getRequestParams().view_in) {
                    case 'all_media':
                        if (Helpers.inArray(['remove_favorite', 'delete', 'restore'], item.action)) {
                            is_break = true
                        }
                        break
                    case 'recent':
                        if (Helpers.inArray(['remove_favorite', 'delete', 'restore', 'make_copy'], item.action)) {
                            is_break = true
                        }
                        break
                    case 'favorites':
                        if (Helpers.inArray(['favorite', 'delete', 'restore', 'make_copy'], item.action)) {
                            is_break = true
                        }
                        break
                    case 'trash':
                        if (!Helpers.inArray(['preview', 'delete', 'restore', 'rename', 'download'], item.action)) {
                            is_break = true
                        }
                        break
                }
                if (!is_break) {
                    let template = ACTION_TEMPLATE.replace(/__action__/gi, item.action || '')
                        .replace(
                            '<i class="__icon__ dropdown-item-icon dropdown-item-icon"></i>',
                            '<span class="icon-tabler-wrapper dropdown-item-icon">__icon__</span>'
                        )
                        .replace('__icon__', '<span class="icon-tabler-wrapper dropdown-item-icon">__icon__</span>')
                        .replace('__icon__', item.icon || '')
                        .replace(/__name__/gi, Helpers.trans(`actions_list.${key}.${item.action}`) || item.name)

                    if (item.icon) {
                        template = template.replace('media-icon', 'media-icon dropdown-item-icon')
                    }

                    if (!index && initializedItem) {
                        template = `<li role="separator" class="divider"></li>${template}`
                    }

                    $dropdownActions.append(template)
                }
            })

            if (action.length > 0) {
                initializedItem++
            }
        })
    }

    static handleDownload(files) {
        const html = $('.media-download-popup')
        let downloadTimeout = null

        html.show()

        $httpClient
            .make()
            .withResponseType('blob')
            .post(RV_MEDIA_URL.download, { selected: files })
            .then((response) => {
                const fileName = (response.headers['content-disposition'] || '').split('filename=')[1].split(';')[0]
                const objectUrl = URL.createObjectURL(response.data)
                const a = document.createElement('a')

                a.href = objectUrl
                a.download = fileName
                document.body.appendChild(a)
                a.click()
                a.remove()

                URL.revokeObjectURL(objectUrl)
            })
            .finally(() => {
                html.hide()
                clearTimeout(downloadTimeout)
            })
    }
}
