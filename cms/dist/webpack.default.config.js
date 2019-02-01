const CKEditorWebpackPlugin = require('@ckeditor/ckeditor5-dev-webpack-plugin');
//const {styles} = require('@ckeditor/ckeditor5-dev-utils');

module.exports = {
    entry: './src/index.jsx',
    module: {
        rules: [
            {
                test: /\.(js|jsx)$/,
                exclude: [/@ckeditor.*/],
                loader: 'babel-loader',
                options: {
                    presets: ['@babel/react', '@babel/env'],
                    plugins: ['@babel/plugin-proposal-class-properties']
                }
            },
            { test: /\.(png|woff|woff2|eot|ttf|svg)(\?\S*)?$/, loader: 'url-loader?limit=100000' },
            {
                test: /\.(s*)css$/,
                loader: 'style-loader!css-loader'
            }
        ]
    },
    resolve: {
        modules: ['node_modules', 'src'],
        extensions: ['.js', '.jsx'],
        alias: {

        }
    },
    plugins: [
        new CKEditorWebpackPlugin({
            language: 'ru'
        }),
    ]
};