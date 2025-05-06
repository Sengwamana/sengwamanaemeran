const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/themes/${directory}`
const dist = `public/themes/${directory}`

mix
    .sass(`${source}/assets/sass/theme.scss`, `${dist}/css`)
    .css(`${source}/public/css/main.css`, `${dist}/css`)
    .js(`${source}/assets/js/theme.js`, `${dist}/js`)
    .js(`${source}/public/js/main.js`, `${dist}/js`)

if (mix.inProduction()) {
    mix
        .copy(`${dist}/css/theme.css`, `${source}/public/css`)
        .copy(`${dist}/css/main.css`, `${source}/public/css`)
        .copy(`${dist}/js/theme.js`, `${source}/public/js`)
        .copy(`${dist}/js/main.js`, `${source}/public/js`)
}
