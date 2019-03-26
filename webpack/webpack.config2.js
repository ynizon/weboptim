//Creation du fichier composer
//npm init -y
//Ajout des dependances en global
//npm install --save-dev -g path webpack@latest webpack-dev-server@latest webpack-cli webpack-merge-and-include-globally optimize-css-assets-webpack-plugin
//Test du projet
//npm i
//Lancement du projet
//webpack

//Minify le css et renvoie un main.css 

const path = require('path');
const webpack = require("webpack");
const MergeIntoSingleFilePlugin = require('webpack-merge-and-include-globally');
const OptimizeCssAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const UglifyJsPlugin = require("uglifyjs-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssLoader = require("css-loader");

module.exports = {
  entry: {
	  bundle:['./dist/prebundle.css']
  },
  output: {
    path: path.resolve(__dirname, 'dist'),
  },
  plugins: [	
	new MiniCssExtractPlugin(),
    new OptimizeCssAssetsPlugin({
      assetNameRegExp: /\.optimize\.css$/g,
      cssProcessor: require('cssnano'),
      cssProcessorPluginOptions: {
        preset: ['default', { discardComments: { removeAll: true } }],
      },
      canPrint: true
    })
  ],optimization: {
    minimize: true,
	minimizer: [
      new UglifyJsPlugin({
        cache: true,
        parallel: true,
        sourceMap: true // set to true if you want JS source maps
      }),
      new OptimizeCssAssetsPlugin({})
    ]
  },
   module: {
    rules: [
      {
        test: /\.css$/,
        use: [
          {loader: MiniCssExtractPlugin.loader},
          "css-loader"
	    ]
      }
    ]
  }  
};