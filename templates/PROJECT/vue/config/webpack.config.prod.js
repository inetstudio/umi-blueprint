'use strict';

const webpack                  = require('webpack');
const merge                    = require('webpack-merge');
const OptimizeCSSAssetsPlugin  = require('optimize-css-assets-webpack-plugin');
const MiniCSSExtractPlugin     = require('mini-css-extract-plugin');
const UglifyJSPlugin           = require('uglifyjs-webpack-plugin');
const CompressionPlugin        = require('compression-webpack-plugin');
const helpers                  = require('./helpers');
const commonConfig             = require('./webpack.config.common');
const isProd                   = process.env.NODE_ENV === 'production';
const environment              = isProd ? require('./env/prod.env') : require('./env/dev.env');

const webpackConfig = merge(commonConfig, {
    mode: 'production',
    output: {
        path: helpers.root('/'),
        publicPath: '/',
        filename: 'public/js/[hash].js',
        chunkFilename: 'public/js/[id].[hash].chunk.js'
    },
    optimization: {
        runtimeChunk: 'single',
        minimizer: [
            new OptimizeCSSAssetsPlugin({
                cssProcessorPluginOptions: {
                    preset: [ 'default', { discardComments: { removeAll: true } } ],
                }
            }),
            new UglifyJSPlugin({
                cache: true,
                parallel: true,
                sourceMap: !isProd
            })
        ],
        splitChunks: {
            chunks: 'all',
            maxInitialRequests: Infinity,
            minSize: 0,
            cacheGroups: {
                vendor: {
                    test: /[\\/]node_modules[\\/]/,
                    name (module) {
                        const packageName = module.context.match(/[\\/]node_modules[\\/](.*?)([\\/]|$)/)[1];
                        return `npm.${packageName.replace('@', '')}`;
                    }
                }
            }
        }
    },
    plugins: [
        new webpack.EnvironmentPlugin(environment),
        new MiniCSSExtractPlugin({
            filename: 'css/[name].[hash].css',
            chunkFilename: 'css/[id].[hash].css'
        }),
        new webpack.HashedModuleIdsPlugin()
    ]
});

if (!isProd) {
    webpackConfig.devtool = 'source-map';

    if (process.env.npm_config_report) {
        const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
        webpackConfig.plugins.push(new BundleAnalyzerPlugin());
    }
}

module.exports = webpackConfig;
