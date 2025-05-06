const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/core/${directory}`
const dist = `public/vendor/core/core/${directory}`

mix
    .vue()
    .sass(`${source}/resources/sass/core.scss`, `${dist}/css`)
    .sass(`${source}/resources/sass/libraries/select2/select2.scss`, `${dist}/css/libraries`)
    .sass(`${source}/resources/sass/components/error-pages.scss`, `${dist}/css`)
    .sass(`${source}/resources/sass/components/tree-category.scss`, `${dist}/css`)
    .sass(`${source}/resources/sass/components/crop-image.scss`, `${dist}/css`)
    .postCss(`${dist}/css/core.css`, `${dist}/css/core.rtl.css`, [
        require('rtlcss')
    ])
    .postCss(`${dist}/css/libraries/select2.css`, `${dist}/css/libraries/select2.rtl.css`, [
        require('rtlcss')
    ])
    .js(`${source}/resources/js/core-ui.js`, `${dist}/js`)
    .js(`${source}/resources/js/app.js`, `${dist}/js`)
    .js(`${source}/resources/js/core.js`, `${dist}/js`)
    .js(`${source}/resources/js/editor.js`, `${dist}/js`)
    .js(`${source}/resources/js/global-search.js`, `${dist}/js`)
    .js(`${source}/resources/js/license-activation.js`, `${dist}/js`)
    .js(`${source}/resources/js/cache.js`, `${dist}/js`)
    .js(`${source}/resources/js/tags.js`, `${dist}/js`)
    .js(`${source}/resources/js/form/phone-number-field.js`, `${dist}/js`)
    .js(`${source}/resources/js/system-info.js`, `${dist}/js`)
    .js(`${source}/resources/js/tree-category.js`, `${dist}/js`)
    .js(`${source}/resources/js/cleanup.js`, `${dist}/js`)
    .js(`${source}/resources/js/notification.js`, `${dist}/js`)
    .js(`${source}/resources/js/vue-app.js`, `${dist}/js`)
    .js(`${source}/resources/js/repeater-field.js`, `${dist}/js`)
    .js(`${source}/resources/js/system-update.js`, `${dist}/js`)
    .js(`${source}/resources/js/crop-image.js`, `${dist}/js`)
    .copy('node_modules/jquery/dist/jquery.min.js', `${dist}/libraries/jquery.min.js`)
    .copy(
        mix.inProduction() ? './node_modules/vue/dist/vue.global.prod.js' : './node_modules/vue/dist/vue.global.js',
        `${dist}/libraries/vue.global.min.js`
    )

if (mix.inProduction()) {
    mix
        .copy(`${dist}/css/core.css`, `${source}/public/css`)
        .copy(`${dist}/css/libraries/select2.css`, `${source}/public/css/libraries`)
        .copy(`${dist}/css/error-pages.css`, `${source}/public/css`)
        .copy(`${dist}/css/tree-category.css`, `${source}/public/css`)
        .copy(`${dist}/css/crop-image.css`, `${source}/public/css`)
        .copy(`${dist}/css/core.rtl.css`, `${source}/public/css`)
        .copy(`${dist}/css/libraries/select2.rtl.css`, `${source}/public/css/libraries`)
        .copy(`${dist}/js/core-ui.js`, `${source}/public/js`)
        .copy(`${dist}/js/app.js`, `${source}/public/js`)
        .copy(`${dist}/js/core.js`, `${source}/public/js`)
        .copy(`${dist}/js/vue-app.js`, `${source}/public/js`)
        .copy(`${dist}/js/editor.js`, `${source}/public/js`)
        .copy(`${dist}/js/global-search.js`, `${source}/public/js`)
        .copy(`${dist}/js/license-activation.js`, `${source}/public/js`)
        .copy(`${dist}/js/cache.js`, `${source}/public/js`)
        .copy(`${dist}/js/tags.js`, `${source}/public/js`)
        .copy(`${dist}/js/phone-number-field.js`, `${source}/public/js`)
        .copy(`${dist}/js/system-info.js`, `${source}/public/js`)
        .copy(`${dist}/js/repeater-field.js`, `${source}/public/js`)
        .copy(`${dist}/js/tree-category.js`, `${source}/public/js`)
        .copy(`${dist}/js/cleanup.js`, `${source}/public/js`)
        .copy(`${dist}/js/system-update.js`, `${source}/public/js`)
        .copy(`${dist}/js/crop-image.js`, `${source}/public/js`)
        .copy(`${dist}/js/notification.js`, `${source}/public/js`)
        .copy(`${dist}/libraries/jquery.min.js`, `${source}/public/libraries/jquery.min.js`)
        .copy(`${dist}/libraries/vue.global.min.js`, `${source}/public/libraries/vue.global.min.js`)
}
