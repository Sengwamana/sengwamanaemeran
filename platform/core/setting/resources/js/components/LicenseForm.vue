<template>
    <form id="license-form" ref="formRef" @submit.prevent="onSubmit">
        <slot
            v-bind="{
                initialized,
                loading,
                verified,
                license,
                deactivateLicense,
                resetLicense,
            }"
        ></slot>
    </form>
</template>

<script>
export default {
    props: {
        id: {
            type: String,
            default: () => null,
            required: true,
        },
        verifyUrl: {
            type: String,
            default: () => null,
            required: true,
        },
        activateLicenseUrl: {
            type: String,
            default: () => null,
            required: true,
        },
        deactivateLicenseUrl: {
            type: String,
            default: () => null,
            required: true,
        },
        resetLicenseUrl: {
            type: String,
            default: () => null,
            required: true,
        },
    },

    data() {
        return {
            initialized: null,
            loading: true,
            verified: false,
            license: null,
        }
    },

    mounted() {
        this.verifyLicense()
    },

    methods: {
        async verifyLicense() {
            return $httpClient
                .makeWithoutErrorHandler()
                .get(this.verifyUrl)
                .then(({ data }) => {
                    this.verified = true
                    this.license = data.data
                })
                .catch((data) => {
                    if (data.response.status === 400) {
                        Botble.showError(data.response.data.message)
                    }
                })
                .finally(() => {
                    this.initialized = true
                    this.loading = false
                })
        },

        async onSubmit() {
            const formData = new FormData(this.$refs.formRef)

            return this.doActivateLicense(formData)
        },

        async resetLicense() {
            const formData = new FormData(this.$refs.formRef)

            return this.doResetLicense(formData)
        },

        async deactivateLicense() {
            this.loading = true

            return $httpClient
                .make()
                .post(this.deactivateLicenseUrl)
                .then(() => {
                    this.verified = false
                })
                .finally(() => {
                    this.loading = false
                })
        },

        async doActivateLicense(formData) {
            this.loading = true

            return $httpClient
                .make()
                .postForm(this.activateLicenseUrl, formData)
                .then(({ data }) => {
                    this.verified = true
                    this.license = data.data
                    Botble.showSuccess(data.message)
                })
                .finally(() => {
                    this.loading = false
                })
        },

        async doResetLicense(formData) {
            this.loading = true

            return $httpClient
                .make()
                .postForm(this.resetLicenseUrl, formData)
                .then(({ data }) => {
                    this.verified = false

                    Botble.showSuccess(data.message)
                })
                .finally(() => {
                    this.loading = false
                })
        },
    },
}
</script>
