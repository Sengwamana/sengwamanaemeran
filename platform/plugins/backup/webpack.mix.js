const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/plugins/${directory}`
const dist = `public/vendor/core/plugins/${directory}`

mix
    .js(`${source}/resources/js/backup.js`, `${dist}/js`)
    .sass(`${source}/resources/sass/backup.scss`, `${dist}/css`)

if (mix.inProduction()) {
    mix
        .copy(`${dist}/js/backup.js`, `${source}/public/js`)
        .copy(`${dist}/css/backup.css`, `${source}/public/css`)
}
