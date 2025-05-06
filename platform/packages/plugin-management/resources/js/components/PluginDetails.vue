<script>
import { defineComponent } from 'vue'

export default defineComponent({
    props: {
        plugin: {
            type: Object,
            required: true,
        },
    },

    emits: ['back', 'install', 'uninstall', 'toggleActivation'],

    data() {
        return {
            isInstalled: false,
            isActivated: false,
        }
    },

    mounted() {
        this.initModal()
        this.checkInstalled()
        this.checkActivated()

        $event.on('plugin-installed', (packageName) => {
            if (packageName === this.packageName) {
                this.isInstalled = true
            }
        })

        $event.on('plugin-toggle-activation', (packageName) => {
            if (packageName === this.packageName) {
                this.isActivated = !this.isActivated
            }
        })

        $event.on('plugin-uninstalled', (packageName) => {
            if (packageName === this.packageName) {
                this.isInstalled = false
                this.isActivated = false
            }
        })
    },

    methods: {
        initModal() {
            const modal = new bootstrap.Modal(this.$refs.modal)

            modal.show()

            this.$refs.modal.addEventListener('hidden.bs.modal', () => {
                this.$emit('back')
            })
        },
        checkInstalled() {
            this.isInstalled = window.marketplace.installed.includes(this.packageName)
        },
        checkActivated() {
            this.isActivated = window.marketplace.activated.includes(this.packageName)
        },
        install() {
            bootstrap.Modal.getInstance(this.$refs.modal).hide()
            this.$emit('install', $event, this.plugin.id)
        },
    },

    computed: {
        packageName() {
            const packageName = this.plugin.package_name

            return packageName.substring(packageName.indexOf('/') + 1)
        },
        authorAvatar() {
            return `https://github.com/${this.plugin.author_name}.png`
        },
    },
})
</script>

<template>
    <div class="modal modal-blur fade" ref="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header py-3 px-5">
                    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between w-100">
                        <div>
                            <h2 class="mb-1">{{ plugin.name }}</h2>
                            <p class="text-muted mb-0">{{ plugin.description }}</p>
                        </div>

                        <a :href="plugin.url" target="_blank" class="btn me-5 d-none d-md-block">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="icon"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                fill="none"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6"></path>
                                <path d="M11 13l9 -9"></path>
                                <path d="M15 4h5v5"></path>
                            </svg>
                            {{ __('base.view_on_marketplace') }}
                        </a>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body bg-body">
                    <img :src="plugin.image_url" :alt="plugin.name" class="rounded" />

                    <div class="card my-3">
                        <div class="card-body">
                            <div class="datagrid">
                                <div class="datagrid-item">
                                    <div class="datagrid-title">{{ __('base.author') }}</div>
                                    <div class="datagrid-content">
                                        <div class="d-flex align-items-center">
                                            <span
                                                class="avatar avatar-xs me-2 rounded"
                                                :style="`background-image: url(${authorAvatar})`"
                                            ></span>
                                            {{ plugin.author_name }}
                                        </div>
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">{{ __('base.downloads') }}</div>
                                    <div class="datagrid-content">{{ plugin.downloads_count }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">{{ __('base.version') }}</div>
                                    <div class="datagrid-content">{{ plugin.latest_version }}</div>
                                </div>
                                <div class="datagrid-item" v-if="plugin.version_check">
                                    <div class="datagrid-title">{{ __('base.compatible_version') }}</div>
                                    <div class="datagrid-content">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="icon text-success"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                            stroke="currentColor"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M5 12l5 5l10 -10"></path>
                                        </svg>
                                        {{ plugin.minimum_core_version }}
                                    </div>
                                </div>
                                <div class="datagrid-item" v-else>
                                    <div class="datagrid-title">{{ __('base.incompatible_version') }}</div>
                                    <div class="datagrid-content">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="icon text-danger"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            stroke-width="2"
                                            stroke="currentColor"
                                            fill="none"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M5 12l5 5l10 -10"></path>
                                        </svg>
                                        {{ plugin.minimum_core_version }}
                                    </div>
                                </div>
                                <div class="datagrid-item" v-if="plugin.ratings_count > 0">
                                    <div class="datagrid-title">{{ __('base.rating') }}</div>
                                    <div class="datagrid-content d-flex align-items-center gap-1">
                                        <div class="lh-1">
                                            <svg
                                                v-for="n in 5"
                                                :key="n"
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-sm text-secondary"
                                                :class="{ 'text-yellow': n <= plugin.ratings_avg }"
                                                width="24"
                                                height="24"
                                                viewBox="0 0 24 24"
                                                stroke-width="2"
                                                stroke="currentColor"
                                                fill="none"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            >
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path
                                                    d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z"
                                                    stroke-width="0"
                                                    fill="currentColor"
                                                ></path>
                                            </svg>
                                        </div>
                                        <span class="text-muted">({{ plugin.ratings_count }})</span>
                                    </div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">{{ __('base.last_update') }}</div>
                                    <div class="datagrid-content">{{ plugin.humanized_last_updated_at }}</div>
                                </div>
                                <div class="datagrid-item">
                                    <div class="datagrid-title">{{ __('base.license') }}</div>
                                    <div class="datagrid-content">
                                        <a :href="plugin.license_url" target="_blank" v-if="plugin.license_url">
                                            {{ plugin.license }}
                                        </a>
                                        <template v-else>{{ plugin.license }}</template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-lg" v-if="plugin.content">
                        <div class="card-body markdown" v-html="plugin.content" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" @click="install" v-if="!isInstalled">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="icon"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            fill="none"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                            <path d="M7 11l5 5l5 -5"></path>
                            <path d="M12 4l0 12"></path>
                        </svg>
                        {{ __('base.install_now') }}
                    </button>
                    <template v-if="isInstalled">
                        <button
                            type="button"
                            class="btn btn-danger"
                            @click="$emit('uninstall', $event, this.packageName)"
                            v-if="!isActivated"
                        >
                            {{ __('base.remove') }}
                        </button>
                        <button
                            type="button"
                            class="btn"
                            :class="{
                                'btn-danger': isActivated,
                                'btn-primary': !isActivated,
                            }"
                            @click="$emit('toggleActivation', $event, this.packageName)"
                            v-text="isActivated ? __('base.deactivate') : __('base.activate')"
                        />
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
