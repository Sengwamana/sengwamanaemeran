<script>
import { defineComponent } from 'vue'

export default defineComponent({
    props: {
        plugin: {
            type: Object,
            required: true,
        },
    },

    emits: ['showPlugin', 'install', 'uninstall', 'toggleActivation'],

    data() {
        return {
            isInstalled: false,
            isActivated: false,
        }
    },

    mounted() {
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
        checkInstalled() {
            this.isInstalled = window.marketplace.installed.includes(this.packageName)
        },
        checkActivated() {
            this.isActivated = window.marketplace.activated.includes(this.packageName)
        },
    },

    computed: {
        packageName() {
            const packageName = this.plugin.package_name

            return packageName.substring(packageName.indexOf('/') + 1)
        },
    },
})
</script>

<template>
    <div class="col-md-3">
        <div class="card h-100">
            <div
                class="img-responsive img-responsive-21x9 card-img-top"
                :style="{ backgroundImage: `url(${plugin.image_url})` }"
            ></div>

            <div class="card-body">
                <h3 class="card-title">{{ plugin.name }}</h3>
                <p class="text-secondary">{{ plugin.description }}</p>
            </div>

            <div class="card-footer">
                <div class="d-flex">
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="$emit('install', $event, plugin.id)"
                        v-if="!isInstalled"
                    >
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
                            class="btn"
                            :class="{
                                'btn-danger': isActivated,
                                'btn-primary': !isActivated,
                            }"
                            @click="$emit('toggle-activation', $event, packageName)"
                            v-text="isActivated ? __('base.deactivate') : __('base.activate')"
                        />
                    </template>
                    <button class="btn ms-auto" @click="$emit('showPlugin', plugin)">
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
                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                            <path d="M12 9h.01"></path>
                            <path d="M11 12h1v4h1"></path>
                        </svg>
                        {{ __('base.detail') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
