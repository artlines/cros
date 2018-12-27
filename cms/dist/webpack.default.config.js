const HtmlWebpackPlugin = require('html-webpack-plugin)';

module.exports = {
    entry: '../src/index.jsx',

    module: {
        rules: [
            { test: /\.(js|jsx)$/, loader: 'babel-loader' }
        ]
    },

    resolve: {
        modules: ['node_modules'],
        extensions: ['js', 'jsx']
    },
    plugins: [
        new HtmlWebpackPlugin({
            template: './index.template.html'
        })
    ]
};