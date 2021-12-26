const path = require('path');
const { VueLoaderPlugin } = require("vue-loader");
const Dotenv = require('dotenv-webpack');

module.exports = {
  entry: './src/front/index.js',
  mode: 'development',
  output: {
    path: path.resolve(__dirname, "public/static/js"),
    filename: "bundle.js"
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        loader: "babel-loader",
      },
      {
        test: /\.css$/,
        use: [
          'style-loader',
          'css-loader',
          ]
      },
      {
        test: /\.vue$/,
        loader: "vue-loader"
      }
    ],
  },
  plugins: [
    new VueLoaderPlugin(),
    new Dotenv()
  ],
  resolve: {
    extensions: ['*', '.js', '.vue', '.json'],
    alias: {
      '@': path.resolve('src/front'),
    }
  }
}
