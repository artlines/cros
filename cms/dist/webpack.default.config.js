
module.exports = {
    entry: './src/index.jsx',
    module: {
        rules: [
            {
                test: /\.(js|jsx)$/,
                loader: 'babel-loader',
                options: {
                    presets: ['@babel/react', '@babel/env'],
                    plugins: ['@babel/plugin-proposal-class-properties']
                }
            }
        ]
    },
    resolve: {
        modules: ['node_modules', 'src'],
        extensions: ['.js', '.jsx']
    },
    plugins: []
};