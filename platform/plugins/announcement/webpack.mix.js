const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/plugins/${directory}`
const dist = `public/vendor/core/plugins/${directory}`

mix
    .js(`${source}/resources/js/announcement.js`, `${dist}/js`)
    .sass(`${source}/resources/sass/announcement.scss`, `${dist}/css`)

if (mix.inProduction()) {
    mix
        .copy(`${dist}/js/announcement.js`, `${source}/public/js`)
        .copy(`${dist}/css/announcement.css`, `${source}/public/css`)
}
