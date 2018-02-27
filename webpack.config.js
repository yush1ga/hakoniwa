//
const webpack = require('webpack');
const path = require('path');
const globule = require('globule');
const CopyPlugin = require('copy-webpack-plugin');
const ExtractTextPlugin = require('extract-text-webpack-plugin');



const dir = {
	src : path.resolve(__dirname, 'public/src'),
	dest: path.resolve(__dirname, 'public')
};

const convertExt = {
	sass: 'css',
	ts: 'js'
};

let files = {};
Object.keys(convertExt).forEach(from => {
	const to = convertExt[from];
	globule.find([`**/*.${from}`, `!**/_*.${from}`], {cwd: dir.src}).forEach(filename => {
		files[filename.replace(new RegExp(`.${from}$`), `.${to}`)] = path.join(dir.src, filename);
	});
});

const sassLoader = [
	{
		loader: 'css-loader',
		options: {
			minimize: true
		}
	},
	{
		loader: 'postcss-loader',
		options: {
			ident: 'postcss',
			plugins: () => [require('autoprefixer')()]
		}
	},
	'sass-loader'
];

const tsLoader = [
	'awesome-typescript-loader',
	{
		loader: 'tslint-loader',
		options: {
			configFile: 'tslint.json',
			typeCheck: true
		}
	}
];

const config = {
	context: dir.src,
	target: 'web',
	entry: files,
	output: {
		filename: '[name]',
		jsonpFunction: 'vendor',
		path: dir.dest
	},
	module: {
		rules: [
			{
				test: /\.sass$/,
				oneOf: [
					{
						resourceQuery: /inline/,
						use: sassLoader
					},
					{
						use: ExtractTextPlugin.extract(sassLoader)
					}
				]
			},
			{
				test: /\.tsx?$/,
				exclude: /node_modules(?!\/webpack-dev-server)/,
				use: tsLoader
			}
		]
	},
	resolve: {
		modules: [
			'node_modules',
			path.resolve(__dirname, 'public/src')
		]
	},
	plugins: [
		new ExtractTextPlugin('[name]'),
		new CopyPlugin(
			[{from: {glob: '**/*', dot: true}}],
			{ignore: Object.keys(convertExt).map(ext => `*.${ext}`)}
		),
		new webpack.DefinePlugin({
			'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV)
		}),
	],
	devServer: {
		contentBase: dir.dest,
		port: 8000,
		hot: true
	}
};

module.exports = env => {
	if(env && env.production) {
		config.plugins = config.plugins.concat([
			new webpack.optimize.DedupePlugin(),
			new webpack.optimize.UglifyJsPlugin(),
			new webpack.optimize.OccurrenceOrderPlugin(true),
			new webpack.optimize.AggressiveMergingPlugin()
		]);
	};
	return config
};
