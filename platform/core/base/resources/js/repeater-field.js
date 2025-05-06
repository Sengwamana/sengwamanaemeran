$(() => {
    const htmlUnescapes = {
        '&amp;': '&',
        '&lt;': '<',
        '&gt;': '>',
        '&quot;': '"',
        '&#39;': "'",
    }
    const unescapeHtmlChar = basePropertyOf(htmlUnescapes)
    const reEscapedHtml = /&(?:amp|lt|gt|quot|#39);/g
    const reHasEscapedHtml = RegExp(reEscapedHtml.source)

    function basePropertyOf(object) {
        return function (key) {
            return object == null ? undefined : object[key]
        }
    }

    function unescape(string) {
        string = string.toString()

        return string && reHasEscapedHtml.test(string) ? string.replace(reEscapedHtml, unescapeHtmlChar) : string
    }

    $(document).on('click', '[data-target="repeater-add"]', function () {
        const id = $(this).data('id')
        const $group = $(`#${id}_group`)
        const $template = $(`#${id}_template`)
        const $fields = $(`#${id}_fields`)

        let nextIndex = parseInt($group.data('nextIndex'))
        let content = $template.html()
        let fields = $fields.text()
        content = content.replace(/__key__/g, nextIndex)
        fields = fields.replace(/__key__/g, nextIndex)
        content = content.replace(/__fields__/g, unescape(fields))

        $group.append(content)

        $group.data('nextIndex', nextIndex + 1)

        if (window.Botble) {
            window.Botble.initResources()
            window.Botble.initMediaIntegrate()
            window.Botble.initCoreIcon()
        }

        if (window.EditorManagement) {
            window.EDITOR = new EditorManagement().init()
        }
    })

    $(document).on('click', '[data-target="repeater-remove"]', function () {
        const id = $(this).data('id')

        $(`[data-id="${id}"]`).remove()
    })
})
