import { MediaConfig } from '../Config/MediaConfig'
import { MediaService } from './MediaService'
import { MessageService } from './MessageService'
import { Helpers } from '../Helpers/Helpers'

export class FolderService {
    constructor() {
        this.MediaService = new MediaService()

        $(document).on('shown.bs.modal', '#modal_add_folder', (event) => {
            $(event.currentTarget).find('form input[type=text]').focus()
        })
    }

    create(folderName) {
        let _self = this

        $httpClient
            .make()
            .withButtonLoading($(document).find('#modal_add_folder button[type=submit]'))
            .post(RV_MEDIA_URL.create_folder, {
                parent_id: Helpers.getRequestParams().folder_id,
                name: folderName,
            })
            .then(({ data }) => {
                MessageService.showMessage('success', data.message, Helpers.trans('message.success_header'))
                Helpers.resetPagination()
                _self.MediaService.getMedia(true)
                FolderService.closeModal()
            })
    }

    changeFolder(folderId) {
        MediaConfig.request_params.folder_id = folderId
        Helpers.storeConfig()
        this.MediaService.getMedia(true)
    }

    static closeModal() {
        $(document).find('#modal_add_folder').modal('hide')
    }
}
