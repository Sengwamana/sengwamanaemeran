import PendingRequest from './pending-request'

class HttpClient {
    constructor() {
        this.beforeSendCallbacks = []
        this.completedCallbacks = []
        this.errorHandlerCallback = null
        this.setupCallbacks = []
    }

    setup(callback) {
        this.setupCallbacks.push(callback)

        return this
    }

    beforeSend(callback) {
        this.beforeSendCallbacks.push(callback)

        return this
    }

    completed(callback) {
        this.completedCallbacks.push(callback)

        return this
    }

    errorHandlerUsing(callback) {
        this.errorHandlerCallback = callback

        return this
    }

    _create() {
        const request = new PendingRequest(this)

        for (const callback of this.setupCallbacks) {
            callback(request)
        }

        for (const callback of this.beforeSendCallbacks) {
            request.beforeSend(callback)
        }

        for (const callback of this.completedCallbacks) {
            request.completed(callback)
        }

        if (this.errorHandlerCallback) {
            request.errorHandlerUsing(this.errorHandlerCallback)
        }

        return request
    }

    makeWithoutErrorHandler() {
        return this._create()
    }

    make() {
        return this._create().withDefaultErrorHandler()
    }

    clone() {
        return new HttpClient()
    }
}

const axios = new PendingRequest().axios

export { axios }

export default HttpClient
