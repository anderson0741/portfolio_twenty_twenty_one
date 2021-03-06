const baseConfig = require('./base.config.js');
const merge = require('webpack-merge');
const TerserJSPlugin = require('terser-webpack-plugin');
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");

module.exports = merge(baseConfig, {
    mode: 'production',
    optimization: {
        minimizer: [
          new  TerserJSPlugin({}),
          new OptimizeCSSAssetsPlugin({})
        ]
    }
});
