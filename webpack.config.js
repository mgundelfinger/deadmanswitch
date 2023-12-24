const path = require('path')
// we extend the Nextcloud webpack config
const webpackConfig = require('@nextcloud/webpack-vue-config')

const buildMode = process.env.NODE_ENV
const isDev = buildMode === 'development'
webpackConfig.devtool = isDev ? 'cheap-source-map' : 'source-map'

webpackConfig.stats = {
	colors: true,
	modules: false,
}

// this is the rule with which the /src/mainScript.js file will be compiled to /js/deadmanswitch-mainScript.js
const appId = 'deadmanswitch'
webpackConfig.entry = {
	main: { import: path.join(__dirname, 'src', 'mainScript.js'), filename: appId + '-mainScript.js' },
}

module.exports = webpackConfig
