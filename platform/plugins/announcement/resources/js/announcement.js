'use strict'

const setCookie = (name, value, days) => {
    let expires = ''
    if (days) {
        const date = new Date()
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000))
        expires = `; expires=${date.toUTCString()}`
    }
    document.cookie = `${name}=${value || ''}${expires}; path=/`
}

const getCookie = (name) => {
    const nameEQ = name + '='
    const ca = document.cookie.split(';')
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i]
        while (c.charAt(0) === ' ') c = c.substring(1, c.length)
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length)
    }
    return null
}

document.addEventListener('DOMContentLoaded', () => {
    const init = () => {
        const wrapper = document.querySelector('.ae-anno-announcement-wrapper')

        if (!wrapper) {
            return
        }

        const announcements = wrapper.querySelectorAll('.ae-anno-announcement')
        const nextBtn = document.querySelector('.ae-anno-announcement__next-button')
        const prevBtn = document.querySelector('.ae-anno-announcement__previous-button')
        const dismissButton = document.querySelector('.ae-anno-announcement__dismiss-button')
        const autoplay = wrapper.getAttribute('data-announcement-autoplay') !== null
        const autoplayDelay = parseInt(wrapper.getAttribute('data-announcement-autoplay-delay') || 5000)

        const dismissedAnnouncements = JSON.parse(getCookie('ae-anno-dismissed-announcements') || '[]')

        let currentIndex = 1
        let autoplayInterval = null

        const autoplayAnnouncement = () => {
            if (autoplay && autoplayDelay) {
                if (autoplayInterval) {
                    clearInterval(autoplayInterval)
                }

                autoplayInterval = setInterval(() => {
                    currentIndex++
                    showAnnouncement(currentIndex)
                }, autoplayDelay)
            }
        }

        const showAnnouncement = () => {
            if (currentIndex > announcements.length) {
                currentIndex = 1
            } else if (currentIndex < 1) {
                currentIndex = announcements.length
            }

            announcements.forEach((announcement) => {
                announcement.style.display = 'none'
            })

            announcements[currentIndex - 1].style.display = 'block'

            autoplayAnnouncement()
        }

        showAnnouncement(currentIndex)

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentIndex++
                showAnnouncement(currentIndex)
            })
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                showAnnouncement(currentIndex--)
            })
        }

        if (dismissButton) {
            dismissButton.addEventListener('click', () => {
                const ids = JSON.parse(dismissButton.getAttribute('data-announcement-ids'))

                dismissedAnnouncements.push(...ids)

                setCookie('ae-anno-dismissed-announcements', JSON.stringify(dismissedAnnouncements), 365)

                wrapper.parentNode.removeChild(wrapper)
            })
        }

        autoplayAnnouncement()
    }

    const lazyLoading = $('[data-bb-toggle="announcement-lazy-loading"]')

    if (lazyLoading.length) {
        fetch(lazyLoading.data('url'), {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json', // Sending JSON
                'Accept': 'application/json' // Requesting JSON response
            }
        })
            .then(response => {
                // Check if the response is okay and parse it as JSON
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(({ data }) => {
                // Replace the content of lazyLoading with the fetched data
                lazyLoading.replaceWith(data);

                // Call the init function after replacing the content
                init();
            })
            .catch(error => {
                console.error('Fetch error:', error);
            });
    } else {
        init()
    }
})
