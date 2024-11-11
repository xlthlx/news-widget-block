<?php
/**
 * Plugin Name:     News Widget Block
 * Plugin URI:      https://github.com/xlthlx/news-widget-block
 * Description:     News Widget Block is a WordPress plugin that adds a custom block with a setting to choose the number of articles to display.
 * Author:          xlthlx <xlthlx@gmail.com>
 * Author URI:      https://piccioni.london
 * License:         GPLv3+
 * License URI:     https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:     news-widget-block
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         News_Widget_Block
 */

use News\Widget\Block\News_Widget_Block;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'NWB__PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'NWB__PLUGIN_URL', plugins_url( '/', __FILE__ ) );

require_once NWB__PLUGIN_PATH . 'class-news-widget-block.php';

$news_widget_block = News_Widget_Block::get_instance();

register_activation_hook( __FILE__, array( $news_widget_block, 'plugin_activate' ) );
register_deactivation_hook( __FILE__, array( $news_widget_block, 'plugin_deactivate' ) );
register_uninstall_hook( __FILE__, '$news_widget_block::plugin_uninstall' );

