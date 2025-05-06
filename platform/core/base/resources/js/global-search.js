$(function () {
    const $globalSearchModal = $('[data-bb-toggle="gs-modal"]')
    const $noResultTemplate = $('#gs-no-result-template')
    const $form = $('[data-bb-toggle="gs-form"]')
    const $input = $('[data-bb-toggle="gs-input"]')
    let previousValue = ''

    const activateResult = (element) => {
        if (!element) {
            return
        }

        element.addClass('active')
        element.attr('aria-selected', 'true')
    }

    const deactivateResult = (element) => {
        if (!element) {
            return
        }

        element.removeClass('active')
        element.attr('aria-selected', 'false')
    }

    let searchTimeout = null
    const debounceSearch = (e) => {
        if (searchTimeout) {
            clearTimeout(searchTimeout)
        }

        searchTimeout = setTimeout(() => searchHandler(e), 200)
    }

    const getResults = () => $('[data-bb-toggle="gs-result"]')

    const findSelected = () => {
        const $result = getResults()

        let $selected = $result.find('a.active')

        if ($selected.length <= 0) {
            $selected = $result.find('a:first')
        }

        return $selected
    }

    const autoSelectHandler = () => {
        const $links = getResults().find('a')

        if ($links.length <= 0) {
            return
        }

        $links.attr('aria-selected', 'false')
        $links.removeClass('active')

        const $selected = findSelected()

        if ($selected.length <= 0) {
            return
        }

        if (!$selected.hasClass('active')) {
            activateResult($selected)
        }
    }

    const navigateHandler = (e) => {
        if (!$globalSearchModal.hasClass('show')) {
            return
        }

        if (e.code !== 'ArrowUp' && e.code !== 'ArrowDown') {
            return
        }

        const isNext = e.code === 'ArrowDown'
        let $selected = findSelected()

        if ($selected.length <= 0) {
            return
        }

        if (isNext) {
            const $next = $selected.next()
            deactivateResult($selected)

            if ($next.length <= 0) {
                const $first = getResults().find('a:first')
                activateResult($first)
                return
            }

            activateResult($next)
        } else {
            const $previous = $selected.prev()
            deactivateResult($selected)

            if ($previous.length <= 0) {
                const $last = getResults().find('a:last')
                activateResult($last)
                return
            }

            activateResult($previous)
        }
    }

    const selectHandler = (e) => {
        if (!$globalSearchModal.hasClass('show')) {
            return
        }

        if (e.code !== 'Enter') {
            return
        }

        const $selected = findSelected()

        if ($selected.length <= 0) {
            return
        }

        $selected[0].click()
    }

    const searchHandler = (e) => {
        e.preventDefault()

        if (!$globalSearchModal.hasClass('show')) {
            return
        }

        const keyword = $input.val()

        if (keyword === previousValue) {
            return
        }

        previousValue = keyword

        const $result = getResults()

        $httpClient
            .make()
            .withLoading($result)
            .get($form.attr('action'), { keyword })
            .then(({ data: response }) => {
                $result.html(response.data)

                autoSelectHandler()
            })
    }

    $(document).on('keydown', (e) => {
        if ($globalSearchModal.hasClass('show')) {
            return
        }

        if (
            ((e.code === 'Slash' || e.code === 'KeyK') && (e.metaKey || e.ctrlKey)) ||
            (e.code === 'Slash' && e.target.tagName === 'BODY')
        ) {
            $globalSearchModal.modal('show')
        }
    })
    $(document).on('keydown', navigateHandler)
    $(document).on('keydown', selectHandler)

    $form.on('submit', searchHandler)

    $globalSearchModal.on('show.bs.modal', () => {
        getResults().html($noResultTemplate.html())
    })

    $globalSearchModal.on('shown.bs.modal', () => {
        $input.trigger('focus')
        autoSelectHandler()
    })

    $globalSearchModal.on('hidden.bs.modal', () => {
        $input.val('')
    })

    $input.on('keydown', (e) => {
        if (e.key === 'ArrowUp' || e.key === 'ArrowDown' || e.key === 'Enter') {
            e.preventDefault()
            return
        }

        debounceSearch(e)
    })

    $('[data-bb-toggle="gs-navbar-input"]').on('focus', () => $globalSearchModal.modal('show'))
})
