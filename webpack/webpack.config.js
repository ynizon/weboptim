//Creation du fichier composer
//npm init -y
//Ajout des dependances en global
//npm install --save-dev -g path webpack@latest webpack-dev-server@latest webpack-cli webpack-merge-and-include-globally optimize-css-assets-webpack-plugin
//Test du projet
//npm i
//Lancement du projet
//webpack

const path = require('path');
const webpack = require("webpack");
const MergeIntoSingleFilePlugin = require('webpack-merge-and-include-globally');
const OptimizeCSSAssets = require("optimize-css-assets-webpack-plugin");

module.exports = {
  entry: './index.js',
  output: {
    filename: '[name]',
    path: path.resolve(__dirname, 'dist'),
  },
  plugins: [
    new MergeIntoSingleFilePlugin({
      files: {
		  "bundle.js": [
			//@@JS@@
			path.resolve(__dirname, 'js/__alljs__code.js')
		  ],
		  "bundle.css": [
			//@@CSS@@
			path.resolve(__dirname, 'css/__allcs__code.css')
		  ]
	  },
	  transform: {
		'bundle.js': code => require("uglify-js").minify(code).code
	  }
    }),
	new OptimizeCSSAssets()
  ]
};