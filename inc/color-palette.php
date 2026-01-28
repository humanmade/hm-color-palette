<?php
/**
 * Functionality related to color palette meta data for posts.
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
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_color_palette_css' );
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
	
	// Add version number to bust cache when palettes change
	$palette_version = get_option( 'color_palette_version', 0 );
	$script_version = $asset['version'] . '.' . $palette_version;

	wp_enqueue_script(
		'hm-color-palette-editor',
		HM_COLOR_PALETTE_URL . 'build/index.js',
		$asset['dependencies'],
		$script_version,
		true
	);

	wp_enqueue_style(
		'hm-color-palette-editor',
		HM_COLOR_PALETTE_URL . 'build/index.css',
		[],
		$script_version
	);
}

/**
 * Retrieve the color palette configuration.
 * Custom palettes can be added via filter or theme JSON file.
 */
function get_config() {
	// Get default palettes from JSON file.
	$config_file = HM_COLOR_PALETTE_PATH . 'src/color-palette.json';
	$default_palettes = [];
	
	if ( file_exists( $config_file ) ) {
		$default_palettes = json_decode(
			file_get_contents( $config_file ),
			true
		);
	}
	
	// Check for theme override file
	$theme_palettes = [];
	$theme_palette_file = get_stylesheet_directory() . '/color-palettes.json';
	if ( file_exists( $theme_palette_file ) ) {
		$theme_palettes = json_decode(
			file_get_contents( $theme_palette_file ),
			true
		);
	}
	
	// Merge theme palettes with defaults (theme palettes override defaults)
	$palettes = array_merge( $default_palettes, $theme_palettes );
	
	/**
	 * Filter the color palettes available in the editor.
	 * 
	 * @param array $palettes Array of color palette configurations.
	 */
	return apply_filters( 'hm_color_palette_options', $palettes );
}

/**
 * Get color palette config by slug.
 *
 * @param string $slug Slug of palette to look up.
 * @return []|null Color palette config, if found.
 */
function get_palette_by_slug( $slug ) {
	$config = get_config();
	return $config[ $slug ]['palette'] ?? null;
}

/**
 * Gets the color palette inline styles.
 *
 * @param string $color_palette_slug  Slug of the selected color palette.
 * @param string $element             The element to add the styles to.
 * @return string The inline styles string.
 */
function get_color_palette_css( $color_palette_slug, $element = null ) {

	// Decode the color palette JSON data.
	$color_palette = get_palette_by_slug( $color_palette_slug );

	// Only proceed if we have color palette data.
	if ( empty( $color_palette ) ) {
		return;
	}

	$color_palette_inline_css = "
		--primary-page-color-bright: {$color_palette['color_bright']};
		--primary-page-color-text: {$color_palette['color_text']};
		--primary-page-color-ui: {$color_palette['color_ui']};
		--primary-page-color-reverse-background: {$color_palette['color_reverse_background']};
		--primary-page-color-reverse-text: {$color_palette['color_reverse_text']};
		--primary-page-color-reverse-ui: {$color_palette['color_reverse_ui']};
	";

	// Generate the CSS to set the CSS variables based on the color palette data.
	if ( $element ) {
		return $element . " {\r\n" . $color_palette_inline_css . "}\r\n";
	} else {
		return $color_palette_inline_css;
	}
}

/**
 * Enqueues the color palette CSS to the document.
 *
 * This is used to set the CSS variables for the color palette
 * based on the color palette meta data that is set for the post,
 * on the front-end and back-end.
 *
 * @return void
 */
function enqueue_color_palette_css() : void {
	// Don't run on AJAX, cron, or REST requests
	if ( wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}
	
	// Get the current post ID based on whether it's a front-end or back-end request.
	$post_id = is_admin() && isset( $_GET['post'] ) ? intval( $_GET['post'] ) : get_the_ID();

	// Only proceed if we're on a single post, page or editing a post/page in the admin area.
	if ( empty( $post_id ) || ( ! is_admin() && ! is_single() && ! is_page() ) ) {
		return;
	}

	// Get the color palette meta data from the post.
	$color_palette = get_post_meta( $post_id, 'document_color_palette', true );

	// Only proceed if we have color palette meta data.
	if ( empty( $color_palette ) ) {
		return;
	}

	// Use inline style or register a handle if needed
	wp_register_style( 'hm-color-palette', false );
	wp_enqueue_style( 'hm-color-palette' );
	wp_add_inline_style( 'hm-color-palette', get_color_palette_css( $color_palette, 'body' ) );
}

/**
 * Adds the color as a body class.
 *
 * @param string $classes The current body classes.
 * @return string The new body classes.
 */
function add_color_body_class( $classes ) {
	// Get the color palette meta data from the post.
	$color_palette = get_post_meta( get_the_id(), 'document_color_palette', true );

	// Only proceed if we have color palette meta data.
	if ( empty( $color_palette ) ) {
		return $classes;
	}

	$current_color_class = 'has-' . $color_palette . '-color';

	return array_merge( $classes, [ esc_attr( $current_color_class ) ] );
}
