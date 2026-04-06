<?php
/**
 * HM Color Palette Plugin.
 **
 * @link              https://humanmade.com
 * @since             1.0.0
 * @package           color-palette
 *
 * Plugin Name:       HM Color Palette
 * Plugin URI:        https://humanmade.com
 * Description:       Adds color palette meta data functionality for posts and pages with customizable color schemes.
 * Version:           1.0.0
 * Author:            Human Made Limited
 * Author URI:        https://humanmade.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       color-palette
 * Domain Path:       /languages
 */

namespace HM_Color_Palette;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin constants.
define( 'HM_COLOR_PALETTE_PATH', plugin_dir_path( __FILE__ ) );
define( 'HM_COLOR_PALETTE_URL', plugin_dir_url( __FILE__ ) );

// Load color palette functionality.
require_once HM_COLOR_PALETTE_PATH . 'inc/color-palette.php';
require_once HM_COLOR_PALETTE_PATH . 'inc/admin-page.php';

// Bootstrap the plugin.
Color_Palette\bootstrap();
Admin\bootstrap();
