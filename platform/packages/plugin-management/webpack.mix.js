const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/packages/${directory}`
const dist = `public/vendor/core/packages/${directory}`

mix
    .js(`${source}/resources/js/plugin.js`, `${dist}/js`)
    .js(`${source}/resources/js/marketplace.js`, `${dist}/js`)

if (mix.inProduction()) {
    mix
        .copy(`${dist}/js/plugin.js`, `${source}/public/js`)
        .copy(`${dist}/js/marketplace.js`, `${source}/public/js`)
}
