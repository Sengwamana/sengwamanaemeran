window.getStartedCookie = (() => {
    const COOKIE_VALUE = 1
    const COOKIE_NAME = 'get_started_cookie'
    const COOKIE_DOMAIN = window.location.hostname

    function setupCookie(expirationInDays = 1) {
        setCookie(COOKIE_NAME, COOKIE_VALUE, expirationInDays)
    }

    function cookieExists() {
        return document.cookie.split('; ').indexOf(COOKIE_NAME + '=' + COOKIE_VALUE) !== -1
    }

    function setCookie(name, value, expirationInDays) {
        const date = new Date()
        date.setTime(date.getTime() + expirationInDays * 24 * 60 * 60 * 1000)
        document.cookie = `${name}=${value};expires=${date.toUTCString()};domain=${COOKIE_DOMAIN};path=/`
    }

    return {
        setupCookie: setupCookie,
        cookieExists: cookieExists,
    }
})()

$(() => {
    if (!window.getStartedCookie.cookieExists()) {
        $('.get-started-modal[data-step="1"]').modal('show')
    }

    $(document).on('click', '.get-started-modal button[type=submit]', function (event) {
        event.preventDefault()
        event.stopPropagation()

        let _self = $(this)
        let _form = _self.closest('form')

        Botble.showButtonLoading(_self)

        $httpClient
            .make()
            .postForm(_form.prop('action'), new FormData(_form[0]))
            .then(({ data }) => {
                _self.closest('.get-started-modal').modal('hide')
                $(`.get-started-modal[data-step="${data.data.step}"]`).modal('show')
            })
            .finally(() => {
                Botble.hideButtonLoading(_self)
            })
    })

    $('.get-started-modal .btn-close').on('click', function (event) {
        event.preventDefault()
        let step = $(this).closest('.get-started-modal').data('step')
        $('.js-back-to-wizard').data('step', step)
        $(this).closest('.get-started-modal').modal('hide')

        $('.close-get-started-modal').modal('show')
    })

    $(document).on('click', '.js-back-to-wizard', function (event) {
        event.preventDefault()
        $(this).closest('.close-get-started-modal').modal('hide')
        $(`.get-started-modal[data-step="${$(this).data('step')}"]`).modal('show')
    })

    $(document).on('click', '.js-close-wizard', function (event) {
        event.preventDefault()
        window.getStartedCookie.setupCookie()
        $(this).closest('.close-get-started-modal').modal('hide')
    })

    $(document).on('click', '.resume-setup-wizard', function (event) {
        event.preventDefault()
        $('.get-started-modal[data-step="1"]').modal('show')
    })

    $('.get-started-modal').on('hide.bs.modal', () => {
        if (!window.getStartedCookie.cookieExists()) {
            window.getStartedCookie.setupCookie(0.2)
        }
    })
})
