import Toastify from '../../../../core/base/resources/js/base/toast'

const Theme = Theme || {}
window.Theme = Theme

Theme.showNotice = function (messageType, message) {
    let color = '#fff'
    let icon = ''

    switch (messageType) {
        case 'success':
            color = '#51a351'
            icon =
                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>'
            break
        case 'danger':
            color = '#bd362f'
            icon =
                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>'
            break
        case 'warning':
            color = '#f89406'
            icon =
                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 8v4" /><path d="M12 16h.01" /></svg>'
            break
        case 'info':
            color = '#2f96b4'
            icon =
                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" /><path d="M12 9h.01" /><path d="M11 12h1v4h1" /></svg>'
            break
    }

    Toastify({
        text: message,
        icon: icon,
        duration: 5000,
        close: true,
        gravity: 'bottom',
        position: 'right',
        stopOnFocus: true,
        style: {
            background: color,
        },
        escapeMarkup: false,
    }).showToast()
}

Theme.showError = function (message) {
    this.showNotice('danger', message)
}

Theme.showSuccess = function (message) {
    this.showNotice('success', message)
}

Theme.handleError = (data) => {
    if (typeof data.errors !== 'undefined' && data.errors.length) {
        Theme.handleValidationError(data.errors)
    } else if (typeof data.responseJSON !== 'undefined') {
        if (typeof data.responseJSON.errors !== 'undefined') {
            if (data.status === 422) {
                Theme.handleValidationError(data.responseJSON.errors)
            }
        } else if (typeof data.responseJSON.message !== 'undefined') {
            Theme.showError(data.responseJSON.message)
        } else {
            Theme.showError(data.responseJSON.join(', ').join(', '))
        }
    } else {
        Theme.showError(data.statusText)
    }
}

Theme.handleValidationError = (errors) => {
    let message = ''

    Object.values(errors).forEach((item) => {
        if (message !== '') {
            message += '\n'
        }
        message += item
    })

    Theme.showError(message)
}
