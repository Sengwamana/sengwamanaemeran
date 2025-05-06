const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/packages/${directory}`
const dist = `public/vendor/core/packages/${directory}`

mix
    .js(`${source}/resources/js/theme-options.js`, `${dist}/js`)
    .js(`${source}/resources/js/theme.js`, `${dist}/js`)
    .js(source + '/resources/js/icons-field.js', dist + '/js')
    .js(source + '/resources/js/toast.js', dist + '/js')
    .sass(`${source}/resources/sass/theme-options.scss`, `${dist}/css`)
    .sass(`${source}/resources/sass/admin-bar.scss`, `${dist}/css`)
    .sass(`${source}/resources/sass/guideline.scss`, `${dist}/css`)

if (mix.inProduction()) {
    mix
        .copy(`${dist}/js/theme-options.js`, `${source}/public/js`)
        .copy(`${dist}/js/theme.js`, `${source}/public/js`)
        .copy(dist + '/js/icons-field.js', source + '/public/js')
        .copy(dist + '/js/toast.js', source + '/public/js')
        .copy(`${dist}/css/theme-options.css`, `${source}/public/css`)
        .copy(`${dist}/css/admin-bar.css`, `${source}/public/css`)
        .copy(`${dist}/css/guideline.css`, `${source}/public/css`)
}
