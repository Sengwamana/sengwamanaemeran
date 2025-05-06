import { axios, HttpClient } from './utilities'

window._ = require('lodash')

window.axios = axios

window.$httpClient = new HttpClient()

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    },
})

$(() => {
    setTimeout(() => {
        if (typeof siteAuthorizedUrl === 'undefined' || typeof isAuthenticated === 'undefined' || !isAuthenticated) {
            return
        }

        const $reminder = $('[data-bb-toggle="authorized-reminder"]')

        if ($reminder.length) {
            return
        }

        $httpClient
            .makeWithoutErrorHandler()
            .get(siteAuthorizedUrl, { verified: true })
            .then(() => null)
            .catch((error) => {
                if (!error.response || error.response.status !== 200) {
                    return
                }

                $(error.response.data.data.html).prependTo('body')
                $(document).find('.alert-license').slideDown()
            })
    }, 1000)
})
