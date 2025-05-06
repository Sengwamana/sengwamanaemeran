const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/core/${directory}`
const dist = `public/vendor/core/core/${directory}`

mix
    .js(`${source}/resources/js/dashboard.js`, `${dist}/js`)
    .js(`${source}/resources/js/check-for-updates.js`, `${dist}/js`)

if (mix.inProduction()) {
    mix
        .copy(`${dist}/js/dashboard.js`, `${source}/public/js`)
        .copy(`${dist}/js/check-for-updates.js`, `${source}/public/js`)
}
