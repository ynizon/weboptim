//Creation du fichier composer
//npm init -y
//Ajout des dependances en global
//npm install --save-dev -g path webpack@latest webpack-dev-server@latest webpack-cli webpack-merge-and-include-globally optimize-css-assets-webpack-plugin uglifyjs-webpack-plugin mini-css-extract-plugin css-loader
//Test du projet
//npm i
//Lancement du projet
//webpack

const path = require('path');
const webpack = require("webpack");
const MergeIntoSingleFilePlugin = require('webpack-merge-and-include-globally');
const OptimizeCssAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const UglifyJsPlugin = require("uglifyjs-webpack-plugin");

module.exports = {
  entry: './index.js',
  output: {
    filename: '[name]',
    path: path.resolve(__dirname, 'dist'),
  },
  plugins: [
    new MergeIntoSingleFilePlugin({
      files: {
		  "prebundle.js": [
			//@@JS@@
			path.resolve(__dirname, 'js/__alljs__code.js')
		  ],
		  "prebundle.css": [
			path.resolve(__dirname, 'css/__allcss__import.css'),
			//@@CSS@@
			path.resolve(__dirname, 'css/__allcss__code.css')
		  ]
	  },
	  transform: {
		'prebundle.js': code => require("uglify-js").minify(code).code
	  }
    }),
  ]/*,
  optimization: {
    minimize: true,
	minimizer: [
      new UglifyJsPlugin({
        cache: true,
        parallel: true,
        sourceMap: true // set to true if you want JS source maps
      }),
      new OptimizeCssAssetsPlugin({})
    ]
  }*/
};