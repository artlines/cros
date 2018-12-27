const defaultConfig = require('./webpack.default.config');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const path = require('path');

module.exports = {
    ...defaultConfig,

    mode: 'production',
    output: {
        path: path.resolve(__dirname, '../public/pre_build'),
        publicPath: '/cms/build',
        filename: 'bundle.[hash].min.js'
    },

    plugins: [
        ...defaultConfig.plugins,
        new HtmlWebpackPlugin({
            template: './dist/index.template.html',
            minify: true
        })
    ]
};