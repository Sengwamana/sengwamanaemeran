const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/plugins/${directory}`
const dist = `public/vendor/core/plugins/${directory}`

mix
    .sass(`${source}/resources/sass/contact.scss`, `${dist}/css`)
    .sass(`${source}/resources/sass/contact-public.scss`, `${dist}/css`)
    .js(`${source}/resources/js/contact.js`, `${dist}/js`)
    .js(`${source}/resources/js/custom-field.js`, `${dist}/js`)
    .js(`${source}/resources/js/contact-public.js`, `${dist}/js`)

if (mix.inProduction()) {
    mix
        .copy(`${dist}/css/contact.css`, `${source}/public/css`)
        .copy(`${dist}/css/contact-public.css`, `${source}/public/css`)
        .copy(`${dist}/js/contact.js`, `${source}/public/js`)
        .copy(`${dist}/js/custom-field.js`, `${source}/public/js`)
        .copy(`${dist}/js/contact-public.js`, `${source}/public/js`)
}
