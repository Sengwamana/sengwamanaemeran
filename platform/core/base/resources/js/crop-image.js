class CropImage {
    modal = $(document).find('.crop-image-modal')
    image = this.modal.find('.cropper-image')
    cropper = null

    constructor() {
        this.modal
            .on('change', 'input[type="file"]', (e) => {
                const files = e.target.files
                let reader
                let file

                if (files && files.length > 0) {
                    file = files[0]

                    if (URL) {
                        this.image.prop('src', URL.createObjectURL(file))
                    } else if (FileReader) {
                        reader = new FileReader()
                        reader.onload = () => {
                            this.image.prop('src', reader.result)
                        }
                        reader.readAsDataURL(file)
                    }
                }

                this.init()
            })
            .on('click', 'button[type="submit"]', (e) => {
                e.preventDefault()

                const button = $(e.currentTarget)
                const form = this.modal.find('form')

                const canvas = this.cropper.getCroppedCanvas({
                    width: 160,
                    height: 160,
                })

                canvas.toBlob((blob) => {
                    const formData = new FormData()

                    formData.append(form.find('input[type="file"]').prop('name'), blob)

                    $httpClient
                        .make()
                        .withButtonLoading(button)
                        .post(form.prop('action'), formData)
                        .then(({ data }) => {
                            this.updateImage(data.data.url)

                            Botble.showSuccess(data.message)
                            this.modal.modal('hide')
                        })
                })
            })
            .on('shown.bs.modal', (e) => {
                const originalImage = $(e.relatedTarget).closest('.crop-image-container').find('.crop-image-original')

                const image = new Image()
                image.src = originalImage.prop('src')
                image.onload = () => {
                    this.image.prop('src', image.src)
                    this.init()
                }
            })
            .on('hidden.bs.modal', () => {
                this.destroy()
            })

        $(document).on('click', '[data-bb-toggle="delete-avatar"]', (e) => {
            e.preventDefault()

            const button = $(e.currentTarget)

            $httpClient
                .make()
                .post(button.prop('href'))
                .then(({ data }) => {
                    this.updateImage(data.data.url)

                    Botble.showSuccess(data.message)
                    this.modal.modal('hide')
                })
        })
    }

    init() {
        this.cropper && this.cropper.destroy()

        this.cropper = new Cropper(this.image[0], {
            aspectRatio: 1,
            preview: '.img-preview',
        })
    }

    destroy() {
        this.cropper.destroy()
        this.cropper = null
        this.image.prop('src', '')
        this.modal.find('input[type="file"]').val('')
    }

    updateImage(url) {
        $(document)
            .find('.crop-image-original')
            .each((i, el) => {
                if ($(el).is('img')) {
                    $(el).prop('src', url)
                } else {
                    $(el).css('background-image', `url(${url})`)
                }
            })
    }
}

$(() => {
    new CropImage()
})
