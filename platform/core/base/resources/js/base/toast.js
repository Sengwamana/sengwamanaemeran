/**
 * Options used for Toastify
 * @typedef {Object} ToastifyConfigurationObject
 * @property {string} text - Message to be displayed in the toast
 * @property {Element} node - Provide a node to be mounted inside the toast. node takes higher precedence over text
 * @property {number} duration - Duration for which the toast should be displayed. -1 for permanent toast
 * @property {string|Element} selector - CSS ID Selector on which the toast should be added
 * @property {boolean} close - To show the close icon or not
 * @property {string} gravity - To show the toast from top or bottom
 * @property {string} position - To show the toast on left or right
 * @property {string} className - Ability to provide custom class name for further customization
 * @property {boolean} stopOnFocus - To stop timer when hovered over the toast (Only if duration is set)
 * @property {Function} callback - Invoked when the toast is dismissed
 * @property {Function} onClick - Invoked when the toast is clicked
 * @property {Object} offset - Ability to add some offset to axis
 * @property {boolean} escapeMarkup - Toggle the default behavior of escaping HTML markup
 * @property {string} ariaLive - Use the HTML DOM style property to add styles to toast
 * @property {Object} style - Use the HTML DOM style property to add styles to toast
 * @property {string} icon - Icon to be shown before text
 */

class Toastify {
    defaults = {
        oldestFirst: true,
        text: 'Toastify is awesome!',
        node: undefined,
        duration: 3000,
        selector: undefined,
        callback: function () {},
        close: false,
        gravity: 'toastify-top',
        position: '',
        className: '',
        stopOnFocus: true,
        onClick: function () {},
        offset: { x: 0, y: 0 },
        escapeMarkup: true,
        ariaLive: 'polite',
        style: { background: '' },
    }

    constructor(options) {
        /**
         * The configuration object to configure Toastify
         * @type {ToastifyConfigurationObject}
         * @public
         */
        this.options = {}

        /**
         * The element that is the Toast
         * @type {Element}
         * @public
         */
        this.toastElement = null

        /**
         * The root element that contains all the toasts
         * @type {Element}
         * @private
         */
        this._rootElement = document.body

        this._init(options)
    }

    /**
     * Display the toast
     * @public
     */
    showToast() {
        this.toastElement = this._buildToast()

        if (typeof this.options.selector === 'string') {
            this._rootElement = document.getElementById(this.options.selector)
        } else if (this.options.selector instanceof HTMLElement || this.options.selector instanceof ShadowRoot) {
            this._rootElement = this.options.selector
        } else {
            this._rootElement = document.body
        }

        if (!this._rootElement) {
            throw 'Root element is not defined'
        }

        this._rootElement.insertBefore(this.toastElement, this._rootElement.firstChild)

        this._reposition()

        if (this.options.duration > 0) {
            this.toastElement.timeOutValue = window.setTimeout(() => {
                this._removeElement(this.toastElement)
            }, this.options.duration)
        }

        return this
    }

    /**
     * Hide the toast
     * @public
     */
    hideToast() {
        if (this.toastElement.timeOutValue) {
            clearTimeout(this.toastElement.timeOutValue)
        }
        this._removeElement(this.toastElement)
    }

    /**
     * Init the Toastify class
     * @param {ToastifyConfigurationObject} options - The configuration object to configure Toastify
     * @param {string} [options.text=Hi there!] - Message to be displayed in the toast
     * @param {Element} [options.node] - Provide a node to be mounted inside the toast. node takes higher precedence over text
     * @param {number} [options.duration=3000] - Duration for which the toast should be displayed. -1 for permanent toast
     * @param {string} [options.selector] - CSS Selector on which the toast should be added
     * @param {boolean} [options.close=false] - To show the close icon or not
     * @param {string} [options.gravity=toastify-top] - To show the toast from top or bottom
     * @param {string} [options.position=right] - To show the toast on left or right
     * @param {string} [options.className] - Ability to provide custom class name for further customization
     * @param {boolean} [options.stopOnFocus] - To stop timer when hovered over the toast (Only if duration is set)
     * @param {Function} [options.callback] - Invoked when the toast is dismissed
     * @param {Function} [options.onClick] - Invoked when the toast is clicked
     * @param {Object} [options.offset] - Ability to add some offset to axis
     * @param {boolean} [options.escapeMarkup=true] - Toggle the default behavior of escaping HTML markup
     * @param {string} [options.ariaLive] - Announce the toast to screen readers
     * @param {Object} [options.style] - Use the HTML DOM style property to add styles to toast
     * @private
     */
    _init(options) {
        this.options = Object.assign(this.defaults, options)

        this.toastElement = null

        this.options.gravity = options.gravity === 'bottom' ? 'toastify-bottom' : 'toastify-top'

        this.options.stopOnFocus = options.stopOnFocus === undefined ? true : options.stopOnFocus
    }

    /**
     * Build the Toastify element
     * @returns {Element}
     * @private
     */
    _buildToast() {
        if (!this.options) {
            throw 'Toastify is not initialized'
        }

        let divElement = document.createElement('div')
        divElement.className = `toastify on ${this.options.className} pe-5`

        divElement.className += ` toastify-${this.options.position}`

        divElement.className += ` ${this.options.gravity}`

        for (const property in this.options.style) {
            divElement.style[property] = this.options.style[property]
        }

        if (this.options.ariaLive) {
            divElement.setAttribute('aria-live', this.options.ariaLive)
        }

        if (this.options.icon !== '') {
            let iconElement = document.createElement('div')
            iconElement.className = 'toastify-icon'
            iconElement.innerHTML = this.options.icon

            divElement.appendChild(iconElement)
        }

        const textElement = document.createElement('span')
        textElement.className = 'toastify-text'

        if (this.options.node && this.options.node.nodeType === Node.ELEMENT_NODE) {
            textElement.appendChild(this.options.node)
        } else {
            if (this.options.escapeMarkup) {
                textElement.innerText = this.options.text
            } else {
                textElement.innerHTML = this.options.text
            }
        }

        divElement.appendChild(textElement)

        if (this.options.close === true) {
            let closeElement = document.createElement('button')
            closeElement.type = 'button'
            closeElement.setAttribute('aria-label', 'Close')
            closeElement.className = 'toast-close'
            closeElement.style.cssText = 'position: absolute; top: 8px; inset-inline-end: 8px;'
            closeElement.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M18 6l-12 12"></path>
                <path d="M6 6l12 12"></path>
            </svg>`

            closeElement.addEventListener('click', (event) => {
                event.stopPropagation()
                this._removeElement(this.toastElement)
                window.clearTimeout(this.toastElement.timeOutValue)
            })

            //Calculating screen width
            const width = window.innerWidth > 0 ? window.innerWidth : screen.width

            if (this.options.position === 'left' && width > 360) {
                divElement.insertAdjacentElement('afterbegin', closeElement)
            } else {
                divElement.appendChild(closeElement)
            }
        }

        if (this.options.stopOnFocus && this.options.duration > 0) {
            divElement.addEventListener('mouseover', (event) => {
                window.clearTimeout(divElement.timeOutValue)
            })

            divElement.addEventListener('mouseleave', () => {
                divElement.timeOutValue = window.setTimeout(() => {
                    this._removeElement(divElement)
                }, this.options.duration)
            })
        }

        if (typeof this.options.onClick === 'function') {
            divElement.addEventListener('click', (event) => {
                event.stopPropagation()
                this.options.onClick()
            })
        }

        if (typeof this.options.offset === 'object') {
            const x = this._getAxisOffsetAValue('x', this.options)
            const y = this._getAxisOffsetAValue('y', this.options)

            const xOffset = this.options.position === 'left' ? x : `-${x}`
            const yOffset = this.options.gravity === 'toastify-top' ? y : `-${y}`

            divElement.style.transform = `translate(${xOffset},${yOffset})`
        }

        return divElement
    }

    /**
     * Remove the toast from the DOM
     * @param {Element} toastElement
     */
    _removeElement(toastElement) {
        toastElement.className = toastElement.className.replace(' on', '')

        window.setTimeout(() => {
            if (this.options.node && this.options.node.parentNode) {
                this.options.node.parentNode.removeChild(this.options.node)
            }

            if (toastElement.parentNode) {
                toastElement.parentNode.removeChild(toastElement)
            }

            this.options.callback.call(toastElement)

            this._reposition()
        }, 400)
    }

    /**
     * Position the toast on the DOM
     * @private
     */
    _reposition() {
        let topLeftOffsetSize = {
            top: 15,
            bottom: 15,
        }
        let topRightOffsetSize = {
            top: 15,
            bottom: 15,
        }
        let offsetSize = {
            top: 15,
            bottom: 15,
        }

        let allToasts = this._rootElement.querySelectorAll('.toastify')

        let classUsed

        for (let i = 0; i < allToasts.length; i++) {
            if (allToasts[i].classList.contains('toastify-top') === true) {
                classUsed = 'toastify-top'
            } else {
                classUsed = 'toastify-bottom'
            }

            let height = allToasts[i].offsetHeight
            classUsed = classUsed.substr(9, classUsed.length - 1)

            let offset = 15

            let width = window.innerWidth > 0 ? window.innerWidth : screen.width

            if (width <= 360) {
                allToasts[i].style[classUsed] = `${offsetSize[classUsed]}px`

                offsetSize[classUsed] += height + offset
            } else {
                if (allToasts[i].classList.contains('toastify-left') === true) {
                    allToasts[i].style[classUsed] = `${topLeftOffsetSize[classUsed]}px`

                    topLeftOffsetSize[classUsed] += height + offset
                } else {
                    allToasts[i].style[classUsed] = `${topRightOffsetSize[classUsed]}px`

                    topRightOffsetSize[classUsed] += height + offset
                }
            }
        }
    }

    /**
     * Helper function to get offset
     * @param {string} axis - 'x' or 'y'
     * @param {ToastifyConfigurationObject} options - The options object containing the offset object
     */
    _getAxisOffsetAValue(axis, options) {
        if (options.offset[axis]) {
            if (isNaN(options.offset[axis])) {
                return options.offset[axis]
            } else {
                return `${options.offset[axis]}px`
            }
        }

        return '0px'
    }
}

function injectCSS() {
    const element = document.createElement('style')

    element.textContent = `
        .toastify {
            padding: 0.75rem 2rem 0.75rem 0.75rem;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow:
                0 3px 6px -1px rgba(0, 0, 0, 0.12),
                0 10px 36px -4px rgba(77, 96, 232, 0.3);
            background: -webkit-linear-gradient(315deg, #73a5ff, #5477f5);
            background: linear-gradient(135deg, #73a5ff, #5477f5);
            position: fixed;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.215, 0.61, 0.355, 1);
            border-radius: 2px;
            cursor: pointer;
            text-decoration: none;
            z-index: 999999;
            width: 25rem;
            max-width: calc(100% - 30px);
        }

        .toastify.on {
            opacity: 1;
        }

        .toastify-icon {
            width: 1.5rem;
            height: 1.5rem;
        }

        .toast-close {
            background: transparent;
            border: 0;
            color: white;
            cursor: pointer;
            font-family: inherit;
            font-size: 1em;
            opacity: 0.4;
            padding: 0 5px;
            position: absolute;
            top: 0.25rem;
            inset-inline-end: 0.25rem;
        }

        .toast-close svg {
            width: 1em;
            height: 1em;
        }

        .toastify-text a {
            text-decoration: underline;
            color: #fff;
        }

        .toastify-right {
            inset-inline-end: 15px;
        }

        .toastify-left {
            inset-inline-start: 15px;
        }

        .toastify-top {
            top: -150px;
        }

        .toastify-bottom {
            bottom: -150px;
        }

        .toastify-rounded {
            border-radius: 25px;
        }

        .toastify-center {
            margin-inline-start: auto;
            margin-inline-end: auto;
            inset-inline-start: 0;
            inset-inline-end: 0;
            max-width: fit-content;
            max-width: -moz-fit-content;
        }

        @media only screen and (max-width: 360px) {
            .toastify-right,
            .toastify-left {
                margin-inline-start: auto;
                margin-inline-end: auto;
                inset-inline-start: 0;
                inset-inline-end: 0;
                max-width: fit-content;
            }
        }
    `

    document.head.appendChild(element)
}

injectCSS()

function StartToastifyInstance(options) {
    return new Toastify(options)
}

export default StartToastifyInstance
