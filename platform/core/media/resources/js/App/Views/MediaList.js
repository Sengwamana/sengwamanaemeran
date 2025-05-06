import { Helpers } from '../Helpers/Helpers'
import { ActionsService } from '../Services/ActionsService'

export class MediaList {
    constructor() {
        this.group = {}
        this.group.list = $('#rv_media_items_list').html()
        this.group.tiles = $('#rv_media_items_tiles').html()

        this.item = {}
        this.item.list = $('#rv_media_items_list_element').html()
        this.item.tiles = $('#rv_media_items_tiles_element').html()

        this.$groupContainer = $('.rv-media-items')
    }

    renderData(data, reload = false, load_more_file = false) {
        let _self = this
        let MediaConfig = Helpers.getConfigs()
        let template = _self.group[Helpers.getRequestParams().view_type]

        let view_in = Helpers.getRequestParams().view_in

        if (!Helpers.inArray(['all_media', 'public', 'trash', 'favorites', 'recent'], view_in)) {
            view_in = 'all_media'
        }

        let icon

        switch (view_in) {
            case 'all_media':
                icon = `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                    <path d="M7 9l5 -5l5 5"></path>
                    <path d="M12 4l0 12"></path>
                </svg>`

                break

            case 'public':
                icon = `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"></path>
                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"></path>
                </svg>`

                break;

            case 'trash':
                icon = `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M4 7l16 0"></path>
                    <path d="M10 11l0 6"></path>
                    <path d="M14 11l0 6"></path>
                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                </svg>`

                break

            case 'favorites':
                icon = `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" stroke-width="0" fill="currentColor"></path>
                </svg>`

                break

            case 'recent':
                icon = `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                    <path d="M12 7v5l3 3"></path>
                </svg>`
        }

        template = template
            .replace(/__noItemIcon__/gi, icon)
            .replace(/__noItemTitle__/gi, Helpers.trans(`no_item.${view_in}.title`) || '')
            .replace(/__noItemMessage__/gi, Helpers.trans(`no_item.${view_in}.message`) || '')

        let $result = $(template)
        let $itemsWrapper = $result.find('ul')

        if (load_more_file && this.$groupContainer.find('.rv-media-grid ul').length > 0) {
            $itemsWrapper = this.$groupContainer.find('.rv-media-grid ul')
        }

        if (Helpers.size(data.folders) > 0 || Helpers.size(data.files) > 0 || load_more_file) {
            $('.rv-media-items').addClass('has-items')
        } else {
            $('.rv-media-items').removeClass('has-items')
        }

        Helpers.forEach(data.folders, (value) => {
            let item = _self.item[Helpers.getRequestParams().view_type]
            item = item
                .replace(/__type__/gi, 'folder')
                .replace(/__id__/gi, value.id)
                .replace(/__name__/gi, value.name || '')
                .replace(/__size__/gi, '')
                .replace(/__date__/gi, value.created_at || '')
                .replace(
                    /__thumb__/gi,
                    `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"></path>
                </svg>`
                )
            let $item = $(item)
            Helpers.forEach(value, (val, index) => {
                $item.data(index, val)
            })
            $item.data('is_folder', true)
            $item.data('icon', MediaConfig.icons.folder)
            $item.find('.rv-media-thumbnail').css('color', value.color)
            $itemsWrapper.append($item)
        })

        Helpers.forEach(data.files, (value) => {
            let item = _self.item[Helpers.getRequestParams().view_type]
            item = item
                .replace(/__type__/gi, 'file')
                .replace(/__id__/gi, value.id)
                .replace(/__name__/gi, value.name || '')
                .replace(/__size__/gi, value.size || '')
                .replace(/__date__/gi, value.created_at || '')
            if (Helpers.getRequestParams().view_type === 'list') {
                item = item.replace(/__thumb__/gi, value.icon)
            } else {
                item = item.replace(
                    /__thumb__/gi,
                    value.thumb
                        ? `<img src="${value.thumb ? value.thumb : value.full_url}" alt="${value.name}">`
                        : value.icon
                )
            }
            let $item = $(item)
            $item.data('is_folder', false)
            Helpers.forEach(value, (val, index) => {
                $item.data(index, val)
            })

            $itemsWrapper.append($item)
        })
        if (reload !== false) {
            _self.$groupContainer.empty()
        }

        if (!(load_more_file && this.$groupContainer.find('.rv-media-grid ul').length > 0)) {
            _self.$groupContainer.append($result)
        }
        _self.$groupContainer.find('.loading-spinner').remove()
        ActionsService.handleDropdown()

        // Trigger event click for file selected
        $(`.js-media-list-title[data-id=${data.selected_file_id}]`).trigger('click')
    }
}
