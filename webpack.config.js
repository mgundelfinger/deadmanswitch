// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
const path = require('path')
const webpackConfig = require('@nextcloud/webpack-vue-config')

const buildMode = process.env.NODE_ENV
const isDev = buildMode === 'development'
webpackConfig.devtool = isDev ? 'cheap-source-map' : 'source-map'

webpackConfig.stats = {
	colors: true,
	modules: false,
}

const appId = 'deadmanswitch'
webpackConfig.entry = {
	main: { import: path.join(__dirname, 'src', 'mainScript.js'), filename: appId + '-mainScript.js' },
	checkIn: { import: path.join(__dirname, 'src', 'checkInScript.js'), filename: appId + '-checkInScript.js' },
}

module.exports = webpackConfig
