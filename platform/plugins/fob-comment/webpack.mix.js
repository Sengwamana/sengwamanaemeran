const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/plugins/${directory}`
const dist = `public/vendor/core/plugins/${directory}`

mix
    .sass(`${source}/resources/sass/comment.scss`, `${dist}/css`)
    .js(`${source}/resources/js/comment.js`, `${dist}/js`)

if (mix.inProduction()) {
    mix.copy(`${dist}/css/comment.css`, `${source}/public/css`)
    mix.copy(`${dist}/js/comment.js`, `${source}/public/js`)
}
