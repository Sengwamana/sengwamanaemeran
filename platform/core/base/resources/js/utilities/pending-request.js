import AxiosInstance, { AxiosError } from 'axios'

class PendingRequest {
    constructor(httpClient) {
        this.httpClient = httpClient
        this.beforeSendCallbacks = []
        this.completedCallbacks = []
        this.errorHandlerCallback = null
        this.config = {}

        this.axios = AxiosInstance.create({
            baseURL: window.location.origin,
            withCredentials: true,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
    }

    setup(callback) {
        callback(this)

        return this
    }

    withCredentials(enable) {
        this.withConfig({ withCredentials: enable })

        return this
    }

    withConfig(config) {
        this.config = { ...this.config, ...config }

        return this
    }

    withButtonLoading(element) {
        this.beforeSend(() => Botble.showButtonLoading(element))
        this.completed(() => Botble.hideButtonLoading(element))

        return this
    }

    withLoading(element) {
        this.beforeSend(() => Botble.showLoading(element))
        this.completed(() => Botble.hideLoading(element))

        return this
    }

    baseURL(url) {
        this.withConfig({ baseURL: url })

        return this
    }

    method(method) {
        this.withConfig({ method })

        return this
    }

    withHeaders(headers) {
        const originalHeaders = this.config.headers || {}
        this.withConfig({ headers: { ...originalHeaders, ...headers } })

        return this
    }

    withResponseType(type) {
        this.withConfig({ responseType: type })

        return this
    }

    async get(path, params = {}) {
        this.withConfig({ method: 'get', url: path, params })

        return this.send()
    }

    async head(path, params = {}) {
        this.withConfig({ method: 'head', url: path, params })

        return this.send()
    }

    async options(path, params = {}) {
        this.withConfig({ method: 'options', url: path, params })

        return this.send()
    }

    async post(path, data = {}) {
        this.withConfig({ method: 'post', url: path, data })

        return this.send()
    }

    async put(path, data = {}) {
        this.withConfig({ method: 'post', url: path, data: { _method: 'put', ...data } })

        return this.send()
    }

    async patch(path, data = {}) {
        this.withConfig({ method: 'post', url: path, data: { _method: 'patch', ...data } })

        return this.send()
    }

    async delete(path, data = {}) {
        this.withConfig({ method: 'post', url: path, data: { _method: 'delete', ...data } })

        return this.send()
    }

    async postForm(path, data = null) {
        data = data || new FormData()

        if (!(data instanceof FormData)) {
            throw new Error('Data must be an instance of FormData.')
        }

        this.withConfig({ method: 'post', url: path, data })

        return this.send()
    }

    async putForm(path, data = null) {
        data = data || new FormData()

        if (!(data instanceof FormData)) {
            throw new Error('Data must be an instance of FormData.')
        }

        data.set('_method', 'put')

        this.withConfig({ method: 'post', url: path, data })

        return this.send()
    }

    async patchForm(path, data = null) {
        data = data || new FormData()

        if (!(data instanceof FormData)) {
            throw new Error('Data must be an instance of FormData.')
        }

        data.set('_method', 'patch')

        this.withConfig({ method: 'post', url: path, data })

        return this.send()
    }

    beforeSend(callback) {
        this.beforeSendCallbacks.push(callback)

        return this
    }

    async handleBeforeSend() {
        for (const callback of this.beforeSendCallbacks) {
            await callback(this)
        }
    }

    completed(callback) {
        this.completedCallbacks.push(callback)

        return this
    }

    async handleCompleted() {
        for (const callback of this.completedCallbacks) {
            await callback(this)
        }
    }

    errorHandlerUsing(callback) {
        this.errorHandlerCallback = callback

        return this
    }

    async handleError(error) {
        if (this.errorHandlerCallback) {
            await this.errorHandlerCallback(error)
        }
    }

    clearErrorHandler() {
        this.errorHandlerCallback = null
    }

    withDefaultErrorHandler() {
        this.errorHandlerUsing((error) => {
            const statusCode = error.response.status
            const data = error.response.data

            switch (statusCode) {
                case 419:
                    Botble.showError('Session expired this page will reload in 5s.')
                    setTimeout(() => window.location.reload(), 5000)
                    break
                case 422:
                    if (typeof data.errors !== 'undefined') {
                        Botble.handleValidationError(data.errors)
                    }
                    break
                default:
                    if (data.message !== undefined) {
                        Botble.showError(data.message)
                    }
            }

            return Promise.reject(error)
        })

        return this
    }

    async send() {
        try {
            await this.handleBeforeSend(this)

            const response = await this.axios(this.config)

            if (response.data && response.data.error) {
                const { data, status } = response

                throw new AxiosError(data.message, status.toString(), this.config, null, response)
            }

            return response
        } catch (error) {
            await this.handleError(error)

            return Promise.reject(error)
        } finally {
            await this.handleCompleted(this)
        }
    }
}

export default PendingRequest
