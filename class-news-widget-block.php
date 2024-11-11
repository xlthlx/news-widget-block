<?php
/**
 * Set up the plugin.
 *
 * @package News_Widget_Block
 */

namespace News\Widget\Block;

/**
 * Class definition.
 */
class News_Widget_Block {

	/**
	 * A static reference to track the single instance of this class.
	 *
	 * @var object
	 */
	private static object $instance;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Actions and filters.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'plugins_loaded', array( $this, 'load_plugin' ) );
		add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );
	}

	/**
	 * Method used to provide a single instance of this class.
	 *
	 * @return News_Widget_Block|null
	 */
	public static function get_instance(): News_Widget_Block|null {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Load plugin files.
	 *
	 * @return void
	 */
	public function load_plugin(): void {

		require_once NWB__PLUGIN_PATH . 'blocks/class-manage-blocks.php';

		if ( is_admin() ) {
			require_once NWB__PLUGIN_PATH . 'admin/class-options-page.php';
		}
	}

	/**
	 * Load translations.
	 *
	 * @return void
	 */
	public function load_text_domain(): void {
		load_plugin_textdomain( 'digital-catalogue', false, NWB__PLUGIN_PATH . 'languages/' );
	}

	/**
	 * Plugin activated.
	 *
	 * @return void
	 */
	public function plugin_activate(): void {
		// TODO.
	}

	/**
	 * Plugin deactivated.
	 *
	 * @return void
	 */
	public function plugin_deactivate(): void {
		// TODO.
	}

	/**
	 * Plugin uninstall.
	 *
	 * @return void
	 */
	public static function plugin_uninstall(): void {
		// TODO.
	}
}
