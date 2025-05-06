<template>
    <slot v-bind="{ hasNewVersion, message }"></slot>
</template>

<script>
export default {
    props: {
        checkUpdateUrl: {
            type: String,
            default: () => null,
            required: true,
        },
    },

    data() {
        return {
            hasNewVersion: false,
            message: null,
        }
    },
    mounted() {
        this.checkUpdate()
    },

    methods: {
        checkUpdate() {
            axios
                .get(this.checkUpdateUrl)
                .then(({ data }) => {
                    if (!data.error && data.data.has_new_version) {
                        this.hasNewVersion = true
                        this.message = data.message
                    }
                })
                .catch(() => {})
        },
    },
}
</script>
