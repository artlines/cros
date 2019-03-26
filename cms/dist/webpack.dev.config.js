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
        port: '5509',
        host: '127.0.0.1',
        index: 'index.html',
        public: 'https://cros-artur.nag.how',
        overlay: {
            warnings: true,
            errors: true
        },
        compress: true,
        contentBase: './',
        allowedHosts: ['.nag.how'],
        disableHostCheck: true,
        historyApiFallback: {
            index: '/cms/'
        },
    },

    plugins: [
        ...defaultConfig.plugins,
        new webpack.HotModuleReplacementPlugin(),
        new HtmlWebpackPlugin({
            template: './dist/index.template.html'
        })
    ]
};