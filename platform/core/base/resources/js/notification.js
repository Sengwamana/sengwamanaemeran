$(() => {
    const $notification = $(document).find('#notification-sidebar')

    const updateNotificationsCount = () => {
        $httpClient
            .make()
            .get($notification.data('count-url'))
            .then(({ data }) => {
                $(document).find('.badge.notification-count').text(data.data)
            })
    }

    const updateNotificationsContent = (url) => {
        $httpClient
            .make()
            .get(url || $notification.data('url'))
            .then(({ data }) => {
                $notification.find('.notification-content').html(data.data)
            })
    }

    const closeNotification = () => {
        $notification.offcanvas('hide')
    }

    $notification.on('hide.bs.offcanvas', () => {
        $('.offcanvas-backdrop').remove()
    })

    $(document).on('click', '.offcanvas-backdrop', function () {
        $(this).remove()

        closeNotification()
    })

    $notification
        .on('show.bs.offcanvas', () => {
            updateNotificationsContent()

            $('body').after(`<div class="offcanvas-backdrop"></div>`)
        })
        .on('click', '.mark-all-notifications-as-read', function (e) {
            e.preventDefault()

            $httpClient
                .make()
                .put($(this).data('url'))
                .then(({ data }) => {
                    $notification.find('.notification-content').html(data.data)
                })
                .finally(() => {
                    updateNotificationsCount()
                    updateNotificationsContent()
                })
        })
        .on('click', '.clear-notifications', function () {
            $httpClient
                .make()
                .delete($(this).data('url'))
                .then(() => {})
                .finally(() => {
                    updateNotificationsCount()
                    closeNotification()
                })
        })
        .on('click', '.list-group-item .btn-delete-notification', function () {
            $httpClient
                .make()
                .delete($(this).data('url'))
                .then(() => {
                    const $this = $(this).closest('.list-group-item')

                    $this.hide('slow', () => {
                        $this.remove()
                        updateNotificationsContent()
                    })
                })
                .finally(() => {
                    updateNotificationsCount()
                })
        })
        .on('click', 'nav .btn-previous', function () {
            updateNotificationsContent($(this).data('url'))
        })
        .on('click', 'nav .btn-next', function () {
            updateNotificationsContent($(this).data('url'))
        })
})
