<?php
/**
 * Color Palette functionality.
 *
 * Handles color palette meta data registration, editor integration,
 * and front-end body class injection for posts and pages.
 *
 * @package HM_Color_Palette
 */

namespace HM_Color_Palette\Color_Palette;

/**
 * Connects namespace methods to hooks and filters.
 *
 * @return void
 */
function bootstrap() : void {
	add_action( 'init', __NAMESPACE__ . '\\register_color_palette_post_meta' );
	add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_editor_assets' );
	add_filter( 'body_class', __NAMESPACE__ . '\\add_color_body_class' );
}

/**
 * Registers the color palette post meta.
 *
 * @return void
 */
function register_color_palette_post_meta() : void {
	register_post_meta( '', 'document_color_palette', [
		'show_in_rest' => true,
		'single' => true,
		'type' => 'string',
	] );
}

/**
 * Enqueue editor assets.
 *
 * @return void
 */
function enqueue_editor_assets() : void {
	$asset_file = HM_COLOR_PALETTE_PATH . 'build/index.asset.php';
	
	if ( ! file_exists( $asset_file ) ) {
		return;
	}

	$asset = require $asset_file;
	$script_version = $asset['version'];

	wp_enqueue_script(
		'hm-color-palette-editor',
		HM_COLOR_PALETTE_URL . 'build/index.js',
		$asset['dependencies'],
		$script_version,
		true
	);

	$theme_colors = get_theme_colors();

	wp_localize_script( 'hm-color-palette-editor', 'themeColors', $theme_colors );

	wp_enqueue_style(
		'hm-color-palette-editor',
		HM_COLOR_PALETTE_URL . 'build/index.css',
		[],
		$script_version
	);
}

/**
 * Retrieve the theme color palette options.
 *
 * @return array Array of theme color palette options.
 */
function get_theme_colors() {
	$theme_data = \WP_Theme_JSON_Resolver::get_merged_data()->get_settings();
	$palette = $theme_data['color']['palette']['theme'] ?? [];

	return apply_filters( 'hm_color_palette_options', $palette );
}

/**
 * Adds the color palette as a body class.
 *
 * Appends a CSS class based on the selected color palette to the body classes.
 *
 * @param array $classes The current body classes.
 * @return array The modified body classes array.
 */
function add_color_body_class( $classes ) {
	$color_palette = get_post_meta( get_the_id(), 'document_color_palette', true );

	if ( empty( $color_palette ) ) {
		return $classes;
	}

	$current_color_class = 'has-' . $color_palette . '-color-palette';

	return array_merge( $classes, [ esc_attr( $current_color_class ) ] );
}
