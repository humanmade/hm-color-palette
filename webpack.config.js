const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		// Main library entry
		'index': path.resolve( process.cwd(), 'src', 'index.js' ),
		// Test block entry
		'blocks/test-block/editor': path.resolve( process.cwd(), 'src', 'blocks', 'test-block', 'editor.js' ),
	},
	output: {
		...defaultConfig.output,
		path: path.resolve( process.cwd(), 'build' ),
	},
};
