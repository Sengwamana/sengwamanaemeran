const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/core/${directory}`
const dist = `public/vendor/core/core/${directory}`

mix
    .sass(`${source}/resources/sass/media.scss`, `${dist}/css`)
    .js(`${source}/resources/js/media.js`, `${dist}/js`)
    .js(`${source}/resources/js/integrate.js`, `${dist}/js`)

if (mix.inProduction()) {
    mix
        .copy(`${dist}/js/media.js`, `${source}/public/js`)
        .copy(`${dist}/js/integrate.js`, `${source}/public/js`)
        .copy(`${dist}/css/media.css`, `${source}/public/css`)
}
