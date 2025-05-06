import { Helpers } from '../Helpers/Helpers'

export class MediaDetails {
    constructor() {
        this.$detailsWrapper = $('.rv-media-main .rv-media-details')

        this.descriptionItemTemplate = `<div class="mb-3 rv-media-name">
            <label class="form-label">__title__</label>
            __url__
        </div>`

        this.onlyFields = [
            'name',
            'alt',
            'full_url',
            'size',
            'mime_type',
            'created_at',
            'updated_at',
            'nothing_selected',
        ]
    }

    renderData(data) {
        const _self = this
        const thumb = data.type === 'image' && data.full_url ? `<img src="${data.full_url}" alt="${data.name}">` : data.icon
        let description = ''
        Helpers.forEach(data, (val, index) => {
            if (Helpers.inArray(_self.onlyFields, index) && val) {
                if (!Helpers.inArray(['mime_type'], index)) {
                    description += _self.descriptionItemTemplate
                        .replace(/__title__/gi, Helpers.trans(index))
                        .replace(/__url__/gi,
                        val
                            ? index === 'full_url'
                                ? `<div class="input-group pe-1">
                                        <input type="text" id="file_details_url" class="form-control" value="${val}" />
                                        <button class="input-group-text btn btn-default js-btn-copy-to-clipboard" type="button"
                                                data-bb-toggle="clipboard"
                                                data-clipboard-action="copy"
                                                data-clipboard-message="Copied"
                                                data-clipboard-target="#file_details_url"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-clipboard me-0" data-clipboard-icon="true" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                               <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                               <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>
                                               <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>
                                            </svg>
                                            <svg class="icon text-success me-0 d-none" data-clipboard-success-icon="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                              <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                              <path d="M5 12l5 5l10 -10"></path>
                                            </svg>
                                        </button>
                                    </div>`
                                : `<span title="${val}">${val}</span>`
                            : ''
                    )
                }
            }
        })

        _self.$detailsWrapper.find('.rv-media-thumbnail').html(thumb)
        _self.$detailsWrapper.find('.rv-media-thumbnail').css('color', data.color)
        _self.$detailsWrapper.find('.rv-media-description').html(description)

        let dimensions = ''

        if (data.mime_type && data.mime_type.indexOf('image') !== -1) {
            const image = new Image()
            image.src = data.full_url

            image.onload = () => {
                dimensions += this.descriptionItemTemplate
                    .replace(/__title__/gi, Helpers.trans('width'))
                    .replace(/__url__/gi, `<span title="${image.width}">${image.width}px</span>`)

                dimensions += this.descriptionItemTemplate
                    .replace(/__title__/gi, Helpers.trans('height'))
                    .replace(/__url__/gi, `<span title="${image.height}">${image.height}px</span>`)

                _self.$detailsWrapper.find('.rv-media-description').append(dimensions)
            }
        }
    }
}
