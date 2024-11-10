<?php
/**
 * Plugin Name:     News Widget Block
 * Plugin URI:      https://github.com/xlthlx/news-widget-block
 * Description:     News Widget Block is a WordPress plugin that adds a custom block with a setting to choose the number of articles to display.
 * Author:          xlthlx <xlthlx@gmail.com>
 * Author URI:      https://piccioni.london
 * Text Domain:     news-widget-block
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         News_Widget_Block
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'NWB__PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'NWB__PLUGIN_URL', plugins_url( '/', __FILE__ ) );

