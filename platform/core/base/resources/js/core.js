import Toastify from './base/toast'

class Botble {
    static noticesTimeout = {}
    static noticesTimeoutCount = 500

    constructor() {
        this.initGlobalModal()
        this.countCharacter()
        this.manageSidebar()
        this.handleWayPoint()
        this.handleTurnOffDebugMode()
        Botble.initNavbarMinimal()
        Botble.initResources()
        Botble.initGlobalResources()
        Botble.handleCounterUp()
        Botble.initMediaIntegrate()

        if (
            BotbleVariables &&
            BotbleVariables.authorized === '0' &&
            typeof BotbleVariables.authorize_url !== 'undefined'
        ) {
            this.processAuthorize()
        }

        this.countMenuItemNotifications()
    }

    static initCoreIcon() {
        const $coreIcon = $(document).find('[data-bb-core-icon]')

        const formatTemplate = ({ id, text }) => {
            if (typeof id === 'undefined') {
                id = ''
            }

            return $(`<span><span class="dropdown-item-indicator">${text}</span> ${id}</span>`)
        }

        Botble.select($coreIcon, {
            ajax: {
                url: $coreIcon.data('url'),
                delay: 250,
                cache: true,
                data: function (params) {
                    return {
                        q: params.term,
                        page: params.page || 1,
                    }
                },
                processResults: function ({ data }) {
                    return {
                        results: $.map(data.data, function (icon, name) {
                            return {
                                text: icon,
                                id: name,
                            }
                        }),
                        pagination: {
                            more: data.next_page_url && Object.keys(data.data).length > 0,
                        },
                    }
                },
            },
            placeholder: $coreIcon.data('placeholder'),
            templateResult: formatTemplate,
            templateSelection: formatTemplate,
        }, true)
    }

    static blockUI(options) {
        options = options || {}

        Botble.showLoading(options.target)
    }

    static unblockUI(target) {
        Botble.hideLoading(target)
    }

    static showNotice(messageType, message, messageHeader = '') {
        let key = `notices_msg.${messageType}.${message}`
        let color = ''
        let icon = ''

        if (Botble.noticesTimeout[key]) {
            clearTimeout(Botble.noticesTimeout[key])
        }

        Botble.noticesTimeout[key] = setTimeout(() => {
            if (!messageHeader) {
                switch (messageType) {
                    case 'error':
                        messageHeader = BotbleVariables.languages.notices_msg.error
                        break
                    case 'success':
                        messageHeader = BotbleVariables.languages.notices_msg.success
                        break
                }
            }

            switch (messageType) {
                case 'error':
                    color = '#f44336'
                    icon =
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>'
                    break
                case 'success':
                    color = '#4caf50'
                    icon =
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10" /></svg>'
                    break
            }

            Toastify({
                text: message,
                duration: 5000,
                close: true,
                gravity: 'bottom',
                position: 'right',
                stopOnFocus: true,
                escapeMarkup: false,
                icon: icon,
                style: {
                    background: color,
                },
            }).showToast()
        }, Botble.noticesTimeoutCount)
    }

    static showError(message, messageHeader = '') {
        this.showNotice('error', message, messageHeader)
    }

    static showSuccess(message, messageHeader = '') {
        this.showNotice('success', message, messageHeader)
    }

    static handleError(data) {
        if (typeof data.errors !== 'undefined' && !_.isArray(data.errors)) {
            Botble.handleValidationError(data.errors)
        } else {
            if (typeof data.responseJSON !== 'undefined') {
                if (typeof data.responseJSON.errors !== 'undefined') {
                    if (data.status === 422) {
                        Botble.handleValidationError(data.responseJSON.errors)
                    }
                } else if (typeof data.responseJSON.message !== 'undefined') {
                    Botble.showError(data.responseJSON.message)
                } else {
                    $.each(data.responseJSON, (index, el) => {
                        $.each(el, (key, item) => {
                            Botble.showError(item)
                        })
                    })
                }
            } else {
                Botble.showError(data.statusText)
            }
        }
    }

    static handleValidationError(errors) {
        let message = ''
        $.each(errors, (index, item) => {
            message += item + '\n'
        })
        Botble.showError(message)
    }

    static callScroll(obj) {
        obj.mCustomScrollbar({
            theme: 'dark',
            scrollInertia: 0,
            callbacks: {
                whileScrolling: function () {
                    obj.find('.tableFloatingHeaderOriginal').css({
                        top: -this.mcs.top + 'px',
                    })
                },
            },
        })

        obj.stickyTableHeaders({ scrollableArea: obj, fixedOffset: 2 })
    }

    static async copyToClipboard(textToCopy, parentTarget) {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(textToCopy)
        } else {
            Botble.unsecuredCopyToClipboard(textToCopy, parentTarget)
        }
    }

    static unsecuredCopyToClipboard(textToCopy, parentTarget) {
        parentTarget = parentTarget || document.body
        const textArea = document.createElement('textarea')
        textArea.value = textToCopy
        textArea.style.position = 'absolute'
        textArea.style.left = '-999999px'
        parentTarget.append(textArea)
        textArea.select()

        try {
            document.execCommand('copy')
        } catch (error) {
            console.error('Unable to copy to clipboard', error)
        }

        parentTarget.removeChild(textArea)
    }

    initGlobalModal() {
        $(() => {
            $('[data-bb-toggle="modal"]').on('click', (event) => {
                event.preventDefault()

                const $this = $(event.currentTarget)

                const modalType = $this.data('type')
                const isActionModal = $this.data('actionModal')
                const data = JSON.parse($this.data('payload'))
                const actionUrl = $this.data('url')
                const actionMethod = $this.data('method')
                const confirmText = $this.data('confirmText')
                const cancelText = $this.data('cancelText')

                const modalId = `global-${modalType}-modal`
                const modal = $('#' + modalId)

                const modalTitle = $this.find('.modal-replace-title').html()
                const modalDescription = $this.find('.modal-replace-description').html()

                modal.find('.mb-2 i').siblings().remove()
                modal.find('.mb-2').append(modalTitle)
                modal.find('.mb-2').append(modalDescription)

                if (!isActionModal) {
                    modal.find('.modal-footer').remove()
                } else {
                    const modalFooter = `
                    <div class='modal-footer'>
                        <div class='w-100'>
                            <div class='row'>
                                <div class='col'>
                                    <button type='button' class='w-100 btn' data-bs-dismiss='modal'>${cancelText}</button>
                                </div>
                                <div class='col'>
                                    <button type='button' class='w-100 btn btn-${modalType} confirm-trigger-single-action-button'>${confirmText}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    `
                    modal.find('.modal-body').siblings().remove()
                    modal.find('.modal-body').after(modalFooter)

                    modal.find('.confirm-trigger-single-action-button').on('click', function () {
                        $.ajax({
                            type: actionMethod,
                            url: actionUrl,
                            data: data,
                            success: (res) => {},
                            error: (res) => {},
                            complete: () => modal.modal('hide'),
                        })
                    })
                }

                modal.modal('show')
            })
        })
    }

    countCharacter() {
        $.fn.charCounter = function (max, settings) {
            max = max || 100
            settings = $.extend(
                {
                    container: '<span></span>',
                    classname: 'charcounter',
                    format: `(%1/%2)`,
                    pulse: true,
                    delay: 0,
                    allowOverLimit: false,
                },
                settings
            )
            let p, timeout

            let count = (el, container) => {
                el = $(el)
                let current = el.val().length
                let remaining = max - el.val().length
                container.html(settings.format.replace(/%1/, current).replace(/%2/, max))

                container.toggleClass('text-danger', el.val().length > max)

                if (!settings.allowOverLimit && el.val().length > max) {
                    el.val(el.val().substring(0, max))
                    if (settings.pulse && !p) {
                        pulse(container, true)
                    }
                }

                if (settings.delay > 0) {
                    if (timeout) {
                        window.clearTimeout(timeout)
                    }
                    timeout = window.setTimeout(() => {
                        container.html(settings.format.replace(/%1/, remaining).replace(/%2/, max))
                    }, settings.delay)
                }
            }

            let pulse = (el, again) => {
                if (p) {
                    window.clearTimeout(p)
                    p = null
                }

                el.animate({ opacity: 0.1 }, 100, () => {
                    $(el).animate({ opacity: 1.0 }, 100)
                })

                if (again) {
                    p = window.setTimeout(() => pulse(el), 200)
                }
            }

            return this.each((index, el) => {
                let container
                if (!settings.container.match(/^<.+>$/)) {
                    container = $(settings.container)
                } else {
                    $(el)
                        .nextAll('.' + settings.classname)
                        .remove()

                    container = $(settings.container).insertAfter(el).addClass(settings.classname)
                }

                $(el)
                    .off('.charCounter')
                    .on('keydown.charCounter', () => {
                        count(el, container)
                    })
                    .on('keypress.charCounter', () => {
                        count(el, container)
                    })
                    .on('keyup.charCounter', () => {
                        count(el, container)
                    })
                    .on('focus.charCounter', () => {
                        count(el, container)
                    })
                    .on('mouseover.charCounter', () => {
                        count(el, container)
                    })
                    .on('mouseout.charCounter', () => {
                        count(el, container)
                    })
                    .on('paste.charCounter', () => {
                        setTimeout(() => {
                            count(el, container)
                        }, 10)
                    })
                if (el.addEventListener) {
                    el.addEventListener(
                        'input',
                        () => {
                            count(el, container)
                        },
                        false
                    )
                }
                count(el, container)
            })
        }

        $(document).on('click', 'input[data-counter], textarea[data-counter]', (event) => {
            const $this = $(event.currentTarget)

            $(event.currentTarget).charCounter($this.data('counter'), {
                container: '<small></small>',
                allowOverLimit: $this.data('allow-over-limit') == '' ? true : false,
            })
        })
    }

    manageSidebar() {
        let body = $('body')
        let navigation = $('.navigation')
        let sidebar_content = $('.sidebar-content')

        navigation.find('li.active').parents('li').addClass('active')
        navigation.find('li').has('ul').children('a').parent('li').addClass('has-ul')

        $(document).on('click', '.sidebar-toggle.d-none', (event) => {
            event.preventDefault()

            body.toggleClass('sidebar-narrow')
            body.toggleClass('page-sidebar-closed')

            if (body.hasClass('sidebar-narrow')) {
                navigation.children('li').children('ul').css('display', '')

                sidebar_content.delay().queue(() => {
                    $(event.currentTarget).show().addClass('animated fadeIn').clearQueue()
                })
            } else {
                navigation.children('li').children('ul').hide()
                navigation.children('li.active').children('ul').show()

                sidebar_content.delay().queue(() => {
                    $(event.currentTarget).show().addClass('animated fadeIn').clearQueue()
                })
            }
        })
    }

    static initNavbarMinimal() {
        $(document).on('click', '[data-bb-toggle="navbar-minimal"]', (event) => {
            const _self = $(event.currentTarget)
            const navbar = $(_self.data('bb-target'))

            if (navbar.length > 0) {
                navbar.toggleClass('navbar-minimal')

                $httpClient
                    .makeWithoutErrorHandler()
                    .post(
                        _self.data('url'),
                        {
                            _method: _self.data('method'),
                            minimal_sidebar: navbar.hasClass('navbar-minimal') ? 'yes' : 'no'
                        }
                    )
                    .then(() => {})
                    .catch(() => {})
            }
        })
    }

    static initDatePicker(element) {
        if (jQuery().flatpickr) {
            const $element = $(document).find(element);

            const $input = $element.find('input')

            let format = $input.data('date-format')

            if (!format) {
                format = 'Y-m-d'
            }

            let locale = window.siteEditorLocale

            if (locale === 'vi') {
                locale = 'vn'
            }

            let options = {
                dateFormat: format,
                wrap: true,
                locale: locale || 'en',
            }

            if ($input.data('options')) {
                options = Object.assign(options, $input.data('options'))
            }

            $element.flatpickr(options)
        }
    }

    static initResources() {
        $(document).on('click', '[data-bb-toggle="check-all"]', (e) => {
            const currentTarget = $(e.currentTarget)
            const targets = $(currentTarget.data('bb-target'))

            if (currentTarget.prop('checked')) {
                targets.prop('checked', true)
                currentTarget.prop('checked', false)
            } else {
                targets.prop('checked', false)
                currentTarget.prop('checked', true)
            }
        })

        $(document).find('[data-bb-toggle="check-all"]').each(function (index, element) {
            const $checkboxChildren = $(document).find($(element).attr('data-target'))

            const $parent = $(element).find('input[type=checkbox]')

            if ($checkboxChildren.length === $checkboxChildren.filter(':checked').length) {
                $parent.prop('indeterminate', false)
                $parent.prop('checked', true)
            } else {
                $parent.prop('indeterminate', true)
            }
        })

        $(document).find('[data-bb-toggle="check-all"]').each(function (index, element) {
            $(document).on('click', $(element).attr('data-target'), () => {
                const $checkboxChildren = $(document).find($(element).attr('data-target'))

                const $parent = $(element).find('input[type=checkbox]')

                if ($checkboxChildren.length === $checkboxChildren.filter(':checked').length) {
                    $parent.prop('indeterminate', false)
                    $parent.prop('checked', true)
                } else {
                    $parent.prop('indeterminate', true)
                }
            })
        })

        $(document).on('change', '.check-all', (event) => {
            let _self = $(event.currentTarget)
            let set = _self.attr('data-set')
            let checked = _self.find('input').prop('checked')
            $(set).each((index, el) => {
                if (checked) {
                    $(el).prop('checked', true)
                } else {
                    $(el).prop('checked', false)
                }
            })
        })

        $(document).find('.check-all').each(function (index, element) {
            $(document).on('click', $(element).attr('data-set'), () => {
                const $checkboxChildren = $(document).find($(element).attr('data-set'))

                const $parent = $(element).find('input[type=checkbox]')

                if ($checkboxChildren.length === $checkboxChildren.filter(':checked').length) {
                    $parent.prop('indeterminate', false)
                    $parent.prop('checked', true)
                } else {
                    $parent.prop('indeterminate', true)
                }
            })
        })

        $(document).find('.check-all').each(function (index, element) {
            const $checkboxChildren = $(document).find($(element).attr('data-set'))

            const $parent = $(element).find('input[type=checkbox]')

            if ($checkboxChildren.length === $checkboxChildren.filter(':checked').length) {
                $parent.prop('indeterminate', false)
                $parent.prop('checked', true)
            } else {
                $parent.prop('indeterminate', true)
            }
        })

        $.each($(document).find('select.select-search-full'), function (index, element) {
            Botble.select(element)
        })

        $.each($(document).find('select.select-full'), function (index, element) {
            Botble.select(element, {
                controlInput: null,
            })
        })

        $(document)
            .find('select.select-autocomplete')
            .each(function (index, element) {
                Botble.select(element, {
                    minimumInputLength: $(element).data('minimum-input') || 1,
                    width: '100%',
                    delay: 250,
                    ajax: {
                        url: $(element).data('url'),
                        data: (params) => ({ q: params.term, page: params.page || 1 }),
                        dataType: 'json',
                        type: $(element).data('type') || 'GET',
                        processResults: function (response) {
                            return {
                                results: $.map(response.data, function (item) {
                                    return Object.assign(
                                        {
                                            text: item.name,
                                            id: item.id,
                                        },
                                        item
                                    )
                                }),
                                pagination: {
                                    more: response.links ? response.links.next : null,
                                },
                            }
                        },
                        cache: true,
                    },
                })
            })

        $.each($(document).find('.select-multiple'), function (index, element) {
            const $element = $(element)

            Botble.select(element, {
                allowClear: $element.data('allow-clear'),
                placeholder: $element.data('placeholder'),
            })

            if ($(this).hasClass('.select-sorting')) {
                $(this).on('select2:select', function (e) {
                    const $element = $(e.params.data.element)

                    $element.detach()

                    $(this).append($element)

                    $(this).trigger('change')
                })
            }
        })

        $.each($(document).find('.select-search-ajax'), function (index, element) {
            const $element = $(element)

            if ($element.data('url')) {
                let options = {
                    placeholder: $element.data('placeholder') || '--Select--',
                    minimumInputLength: $element.data('minimum-input') || 1,
                    width: '100%',
                    delay: 250,
                    ajax: {
                        url: $element.data('url'),
                        dataType: 'json',
                        type: $element.data('type') || 'GET',
                        quietMillis: 50,
                        data: function (params) {
                            // Query parameters will be ?search=[term]&page=[page]
                            return {
                                search: params.term,
                                page: params.page || 1,
                            }
                        },
                        processResults: function (response) {
                            /**
                             * response {
                             *  error: false
                             *  data: {},
                             *  message: ''
                             * }
                             */
                            const data = Array.isArray(response.data) ? response.data : response.data.data

                            return {
                                results: $.map(data, function (item) {
                                    return {
                                        text: item.name,
                                        id: item.id,
                                    }
                                }),
                                pagination: {
                                    more: response.links ? response.links.next : null,
                                },
                            }
                        },
                        cache: true,
                    },
                    allowClear: true,
                }

                Botble.select(element, options)
                const selected = $element.data('selected')

                if (typeof selected !== 'undefined' && Object.keys(selected).length > 0) {
                    Object.keys(selected).forEach((key) => {
                        const option = new Option(selected[key], key, true, true)
                        $element.append(option).trigger('change')
                    })
                }
            }
        })

        $(document)
            .find('[data-bb-toggle="google-font-selector"]')
            .each(function (i, element) {
                if (!$(element).hasClass('select2-hidden-accessible')) {
                    let options = {
                        templateResult: function (opt) {
                            if (!opt.id) {
                                return opt.text
                            }

                            return $('<span style="font-family:\'' + opt.id + '\';"> ' + opt.text + '</span>')
                        },
                        width: '100%',
                    }

                    Botble.select(element, options)
                }
            })

        if (jQuery().timepicker) {
            $('.timepicker-default').timepicker({
                autoclose: true,
                showSeconds: false,
                minuteStep: 1,
                defaultTime: false,
            })

            $('.timepicker-24').timepicker({
                autoclose: true,
                minuteStep: 5,
                showSeconds: false,
                showMeridian: false,
                defaultTime: false,
                icons: {
                    up: 'icon ti ti-chevron-up',
                    down: 'icon ti ti-chevron-down',
                },
            })
        }

        if (jQuery().inputmask) {
            $.each($(document).find('.input-mask-number'), function (index, element) {
                $(element).inputmask({
                    alias: 'numeric',
                    rightAlign: false,
                    digits: $(element).data('digits') ?? 5,
                    groupSeparator: $(element).data('thousands-separator') ?? ',',
                    radixPoint: $(element).data('decimal-separator') ?? '.',
                    digitsOptional: true,
                    placeholder: $(element).data('placeholder') ?? '0',
                    autoGroup: true,
                    autoUnmask: true,
                    removeMaskOnSubmit: true,
                })
            })
        }

        if (jQuery().colorpicker) {
            $.each($(document).find('.color-picker'), function (index, element) {
                $(element)
                    .colorpicker({
                        popover: false,
                        inline: false,
                        container: true,
                        format: 'hex',
                        extensions: [
                            {
                                name: 'swatches',
                                options: {
                                    colors: {
                                        tetrad1: '#000000',
                                        tetrad2: '#000000',
                                        tetrad3: '#000000',
                                        tetrad4: '#000000',
                                    },
                                    namesAsValues: false,
                                },
                            },
                        ],
                    })
                    .on('colorpickerChange colorpickerCreate', function (e) {
                        let colors = e.color.generate('tetrad')

                        colors.forEach(function (color, i) {
                            let colorStr = color.string(),
                                swatch = e.colorpicker.picker.find(
                                    '.colorpicker-swatch[data-name="tetrad' + (i + 1) + '"]'
                                )

                            swatch
                                .attr('data-value', colorStr)
                                .attr('title', colorStr)
                                .find('> i')
                                .css('background-color', colorStr)
                        })
                    })
            })
        }

        if (jQuery().fancybox) {
            $('.iframe-btn').fancybox({
                width: '900px',
                height: '700px',
                type: 'iframe',
                autoScale: false,
                openEffect: 'none',
                closeEffect: 'none',
                overlayShow: true,
                overlayOpacity: 0.7,
            })

            $('.fancybox').fancybox({
                openEffect: 'none',
                closeEffect: 'none',
                overlayShow: true,
                overlayOpacity: 0.7,
                helpers: {
                    media: {},
                },
            })
        }

        if (jQuery().tooltip) {
            $('[data-bs-toggle="tooltip"]').tooltip({ placement: 'top', boundary: 'window' })
        }

        if (jQuery().areYouSure) {
            $('form.dirty-check').areYouSure()
        }

        function urlify(text) {
            const urlRegex = /(https?:\/\/[^\s]+)/g;

            if (
                text.includes('<a ')
                || text.includes('</a>')
                || text.includes(' href=')
                || text.includes('target="_blank"')
                || text.includes('<img src="')
            ) {
                return text
            }

            return text.replace(urlRegex, function(url) {
                return '<a href="' + url + '" target="_blank">' + url + '</a>';
            })
        }

        $.each($(document).find('.form-hint'), function (index, element) {
            $(element).html(urlify($(element).html()))
        })

        Botble.initDatePicker('.datepicker')

        if (jQuery().textareaAutoSize) {
            $('textarea.textarea-auto-height').textareaAutoSize()
        }

        Botble.initCodeEditorComponent()
        Botble.initColorPicker()
        Botble.initLightbox()
        Botble.initTreeCategoriesSelect()
        Botble.initCoreIcon()

        document.dispatchEvent(new CustomEvent('core-init-resources'))
    }

    static initGlobalResources() {
        $(document).on('submit', '.js-base-form', (event) => {
            $(event.currentTarget).find('button[type=submit]').addClass('disabled')
        })

        $(document).on('change', '.media-image-input', function () {
            const input = this

            if (input.files && input.files.length > 0) {
                const reader = new FileReader()
                reader.onload = function (e) {
                    $(input).closest('.image-box').find('.preview-image').prop('src', e.target.result)
                }

                reader.readAsDataURL(input.files[0])
            }
        })

        $(document).on('click', '.media-select-file', function (event) {
            event.preventDefault()
            event.stopPropagation()
            $(this).closest('.attachment-wrapper').find('.media-file-input').trigger('click')
        })

        Botble.initFieldCollapse()
        Botble.initTreeCheckboxes()
        Botble.initClipboard()
        Botble.initDropdownCheckboxes()
    }

    static numberFormat(number, decimals, dec_point, thousands_sep) {
        // *     example 1: number_format(1234.56);
        // *     returns 1: '1,235'
        // *     example 2: number_format(1234.56, 2, ',', ' ');
        // *     returns 2: '1 234,56'
        // *     example 3: number_format(1234.5678, 2, '.', '');
        // *     returns 3: '1234.57'
        // *     example 4: number_format(67, 2, ',', '.');
        // *     returns 4: '67,00'
        // *     example 5: number_format(1000);
        // *     returns 5: '1,000'
        // *     example 6: number_format(67.311, 2);
        // *     returns 6: '67.31'
        // *     example 7: number_format(1000.55, 1);
        // *     returns 7: '1,000.6'
        // *     example 8: number_format(67000, 5, ',', '.');
        // *     returns 8: '67.000,00000'
        // *     example 9: number_format(0.9, 0);
        // *     returns 9: '1'
        // *    example 10: number_format('1.20', 2);
        // *    returns 10: '1.20'
        // *    example 11: number_format('1.20', 4);
        // *    returns 11: '1.2000'
        // *    example 12: number_format('1.2000', 3);
        // *    returns 12: '1.200'
        let n = !isFinite(+number) ? 0 : +number,
            precision = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = typeof thousands_sep === 'undefined' ? ',' : thousands_sep,
            dec = typeof dec_point === 'undefined' ? '.' : dec_point,
            toFixedFix = (n, precision) => {
                // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                let k = Math.pow(10, precision)
                return Math.round(n * k) / k
            },
            s = (precision ? toFixedFix(n, precision) : Math.round(n)).toString().split('.')
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
        }

        if ((s[1] || '').length < precision) {
            s[1] = s[1] || ''
            s[1] += new Array(precision - s[1].length + 1).join('0')
        }

        return s.join(dec)
    }

    handleWayPoint() {
        $(document)
            .find('[data-bb-waypoint]')
            .each(function () {
                const target = $($(this).data('bb-target'))

                new Waypoint({
                    element: $(this),
                    handler: (direction) => {
                        if (direction === 'down') {
                            target.show()
                        } else {
                            target.hide()
                        }
                    },
                })
            })
    }

    handleTurnOffDebugMode() {
        const debugConfirmationModal = $(document).find('#debug-mode-turn-off-confirmation-modal')

        if (!debugConfirmationModal.length) {
            return
        }

        const $submit = debugConfirmationModal.find('#debug-mode-turn-off-form-submit')

        if (!$submit.length) {
            return
        }

        $submit.on('click', function (event) {
            event.preventDefault()

            Botble.showButtonLoading($submit[0])

            $httpClient
                .make()
                .post($submit.data('url'))
                .then(({ data }) => {
                    Botble.showSuccess(data.message)

                    debugConfirmationModal.modal('hide')

                    setTimeout(() => {
                        window.location.reload()
                    }, 1000)
                })
                .finally(() => {
                    Botble.hideButtonLoading($submit[0])
                })
        })
    }

    static handleCounterUp() {
        if (!$().counterUp) {
            return
        }

        $('[data-counter="counterup"]').counterUp({
            delay: 10,
            time: 1000,
        })
    }

    static openMediaUsing(callback) {}

    static handleOpenMedia(item) {}

    static initMediaIntegrate() {
        if (jQuery().rvMedia) {
            Botble.gallerySelectImageTemplate = `
            <div class='custom-image-box image-box'>
                <input type='hidden' name='__name__' value='' class='image-data'>
                    <div class='preview-image-wrapper w-100'>
                    <div class='preview-image-inner'>
                        <img src='${RV_MEDIA_CONFIG.default_image}' alt='${RV_MEDIA_CONFIG.translations.preview_image}' class='preview-image'>
                        <div class='image-picker-backdrop'></div>
                        <span class='image-picker-remove-button'>
                            <button data-bb-toggle='image-picker-remove' class='btn btn-sm btn-icon'>
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm icon-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                  <path d="M18 6l-12 12" />
                                  <path d="M6 6l12 12" />
                                </svg>
                            </button>
                        </span>
                        <div data-bb-toggle='image-picker-edit' class='image-box-actions cursor-pointer'></div>
                    </div>
                </div>
            </div>`

            const $btnGalleries = $('.btn_gallery')

            if ($btnGalleries.length > 0) {
                $btnGalleries.each(function () {
                    const item = $(this)

                    $(item).rvMedia({
                        multiple: false,
                        filter: $(item).data('action') === 'select-image' ? 'image' : 'everything',
                        view_in: 'all_media',
                        onSelectFiles: (files, $el) => {
                            switch ($el.data('action')) {
                                case 'media-insert-ckeditor':
                                    let content = ''
                                    $.each(files, (index, file) => {
                                        let link = file.full_url
                                        if (file.type === 'youtube') {
                                            link = link.replace('watch?v=', 'embed/')
                                            content +=
                                                '<iframe width="420" height="315" src="' +
                                                link +
                                                '" frameborder="0" allowfullscreen loading="lazy"></iframe><br />'
                                        } else if (file.type === 'image') {
                                            const alt = file.alt ? file.alt : file.name
                                            content +=
                                                '<img src="' + link + '" alt="' + alt + '" loading="lazy"/><br />'
                                        } else {
                                            content += '<a href="' + link + '">' + file.name + '</a><br />'
                                        }
                                    })

                                    window.EDITOR.CKEDITOR[$el.data('result')].insertHtml(content)

                                    break
                                case 'media-insert-tinymce':
                                    let html = ''
                                    $.each(files, (index, file) => {
                                        let link = file.full_url
                                        if (file.type === 'youtube') {
                                            link = link.replace('watch?v=', 'embed/')
                                            html += `<iframe width='420' height='315' src='${link}' allowfullscreen loading='lazy'></iframe><br />`
                                        } else if (file.type === 'image') {
                                            const alt = file.alt ? file.alt : file.name
                                            html += `<img src='${link}' alt='${alt}' loading='lazy'/><br />`
                                        } else {
                                            html += `<a href='${link}'>${file.name}</a><br />`
                                        }
                                    })
                                    tinymce.activeEditor.execCommand('mceInsertContent', false, html)
                                    break
                                case 'select-image':
                                    let firstImage = _.first(files)
                                    const $imageBox = $el.closest('.image-box')
                                    const allowThumb = $el.data('allow-thumb')
                                    $imageBox.find('.image-data').val(firstImage.url).trigger('change')
                                    $imageBox
                                        .find('.preview-image')
                                        .attr(
                                            'src',
                                            allowThumb && firstImage.thumb ? firstImage.thumb : firstImage.full_url
                                        )
                                    $imageBox.find('[data-bb-toggle="image-picker-remove"]').show()
                                    $imageBox.find('.preview-image').removeClass('default-image')
                                    $imageBox.find('.preview-image-wrapper').show()
                                    break
                                case 'attachment':
                                    const attachment = _.first(files)
                                    const wrapper = $el.closest('.attachment-wrapper')
                                    wrapper.find('.attachment-url').val(attachment.url)
                                    wrapper.find('.attachment-info').html(`
                                        <a href="${attachment.full_url}" target="_blank" title="${attachment.name}">${attachment.url}</a>
                                        <small class="d-block">${attachment.size}</small>
                                    `)

                                    wrapper.find('[data-bb-toggle="media-file-remove"]').show()
                                    wrapper.find('.attachment-details').removeClass('hidden')
                                    break
                                default:
                                    const coreInsertMediaEvent = new CustomEvent('core-insert-media', {
                                        detail: {
                                            files: files,
                                            element: $el,
                                        },
                                    })
                                    document.dispatchEvent(coreInsertMediaEvent)
                            }
                        },
                    })
                })
            }

            const gallerySelectImages = function (files, $currentBoxList, excludeIndexes = []) {
                let template = Botble.gallerySelectImageTemplate
                const allowThumb = $currentBoxList.data('allow-thumb')
                _.forEach(files, (file, index) => {
                    if (_.includes(excludeIndexes, index)) {
                        return
                    }
                    let imageBox = template.replace(/__name__/gi, $currentBoxList.data('name'))

                    let $template = $(
                        '<div class="col-lg-2 col-md-3 col-4 gallery-image-item-handler mb-2">' + imageBox + '</div>'
                    )

                    $template.find('.image-data').val(file.url).trigger('change')
                    $template
                        .find('.preview-image')
                        .attr('src', allowThumb ? file.thumb : file.full_url)
                        .show()
                    if (!allowThumb) {
                        $template.find('.preview-image-wrapper').addClass('preview-image-wrapper-not-allow-thumb')
                    }
                    $currentBoxList.append($template)
                    $('.list-images').find('.footer-action').show()
                })
            }

            new RvMediaStandAlone('[data-bb-toggle="gallery-add"]', {
                filter: 'image',
                view_in: 'all_media',
                onSelectFiles: (files, $el) => {
                    let $currentBoxList = $el
                        .closest('.gallery-images-wrapper')
                        .find('.images-wrapper .list-gallery-media-images')

                    $currentBoxList.removeClass('hidden')

                    $('.default-placeholder-gallery-image').addClass('hidden')

                    gallerySelectImages(files, $currentBoxList)
                },
            })

            new RvMediaStandAlone('[data-bb-toggle="image-picker-edit"]', {
                filter: 'image',
                view_in: 'all_media',
                onSelectFiles: (files, $el) => {
                    let firstItem = _.first(files)

                    let $currentBox = $el.closest('.gallery-image-item-handler').find('.image-box')
                    let $currentBoxList = $el.closest('.list-gallery-media-images')
                    const allowThumb = $currentBoxList.data('allow-thumb')

                    $currentBox.find('.image-data').val(firstItem.url).trigger('change')
                    $currentBox
                        .find('.preview-image')
                        .attr('src', allowThumb ? firstItem.thumb : firstItem.full_url)
                        .show()

                    gallerySelectImages(files, $currentBoxList, [0])
                },
            })

            $.each(
                $(document).find('[data-bb-toggle="image-picker-choose"][data-target="popup"]'),
                function (index, item) {
                    const _self = $(item)

                    _self.rvMedia({
                        multiple: false,
                        filter: 'image',
                        view_in: 'all_media',
                        onSelectFiles: (files, $el) => {
                            let firstImage = _.first(files)
                            const $imageBox = $el.closest('.image-box')
                            const allowThumb = $el.data('allow-thumb')
                            $imageBox.find('.image-data').val(firstImage.url).trigger('change')
                            $imageBox
                                .find('.preview-image')
                                .attr('src', allowThumb && firstImage.thumb ? firstImage.thumb : firstImage.full_url)
                            $imageBox.find('[data-bb-toggle="image-picker-remove"]').show()
                            $imageBox.find('.preview-image').removeClass('default-image')
                            $imageBox.find('.preview-image-wrapper').show()

                            const coreInsertMediaEvent = new CustomEvent('core-insert-media', {
                                detail: {
                                    files: files,
                                    element: $el,
                                },
                            })

                            document.dispatchEvent(coreInsertMediaEvent)
                        },
                    })
                }
            )
        }

        $(document).on('click', '[data-bb-toggle="image-picker-choose"][data-target="direct"]', (event) => {
            event.preventDefault()
            event.stopPropagation()

            $(event.currentTarget).closest('.image-box').find('.media-image-input').trigger('click')
        })

        $(document).on('show.bs.modal', '#image-picker-add-from-url', (event) => {
            const relatedTarget = $(event.relatedTarget)
            const imageBoxTarget = relatedTarget.data('bb-target')

            const modal = $(event.currentTarget)
            modal.find('input[name="image-box-target"]').val(imageBoxTarget)
        })

        $(document).on('submit', '#image-picker-add-from-url-form', (event) => {
            event.preventDefault()

            const form = $(event.currentTarget)
            const modal = form.closest('.modal')
            const button = modal.find('button[type="submit"]')

            const imageUrl = form.find('input[name="url"]').val()

            if (form.find('#download_image_to_local_storage').is(':checked')) {
                $httpClient
                    .make()
                    .withButtonLoading(button)
                    .post(form.prop('action'), {
                        url: imageUrl,
                        folderId: 0,
                    })
                    .then(({ data }) => {
                        form[0].reset()
                        modal.modal('hide')

                        const $imageBox = $(form.find('input[name="image-box-target"]').val())
                        $imageBox.find('.image-data').val(data.data.url).trigger('change')
                        $imageBox.find('.preview-image').prop('src', data.data.src)
                        $imageBox.find('[data-bb-toggle="image-picker-remove"]').show()
                        $imageBox.find('.preview-image').removeClass('default-image')
                        $imageBox.find('.preview-image-wrapper').show()
                    })
            } else {
                form[0].reset()
                modal.modal('hide')

                const $imageBox = $(form.find('input[name="image-box-target"]').val())
                $imageBox.find('.image-data').val(imageUrl).trigger('change')
                $imageBox.find('.preview-image').prop('src', imageUrl)
                $imageBox.find('[data-bb-toggle="image-picker-remove"]').show()
                $imageBox.find('.preview-image').removeClass('default-image')
                $imageBox.find('.preview-image-wrapper').show()
            }
        })

        $(document).on('click', '[data-bb-toggle="image-picker-remove"]', (event) => {
            event.preventDefault()
            const $this = $(event.currentTarget)
            let $imageBox = $this.closest('.image-box')
            $imageBox
                .find('.preview-image-wrapper img')
                .prop('src', $imageBox.find('.preview-image-wrapper img').data('default'))
            $imageBox.find('.image-data').val('').trigger('change')
            $imageBox.find('.preview-image').addClass('default-image')
            $this.hide()
        })

        $(document).on('click', '[data-bb-toggle="media-file-remove"]', (event) => {
            event.preventDefault()

            const currentTarget = $(event.currentTarget)
            const wrapper = currentTarget.closest('.attachment-wrapper')

            wrapper.find('.attachment-details').addClass('hidden')
            wrapper.find('.attachment-url').val('')

            currentTarget.hide()
        })

        $(document).on('click', '[data-bb-toggle="image-picker-remove"]', (e) => {
            e.preventDefault()
            const $this = $(e.currentTarget)
            $this.tooltip('dispose')
            const $list = $this.closest('.list-gallery-media-images')
            $this.closest('.gallery-image-item-handler').remove()
            if ($list.find('.gallery-image-item-handler').length === 0) {
                const $listImage = $list.closest('.list-images')
                $listImage.find('.default-placeholder-gallery-image').removeClass('hidden')
                $listImage.find('.footer-action').hide()
            }
        })

        const $listImages = $('.list-images')

        if ($listImages.length) {
            $(document).on('click', '[data-bb-toggle="gallery-reset"]', (e) => {
                e.preventDefault()

                $listImages.find('.list-gallery-media-images .gallery-image-item-handler').remove()
                $listImages.find('.default-placeholder-gallery-image').removeClass('hidden')
                $listImages.find('.footer-action').hide()
            })

            $listImages.find('.list-gallery-media-images').each((index, item) => {
                if (jQuery().sortable) {
                    let $current = $(item)
                    if ($current.data('ui-sortable')) {
                        $current.sortable('destroy')
                    }

                    $current.sortable()
                }
            })
        }
    }

    static getViewPort() {
        let e = window,
            a = 'inner'
        if (!('innerWidth' in window)) {
            a = 'client'
            e = document.documentElement || document.body
        }

        return {
            width: e[a + 'Width'],
            height: e[a + 'Height'],
        }
    }

    static initCodeEditor(id, type = 'css') {
        const isObject = typeof id === 'object'
        const editor = isObject ? $(id) : $(document).find('#' + id)
        id = isObject ? id.id : id

        if (isObject ? editor === undefined : !editor.length) {
            return
        }

        editor.wrap(`<div id='wrapper_${id}'><div class='container_content_codemirror'></div> </div>`)

        $(`#wrapper_${id}`).append(`<div class='handle-tool-drag' id='tool-drag_${id}'></div>`)

        CodeMirror.fromTextArea(editor[0], {
            extraKeys: { 'Ctrl-Space': 'autocomplete' },
            lineNumbers: true,
            mode: type,
            autoRefresh: true,
            lineWrapping: true,
        })

        $('.handle-tool-drag').mousedown((event) => {
            let _self = $(event.currentTarget)
            _self.attr('data-start_h', _self.parent().find('.CodeMirror').height()).attr('data-start_y', event.pageY)
            $('body').attr('data-dragtool', _self.attr('id')).on('mousemove', Botble.onDragTool)
            $(window).on('mouseup', Botble.onReleaseTool)
        })
    }

    static onDragTool(e) {
        let $element = $(`#${$('body').attr('data-dragtool')}`)
        let startHeight = parseInt($element.attr('data-start_h'))

        $element
            .parent()
            .find('.CodeMirror')
            .css('height', Math.max(200, startHeight + e.pageY - $element.attr('data-start_y')))
    }

    static onReleaseTool() {
        $('body').off('mousemove', Botble.onDragTool)
        $(window).off('mouseup', Botble.onReleaseTool)
    }

    processAuthorize() {
        $httpClient
            .makeWithoutErrorHandler()
            .post(BotbleVariables.authorize_url)
            .catch(() => {})
    }

    countMenuItemNotifications() {
        let $menuItems = $('.menu-item-count')
        if ($menuItems.length) {
            $httpClient
                .make()
                .get($menuItems.data('url') || BotbleVariables.menu_item_count_url)
                .then(({ data }) => {
                    data.data.map((x) => {
                        if (x.value > 0) {
                            $(`.menu-item-count.${x.key}`).text(x.value).show().removeClass('hidden')
                        }
                    })
                })
        }
    }

    static initFieldCollapse() {
        const parseValue = (value) => {
            if (typeof value === 'string') {
                try {
                    return JSON.parse(value)
                } catch (e) {
                    return value
                }
            }
            return value
        }

        const isEqual = (value1, value2) => String(value1) === String(value2)

        const handleCheckbox = (target, isReverse, isChecked) => {
            const targetElement = $(target)
            isReverse
                ? isChecked
                    ? targetElement.slideUp()
                    : targetElement.slideDown()
                : isChecked
                    ? targetElement.slideDown()
                    : targetElement.slideUp()
        }

        const handleValueBasedCollapse = (target, value) => {
            const targets = $(`${target}[data-bb-value]`)
            targets.each((_, element) => {
                const $element = $(element)
                const bbValue = parseValue($element.data('bb-value'))
                const shouldShow = Array.isArray(bbValue)
                    ? bbValue.some((v) => isEqual(v, value))
                    : isEqual(bbValue, value)
                shouldShow ? $element.slideDown() : $element.slideUp()
            })
        }

        const handleButtonCollapse = (target) => {
            const targetElement = $(target)
            if (targetElement.length) {
                targetElement.slideToggle()
            }
        }

        $(document).on('click change', '[data-bb-toggle="collapse"]', (e) => {
            const $target = $(e.currentTarget)
            const target = $target.data('bb-target')
            const type = e.currentTarget.type

            switch (type) {
                case 'checkbox':
                    handleCheckbox(target, $target.data('bb-reverse'), $target.prop('checked'))
                    break
                case 'radio':
                case 'select-one':
                    handleValueBasedCollapse(target, $target.val())
                    break
                case 'button':
                    handleButtonCollapse(target)
                    break
                default:
                    console.warn(`[Botble] Unknown type ${type} of collapse`)
            }
        })

        const collapsibleTargets = {}
        $(document)
            .find('[data-bb-collapse]')
            .each((_, element) => {
                collapsibleTargets[$(element).data('bb-trigger')] = true
            })

        Object.keys(collapsibleTargets).forEach((target) => {
            $(document).on('change', target, (e) => {
                const $target = $(e.currentTarget)
                const value = $target.val()
                const targetSelector = `[data-bb-trigger="${target}"]`

                $(targetSelector).slideUp()

                if (e.currentTarget.type !== 'checkbox' || $target.is(':checked')) {
                    handleValueBasedCollapse(targetSelector, value)
                }
            })
        })
    }

    static initTreeCheckboxes() {
        const handleChildren = function () {
            const $checkbox = $(this)
            const $checkboxChildren = $checkbox.parent().parent().find('ul input[type=checkbox]')

            $checkboxChildren.each(function () {
                if ($checkbox.prop('checked')) {
                    $(this).prop('checked', true)
                } else {
                    $(this).prop('checked', false)
                    $(this).prop('indeterminate', false)
                }
            })
        }

        const handleParents = function (current) {
            const $parent = $(current).closest('ul').closest('li').find('> .form-check > input[type=checkbox]')
            const $topParent = $parent.parent().parent()

            if ($topParent.find('ul input[type=checkbox]:checked').length > 0) {
                if (
                    $topParent.find('ul input[type=checkbox]:checked').length ===
                    $topParent.find('ul input[type=checkbox]').length
                ) {
                    $parent.prop('indeterminate', false)
                    $parent.prop('checked', true)
                } else {
                    $parent.prop('indeterminate', true)
                }
            } else {
                $parent.prop('indeterminate', false)

                if ($parent.prop('checked') || $topParent.find('ul input[type=checkbox]:checked').length > 0) {
                    $parent.prop('checked', true)
                } else {
                    $parent.prop('checked', false)
                }
            }

            if ($parent.length > 0) {
                handleParents($parent)
            }
        }

        const target = '[data-bb-toggle="tree-checkboxes"] input[type="checkbox"]'

        $(document).on('click', target, handleChildren)
        $(document).on('click', target, function () {
            handleParents(this)
        })

        $(document).ready(function () {
            $(target).each(function () {
                handleParents(this)
            })
        })
    }

    static initCodeEditorComponent() {
        $(document)
            .find('textarea[data-bb-code-editor]')
            .each(function () {
                if ($(this).next().hasClass('CodeMirror')) {
                    return
                }

                Botble.initCodeEditor(this, this.dataset.mode || 'htmlmixed')
            })
    }

    /**
     * @param {HTMLElement} element
     * @param {Boolean} overlay
     * @param {String} position
     */
    static showButtonLoading(element, overlay = true, position = 'start') {
        if (overlay && element) {
            $(element).addClass('btn-loading').attr('disabled', true)

            return
        }

        const loading = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>'
        const icon = $(element).find('svg')

        if (icon.length) {
            icon.addClass('d-none')
        }

        if (position === 'start') {
            $(element).prepend(loading)
        } else if (position === 'end') {
            $(element).append(loading)
        }
    }

    static hideButtonLoading(element) {
        if (!element) {
            return
        }

        if ($(element).hasClass('btn-loading')) {
            $(element).removeClass('btn-loading').removeAttr('disabled')

            return
        }

        $(element).find('.spinner-border').remove()
        $(element).find('svg').removeClass('d-none')
    }

    /**
     * @param {HTMLElement} element
     */
    static showLoading(element = null) {
        if (!element) {
            element = document.querySelector('.page-wrapper')
        }

        if ($(element).find('.loading-spinner').length) {
            return
        }

        $(element).addClass('position-relative')
        $(element).append('<div class="loading-spinner"></div>')
    }

    static hideLoading(element = null) {
        if (!element) {
            element = document.querySelector('.page-wrapper')
        }

        $(element).removeClass('position-relative')
        $(element).find('.loading-spinner').remove()
    }

    /**
     * @param {HTMLElement} element
     * @param {object} options
     * @param {boolean} force
     */
    static select(element, options = {}, force = false) {
        const $element = $(element)

        if (!jQuery().select2 || ($element.hasClass('select2-hidden-accessible') && ! force)) {
            return
        }

        options = {
            width: '100%',
            placeholder: $element.data('placeholder') || null,
            allowClear: $element.data('allow-clear') || false,
            ...options,
        }

        let parent = $element.closest('div[data-select2-dropdown-parent]') || $element.closest('.modal-content') || $element.closest('.modal')

        if (parent.length) {
            options.dropdownParent = parent
            options.width = '100%'
        }

        $element.select2(options)
    }

    /**
     * @param {String[]|HTMLElement} sources
     * @return {FsLightbox}
     */
    static lightbox(sources) {
        const lightbox = new FsLightbox()

        if (Array.isArray(sources)) {
            lightbox.props.sources = sources
            lightbox.open()
        }

        return lightbox
    }

    static initLightbox() {
        let instance = window.lightboxInstance || {}

        const a = document.querySelectorAll('a[data-bb-lightbox]')

        if (!a.length) {
            return
        }

        a.forEach((element) => {
            const instanceName = element.dataset.bbLightbox

            if (!instance[instanceName]) {
                instance[instanceName] = Botble.lightbox()
            }

            const source = element.href

            instance[instanceName].props.sources.push(source)
            instance[instanceName].elements.a.push(element)

            const currentIndex = instance[instanceName].props.sources.length - 1

            element.addEventListener('click', (e) => {
                e.preventDefault()

                instance[instanceName].open(currentIndex)
            })
        })

        window.lightboxInstance = instance
    }

    static initColorPicker() {
        if (!document.querySelector('[data-bb-color-picker]')) {
            return
        }

        $('[data-bb-color-picker]').each((index, item) => {
            let $current = $(item)

            let options = {
                allowEmpty: true,
                color: $current.val() || 'rgb(51, 51, 51)',
                showInput: true,
                containerClassName: 'full-spectrum',
                showInitial: true,
                showSelectionPalette: false,
                showPalette: true,
                showAlpha: true,
                preferredFormat: 'hex',
                showButtons: false,
                palette: [
                    [
                        'rgb(0, 0, 0)',
                        'rgb(102, 102, 102)',
                        'rgb(183, 183, 183)',
                        'rgb(217, 217, 217)',
                        'rgb(239, 239, 239)',
                        'rgb(243, 243, 243)',
                        'rgb(255, 255, 255)',
                        'rgb(230, 184, 175)',
                        'rgb(244, 204, 204)',
                        'rgb(252, 229, 205)',
                        'rgb(255, 242, 204)',
                        'rgb(217, 234, 211)',
                        'rgb(208, 224, 227)',
                        'rgb(201, 218, 248)',
                        'rgb(207, 226, 243)',
                        'rgb(217, 210, 233)',
                        'rgb(234, 209, 220)',
                        'rgb(221, 126, 107)',
                        'rgb(234, 153, 153)',
                        'rgb(249, 203, 156)',
                        'rgb(255, 229, 153)',
                        'rgb(182, 215, 168)',
                        'rgb(162, 196, 201)',
                        'rgb(164, 194, 244)',
                        'rgb(159, 197, 232)',
                        'rgb(180, 167, 214)',
                        'rgb(213, 166, 189)',
                        'rgb(204, 65, 37)',
                        'rgb(224, 102, 102)',
                        'rgb(246, 178, 107)',
                        'rgb(255, 217, 102)',
                        'rgb(147, 196, 125)',
                        'rgb(118, 165, 175)',
                        'rgb(109, 158, 235)',
                        'rgb(111, 168, 220)',
                        'rgb(142, 124, 195)',
                        'rgb(194, 123, 160)',
                        'rgb(166, 28, 0)',
                        'rgb(204, 0, 0)',
                        'rgb(230, 145, 56)',
                        'rgb(241, 194, 50)',
                        'rgb(106, 168, 79)',
                        'rgb(69, 129, 142)',
                        'rgb(60, 120, 216)',
                        'rgb(61, 133, 198)',
                        'rgb(103, 78, 167)',
                        'rgb(166, 77, 121)',
                        'rgb(133, 32, 12)',
                        'rgb(153, 0, 0)',
                        'rgb(180, 95, 6)',
                        'rgb(191, 144, 0)',
                        'rgb(56, 118, 29)',
                        'rgb(19, 79, 92)',
                        'rgb(17, 85, 204)',
                        'rgb(11, 83, 148)',
                        'rgb(53, 28, 117)',
                        'rgb(116, 27, 71)',
                        'rgb(91, 15, 0)',
                        'rgb(102, 0, 0)',
                        'rgb(120, 63, 4)',
                        'rgb(127, 96, 0)',
                        'rgb(39, 78, 19)',
                        'rgb(12, 52, 61)',
                        'rgb(28, 69, 135)',
                        'rgb(7, 55, 99)',
                        'rgb(32, 18, 77)',
                        'rgb(76, 17, 48)',
                        'rgb(152, 0, 0)',
                        'rgb(255, 0, 0)',
                        'rgb(255, 153, 0)',
                        'rgb(255, 255, 0)',
                        'rgb(0, 255, 0)',
                        'rgb(0, 255, 255)',
                        'rgb(74, 134, 232)',
                        'rgb(0, 0, 255)',
                        'rgb(153, 0, 255)',
                        'rgb(255, 0, 255)',
                    ],
                ],
                change: (color) => {
                    if (color) {
                        $current.val(color.toRgbString())
                    }
                },
            }

            let parent = $current.closest('.modal')

            if (parent.length) {
                options.appendTo = parent
            }

            $current.spectrum(options)
        })
    }

    static initClipboard() {
        $(document).on('click', '[data-bb-toggle="clipboard"]', async (e) => {
            e.preventDefault()

            const target = $(e.currentTarget)
            const copiedMessage = target.data('clipboard-message')
            const action = target.data('clipboard-action') || 'copy'
            const isCut = action.toLowerCase() === 'cut'
            const iconClipboard = target.find('[data-clipboard-icon]')
            const iconClipboardSuccess = target.find('[data-clipboard-success-icon]')
            const clipboardParent = target.data('clipboard-parent')
            const clipboardParentTarget = clipboardParent ? document.querySelector(clipboardParent) : undefined

            let text = target.data('clipboard-text')

            if (!text) {
                const copyTarget = $(target.data('clipboard-target'))

                if (copyTarget.length > 0) {
                    text = copyTarget.val()

                    isCut && copyTarget.val('')
                }
            }

            await Botble.copyToClipboard(text, clipboardParentTarget)

            if (copiedMessage) {
                Botble.showSuccess(copiedMessage)
            }

            iconClipboard.addClass('d-none')
            iconClipboardSuccess.removeClass('d-none')

            setTimeout(() => {
                iconClipboard.removeClass('d-none')
                iconClipboardSuccess.addClass('d-none')
            }, 1000)
        })
    }

    static initTreeCategoriesSelect() {
        const element = document.querySelector('[data-bb-toggle="tree-categories-select"]')

        if (!element) {
            return
        }

        Botble.select(element, {
            render: {
                option: (data) => {
                    return `<div>${data.renderOption}</div>`
                },
                item: (data) => {
                    return `<div>${data.renderItem}</div>`
                },
            },
        })
    }

    static initDropdownCheckboxes() {
        const countCheckedDropdownCheckboxes = (e) => {
            const $wrapper = e
                ? $(e.currentTarget).closest('[data-bb-toggle="dropdown-checkboxes"]')
                : $('[data-bb-toggle="dropdown-checkboxes"]')

            if (Array.isArray($wrapper)) {
                $wrapper.forEach((wrapper) => {
                    countCheckedDropdownCheckboxes(wrapper)
                })

                return
            }

            const $checkedCheckboxes = $wrapper.find('input[type="checkbox"]:checked')
            const $textElement = $wrapper.find('> span')

            if ($checkedCheckboxes.length) {
                if ($checkedCheckboxes.length > 3) {
                    $textElement.text($checkedCheckboxes.length + ' ' + $wrapper.data('selected-text'))
                } else {
                    const values = []

                    $checkedCheckboxes.each(function () {
                        values.push($(this).siblings('.form-check-label').text().trim())
                    })

                    $textElement.text(values.join(', '))
                }
            } else {
                if ($wrapper.length) {
                    $.map($wrapper, function (item) {
                        $(item).find('> span').text($(item).data('placeholder') || ' ')
                    })
                }
            }
        }

        countCheckedDropdownCheckboxes()

        $(document).on('click', '[data-bb-toggle="dropdown-checkboxes"] input[type="checkbox"]', (e) => {
            countCheckedDropdownCheckboxes(e)

            const $wrapper = $(e.currentTarget).closest('[data-bb-toggle="dropdown-checkboxes"]')

            const $selected = $wrapper.find('.multi-checklist-selected')

            if ($(e.currentTarget).is(':checked')) {
                const $input = `<input type="hidden" name="${$wrapper.data('name')}" value="${$(
                    e.currentTarget
                ).val()}">`
                $selected.append($input)
            } else {
                const $input = $selected.find(`input[value="${$(e.currentTarget).val()}"]`)
                $input.remove()
            }
        })

        $(document).on('click', '[data-bb-toggle="dropdown-checkboxes"] > span', function (event) {
            event.stopPropagation()

            const $this = $(this)
            const $input = $this.siblings('input[type="text"]')
            const $dropdown = $this.siblings('.dropdown-menu')
            const $wrapper = $this.closest('[data-bb-toggle="dropdown-checkboxes"]')

            $dropdown.addClass('show')
            $this.hide()
            $input.show().trigger('focus')

            if ($wrapper.data('ajax-url')) {
                const template = `<li>
                    <label class="form-check">
                        <input type="checkbox" id="__id__" class="form-check-input" value="__value__">
                        <span class="form-check-label">
                            __label__
                        </span>
                    </label>
                </li>`

                const name = $wrapper.data('name')

                $httpClient
                    .make()
                    .withLoading($dropdown)
                    .get($wrapper.data('ajax-url'))
                    .then(({ data }) => {
                        let html = ''

                        Object.keys(data).map((item) => {
                            html += template
                                .replace(/__id__/g, `${name}-${item}`)
                                .replace(/__value__/g, item)
                                .replace(/__label__/g, data[item])
                        })

                        $dropdown.find('ul').html(html)

                        const $selected = $wrapper.find('.multi-checklist-selected')
                        const $inputs = $selected.find('input[type="hidden"]')

                        $inputs.each(function () {
                            const $input = $(this)
                            const $checkbox = $dropdown.find(`input[value="${$input.val()}"]`)
                            $checkbox.prop('checked', true)
                        })
                    })
            }
        })

        $(document).on('click', function (event) {
            const $target = $(event.target)
            const $wrapper = $('[data-bb-toggle="dropdown-checkboxes"]')

            if (!$target.closest('[data-bb-toggle="dropdown-checkboxes"]').length) {
                $wrapper.find('> .dropdown-menu').removeClass('show')
                $wrapper.find('> span').show()
                $wrapper.find('> input[type="text"]').val('').hide()

                if ($wrapper.data('ajax-url')) {
                    $wrapper.find('> .dropdown-menu ul').html('<div class="py-5"></div>')
                }
            }
        })

        $(document).on('keyup', '[data-bb-toggle="dropdown-checkboxes"] input[type="text"]', function () {
            const $this = $(this)
            const $wrapper = $this.closest('[data-bb-toggle="dropdown-checkboxes"]')
            const $items = $wrapper.find('li')

            const value = $this.val().trim().toLowerCase()

            if (value.length) {
                $items.hide()
                $items.each(function () {
                    const $item = $(this)
                    const $label = $item.find('.form-check-label')

                    if ($label.text().trim().toLowerCase().indexOf(value) !== -1) {
                        $item.show()
                    }
                })
            } else {
                $items.show()
            }
        })
    }

    static initEditable() {
        const $element = $('.editable')

        if (!$element.length) {
            return
        }

        $element.editable({
            mode: 'inline',
            success: function (response) {
                if (response.error && response.message) {
                    Botble.showError(response.message)
                }
            },
            error: function (response) {
                Botble.handleError(response)
            },
        })
    }

    static unmaskInputNumber($form, formData) {
        if (jQuery().inputmask) {
            $form.find('input.input-mask-number').map(function (i, e) {
                const $input = $(e)
                if ($input.inputmask) {
                    if ($.isArray(formData)) {
                        formData[$input.attr('name')] = $input.inputmask('unmaskedvalue')
                    } else {
                        formData.append($input.attr('name'), $input.inputmask('unmaskedvalue'))
                    }
                }
            })

            return formData
        }
    }
}

$(() => {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
    })

    new Botble()
    window.Botble = Botble
})
