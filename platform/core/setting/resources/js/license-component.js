import LicenseForm from './components/LicenseForm.vue'

if (typeof vueApp !== 'undefined') {
    vueApp.booting((vue) => {
        vue.component('v-license-form', LicenseForm)
    })
}
