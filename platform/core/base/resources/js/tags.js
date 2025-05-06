class TagsManager {
    init() {

        $(document)
            .find('.tags')
            .each(function (index, element) {
                if ($(element).hasClass('tagify')) {
                    return
                }

                let tagify = new Tagify(element, {
                    keepInvalidTags:
                        $(element).data('keep-invalid-tags') !== undefined
                            ? $(element).data('keep-invalid-tags')
                            : true,
                    enforceWhitelist:
                        $(element).data('enforce-whitelist') !== undefined
                            ? $(element).data('enforce-whitelist')
                            : false,
                    delimiters: $(element).data('delimiters') !== undefined ? $(element).data('delimiters') : ',',
                    whitelist: element.value ? element.value.trim().split(/\s*,\s*/) : [],
                    userInput: $(element).data('user-input') !== undefined ? $(element).data('user-input') : true,
                })

                if ($(element).data('url')) {
                    tagify.on('input', (e) => {
                        tagify.settings.whitelist.length = 0 // reset current whitelist
                        tagify.loading(true).dropdown.hide.call(tagify) // show the loader animation

                        $httpClient
                            .make()
                            .get($(element).data('url'))
                            .then(({data}) => {
                                tagify.settings.whitelist = data
                                tagify.loading(false).dropdown.show.call(tagify, e.detail.value)
                            })
                    })
                }
            })

        document.querySelectorAll('.list-tagify').forEach((element) => {
            if (! element.dataset.list || $(element).hasClass('tagify')) {
                return
            }

            const list = JSON.parse(element.dataset.list)

            let whiteList = []

            for (const [key, value] of Object.entries(list)) {
                whiteList.push({value: key, name: value})
            }

            let listChosen = String(element.value).split(',')

            let arrayChosen = whiteList.filter((obj) => {
                if (listChosen.includes(String(obj.value))) {
                    return {value: obj.id, name: obj.name}
                }
            })

            const tagTemplate = function (tagData) {
                return `
                <tag title="${tagData.title || tagData.name}"
                        contenteditable='false'
                        spellcheck='false'
                        tabIndex="-1"
                        class="${this.settings.classNames.tag} ${tagData.class ? tagData.class : ''}"
                        ${this.getAttributes(tagData)}>
                    <x title='' class='tagify__tag__removeBtn' role='button' aria-label='remove tag'></x>
                    <div class="d-flex align-items-center">
                        <span class='tagify__tag-text'>${tagData.name}</span>
                    </div>
                </tag>
            `
            }

            const suggestionTemplate = function (tagData) {
                return `
                <div ${this.getAttributes(tagData)}
                    class="tagify__dropdown__item d-flex align-items-center ${tagData.class ? tagData.class : ''}"
                    tabindex="0"
                    role="option">

                    <div class="d-flex flex-column">
                        <strong>${tagData.name}</strong>
                    </div>
                </div>
            `
            }

            let tagify = new Tagify(element, {
                tagTextProp: 'name',
                enforceWhitelist: true,
                skipInvalid: true, // do not temporarily add invalid tags
                dropdown: {
                    closeOnSelect: false,
                    enabled: 0,
                    classname: 'users-list',
                    searchKeys: ['value', 'name'],
                },
                templates: {
                    tag: tagTemplate,
                    dropdownItem: suggestionTemplate,
                },
                whitelist: whiteList,
                originalInputValueFormat: (valuesArr) => valuesArr.map((item) => item.value).join(','),
            })

            tagify.loadOriginalValues(arrayChosen)
        })
    }
}

$(() => {
    new TagsManager().init()
})
