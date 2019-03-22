
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
            { test: /\.(png|jpg|woff|woff2|eot|ttf|svg)(\?\S*)?$/, loader: 'url-loader?limit=100000' },
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
    plugins: []
};