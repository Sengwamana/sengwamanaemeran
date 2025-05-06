const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/core/${directory}`
const dist = `public/vendor/core/core/${directory}`

mix.combine([
    `${source}/resources/js/jquery-validation/jquery.validate.js`,
    `${source}/resources/js/phpjs/strlen.js`,
    `${source}/resources/js/phpjs/array_diff.js`,
    `${source}/resources/js/phpjs/strtotime.js`,
    `${source}/resources/js/phpjs/is_numeric.js`,
    `${source}/resources/js/php-date-formatter/php-date-formatter.js`,
    `${source}/resources/js/js-validation.js`,
    `${source}/resources/js/helpers.js`,
    `${source}/resources/js/timezones.js`,
    `${source}/resources/js/validations.js`,
], `${dist}/js/js-validation.js`)

if (mix.inProduction()) {
    // mix.copy(`${dist}/js/js-validation.js`, `${source}/public/js`)
}
