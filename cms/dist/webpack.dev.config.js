const defaultConfig = require('./webpack.default.config');
const webpack = require('webpack');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const path = require('path');

module.exports = {
    ...defaultConfig,

    mode: 'development',
    devtool: 'eval-source-map',
    output: {
        path: path.resolve(__dirname),
        filename: 'bundle.min.js',
        publicPath: '/cms/'
    },
    devServer: {
        hot: true,
        port: '5505',
        host: '127.0.0.1',
        index: 'index.html',
        public: 'https://cros-eugene.nag.how',
        overlay: {
            warnings: true,
            errors: true
        },
        compress: true,
        publicPath: '/cms/',
        contentBase: './',
        allowedHosts: ['.nag.how'],
        disableHostCheck: true,
        historyApiFallback: true,
    },

    plugins: [
        ...defaultConfig.plugins,
        new webpack.HotModuleReplacementPlugin(),
        new HtmlWebpackPlugin({
            template: './dist/index.template.html'
        })
    ]
};