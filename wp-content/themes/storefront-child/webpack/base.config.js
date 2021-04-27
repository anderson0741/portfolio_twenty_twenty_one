const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
  context: path.resolve(__dirname, '../src'),
  entry: {
    app: './js/index.js',
    login: './js/login.js'
  },
  output: {
    publicPath: '/wp-content/themes/storefront-child/dist/'
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "css/[name].css",
    })
  ],
  module: {
    rules: [
      {
        test: /\.(css|scss)$/,
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          {
            loader: 'postcss-loader' },
          {
            /* for ~slick-carousel/slick/slick-theme.scss */
            loader: 'resolve-url-loader', options: { removeCR: true } },
          {
            /* for resolve-url-loader:
                source maps must be enabled on any preceding loader */
            loader: 'sass-loader?sourceMap' }
        ]
      },
      {
        test: /\.(png|svg|jpg|gif)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              outputPath: 'images/'
            }
          }
        ]
      },
      {
        test: /\.js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: [
              [
                "@babel/preset-env",
                {
                  "useBuiltIns": "usage",
                  corejs: '3',
                }
              ]
            ],
            plugins: ['@babel/plugin-proposal-class-properties']
          }
        }
      }
    ]
  }
}
