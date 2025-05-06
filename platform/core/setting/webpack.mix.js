const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/core/${directory}`
const dist = `public/vendor/core/core/${directory}`

mix
    .js(`${source}/resources/js/admin-email.js`, `${dist}/js`)
    .js(`${source}/resources/js/email.js`, `${dist}/js`)
    .js(`${source}/resources/js/email-template.js`, `${dist}/js`)
    .js(`${source}/resources/js/media.js`, `${dist}/js`)
    .js(`${source}/resources/js/license-component.js`, `${dist}/js`)
    .sass(`${source}/resources/sass/admin-email.scss`, `${dist}/css`)

if (mix.inProduction()) {
    mix
        .copy(`${dist}/js/admin-email.js`, `${source}/public/js`)
        .copy(`${dist}/js/email.js`, `${source}/public/js`)
        .copy(`${dist}/js/email-template.js`, `${source}/public/js`)
        .copy(`${dist}/js/media.js`, `${source}/public/js`)
        .copy(`${dist}/js/license-component.js`, `${source}/public/js`)
        .copy(`${dist}/css/admin-email.css`, `${source}/public/css`)
}
