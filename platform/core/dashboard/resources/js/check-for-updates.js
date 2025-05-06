import CheckForUpdates from './components/CheckForUpdates.vue'

if (typeof vueApp !== 'undefined') {
    vueApp.booting((vue) => {
        vue.component('v-check-for-updates', CheckForUpdates)
    })
}
