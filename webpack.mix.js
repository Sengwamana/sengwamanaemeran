const mix = require('laravel-mix')
const glob = require('glob')

mix.options({
    processCssUrls: false,
    clearConsole: true,
    terser: {
        extractComments: false,
    },
    manifest: false,
})

mix.webpackConfig({
    stats: {
        children: false,
    },
    externals: {
        vue: 'Vue',
    },
})

mix.disableSuccessNotifications()

mix.vue()

let buildPaths = []

function pushToPath(path, type) {
    buildPaths.push(`${type}/${path === 'true' ? '*' : path}`)
}

const types = [
    {
        key: 'npm_config_theme',
        name: 'themes',
    },
    {
        key: 'npm_config_plugin',
        name: 'plugins',
    },
    {
        key: 'npm_config_package',
        name: 'packages',
    },
    {
        key: 'npm_config_core',
        name: 'core',
    },
]

for (const assetType of types) {
    const assetPath = process.env[assetType.key]

    if (! assetPath) {
        continue
    }

    pushToPath(assetPath, assetType.name)
}

if (! buildPaths.length) {
    buildPaths = ['*/*']
}

buildPaths.forEach(buildPath => glob.sync(`./platform/${buildPath}/webpack.mix.js`).forEach(item => require(__dirname + '/' + item)))
