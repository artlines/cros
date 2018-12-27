const defaultConfig = require('./webpack.default.config');
const webpack = require('webpack');

module.exports = {
    ...defaultConfig,
    mode: 'development',

    plugins: [
        new webpack.HotModuleReplacementPlugin()
    ],

    devServer: {
        historyApiFallback: false,
        hot: true,
        contentBase: './build'
    }
};