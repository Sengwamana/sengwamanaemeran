import { RecentItems } from '../Config/MediaConfig'
import { Helpers } from '../Helpers/Helpers'
import { ActionsService } from './ActionsService'
import { ContextMenuService } from './ContextMenuService'
import { MediaList } from '../Views/MediaList'
import { MediaDetails } from '../Views/MediaDetails'

export class MediaService {
    constructor() {
        this.MediaList = new MediaList()
        this.MediaDetails = new MediaDetails()
        this.breadcrumbTemplate = $('#rv_media_breadcrumb_item').html()
    }

    getMedia(reload = false, is_popup = false, load_more_file = false) {
        if (typeof RV_MEDIA_CONFIG.pagination != 'undefined') {
            if (RV_MEDIA_CONFIG.pagination.in_process_get_media) {
                return
            }

            RV_MEDIA_CONFIG.pagination.in_process_get_media = true
        }

        let _self = this

        _self.getFileDetails({
            icon: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M15 8h.01"></path>
                <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z"></path>
                <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5"></path>
                <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3"></path>
            </svg>`,
            nothing_selected: '',
        })

        let params = Helpers.getRequestParams()

        if (params.view_in === 'recent') {
            params = { ...params, recent_items: RecentItems }
        }

        if (is_popup === true) {
            params = { ...params, is_popup: true }
        } else {
            params = { ...params, is_popup: undefined }
        }

        params = { ...params, onSelectFiles: undefined }

        if (
            typeof params.search != 'undefined' &&
            params.search !== '' &&
            typeof params.selected_file_id != 'undefined'
        ) {
            params = { ...params, selected_file_id: undefined }
        }

        params = { ...params, load_more_file }

        if (typeof RV_MEDIA_CONFIG.pagination != 'undefined') {
            params = {
                ...params,
                paged: RV_MEDIA_CONFIG.pagination.paged,
                posts_per_page: RV_MEDIA_CONFIG.pagination.posts_per_page,
            }
        }

        Helpers.showAjaxLoading()

        $httpClient
            .make()
            .get(RV_MEDIA_URL.get_media, params)
            .then(({ data }) => {
                _self.MediaList.renderData(data.data, reload, load_more_file)
                _self.renderBreadcrumbs(data.data.breadcrumbs)
                MediaService.refreshFilter()
                ActionsService.renderActions()

                if (typeof RV_MEDIA_CONFIG.pagination != 'undefined') {
                    if (typeof RV_MEDIA_CONFIG.pagination.paged != 'undefined') {
                        RV_MEDIA_CONFIG.pagination.paged += 1
                    }

                    if (typeof RV_MEDIA_CONFIG.pagination.in_process_get_media != 'undefined') {
                        RV_MEDIA_CONFIG.pagination.in_process_get_media = false
                    }

                    if (
                        typeof RV_MEDIA_CONFIG.pagination.posts_per_page != 'undefined' &&
                        data.data.files.length + data.data.folders.length < RV_MEDIA_CONFIG.pagination.posts_per_page &&
                        typeof RV_MEDIA_CONFIG.pagination.has_more != 'undefined'
                    ) {
                        RV_MEDIA_CONFIG.pagination.has_more = false
                    }
                }
            })
            .finally(() => Helpers.hideAjaxLoading())
    }

    getFileDetails(data) {
        this.MediaDetails.renderData(data)
    }

    renderBreadcrumbs(breadcrumbItems) {
        let _self = this
        let $breadcrumbContainer = $('.rv-media-breadcrumb .breadcrumb')
        $breadcrumbContainer.find('li').remove()

        Helpers.each(breadcrumbItems, (value) => {
            let template = _self.breadcrumbTemplate
            template = template
                .replace(/__name__/gi, value.name || '')
                .replace(
                    /__icon__/gi,
                    value?.icon ||
                        `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"></path>
                </svg>`
                )
                .replace(/__folderId__/gi, value.id || 0)
            $breadcrumbContainer.append($(template))
        })
        $('.rv-media-container').attr('data-breadcrumb-count', Helpers.size(breadcrumbItems))
    }

    static refreshFilter() {
        const $rvMediaContainer = $('.rv-media-container')
        const viewIn = Helpers.getRequestParams().view_in
        const $actionsTarget = $('.rv-media-actions .btn:not([data-type="refresh"]):not([data-bs-toggle="offcanvas"])')

        if (viewIn !== 'all_media' && !Helpers.getRequestParams().folder_id) {
            $actionsTarget.addClass('disabled')
            $rvMediaContainer.attr('data-allow-upload', 'false')
        } else {
            $actionsTarget.removeClass('disabled')
            $rvMediaContainer.attr('data-allow-upload', 'true')
        }

        $('.rv-media-actions .btn.js-rv-media-change-filter-group').removeClass('disabled')

        const $emptyTrashBtn = $('.rv-media-actions .btn[data-action="empty_trash"]')

        $emptyTrashBtn.hide()

        if (viewIn === 'trash' && Helpers.size(Helpers.getItems()) > 0) {
            $emptyTrashBtn.removeClass('d-none disabled').show()
        }

        ContextMenuService.destroyContext()
        ContextMenuService.initContext()

        $rvMediaContainer.attr('data-view-in', viewIn)
    }
}
