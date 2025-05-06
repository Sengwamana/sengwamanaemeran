const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/core/${directory}`
const dist = `public/vendor/core/core/${directory}`

mix
    .js(`${source}/resources/js/table.js`, `${dist}/js`)
    .js(`${source}/resources/js/filter.js`, `${dist}/js`)
    .sass(`${source}/resources/sass/table.scss`, `${dist}/css`)

if (mix.inProduction()) {
    mix
        .copy(`${dist}/js/table.js`, `${source}/public/js`)
        .copy(`${dist}/js/filter.js`, `${source}/public/js`)
        .copy(`${dist}/css/table.css`, `${source}/public/css`)
}
