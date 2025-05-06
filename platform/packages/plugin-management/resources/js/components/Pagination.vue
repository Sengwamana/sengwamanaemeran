<script>
import { defineComponent } from 'vue'

export default defineComponent({
    props: {
        meta: {
            type: Object,
            required: true,
        },

        scrollToTop: {
            type: Boolean,
            default: false,
        },
    },

    computed: {
        totalPages() {
            return this.meta.last_page
        },

        currentPage() {
            return this.meta.current_page
        },

        hasPrevious() {
            return this.currentPage > 1
        },

        hasNext() {
            return this.currentPage < this.totalPages
        },

        pages() {
            const pages = []

            for (let i = 1; i <= this.meta.last_page; i++) {
                pages.push(i)
            }

            return pages
        },

        fromItem() {
            return this.meta.from || 0
        },

        toItem() {
            return this.meta.to || 0
        },

        totalItems() {
            return this.meta.total
        },
    },

    methods: {
        selectPage(page) {
            if (page === this.currentPage) {
                return
            }

            this.$emit('page-selected', page)

            if (this.scrollToTop) {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth',
                })
            }
        },

        selectPreviousPage() {
            this.selectPage(this.currentPage - 1)
        },

        selectNextPage() {
            this.selectPage(this.currentPage + 1)
        },
    },
})
</script>

<template>
    <nav class="d-flex justify-content-between align-items-center">
        <p class="m-0 text-secondary">
            {{ __('base.showing') }}
            <span>{{ fromItem }}</span>
            {{ __('base.to') }}
            <span>{{ toItem }}</span>
            {{ __('base.of') }}
            <span>{{ totalItems }}</span>
            {{ __('base.results') }}
        </p>
        <ul class="pagination">
            <li class="page-item" :class="{ disabled: !hasPrevious }">
                <a
                    href="javascript:void(0)"
                    class="page-link"
                    :tabindex="!hasPrevious ? -1 : undefined"
                    :aria-disabled="!hasPrevious"
                    @click="selectPreviousPage"
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
                        <path d="M15 6l-6 6l6 6"></path>
                    </svg>
                </a>
            </li>
            <li
                class="page-item"
                v-for="(page, i) in pages"
                :key="i"
                :class="{ active: page === currentPage }"
                :aira-current="page === currentPage"
            >
                <a
                    href="javascript:void(0)"
                    class="page-link"
                    @click="selectPage(page)"
                    v-text="page === '...' ? '...' : page"
                />
            </li>
            <li class="page-item" :class="{ disabled: !hasNext }">
                <a
                    href="javascript:void(0)"
                    class="page-link"
                    @click="selectNextPage"
                    :tabindex="!hasNext ? -1 : undefined"
                    :aria-disabled="!hasNext"
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
                        <path d="M9 6l6 6l-6 6"></path>
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
</template>
