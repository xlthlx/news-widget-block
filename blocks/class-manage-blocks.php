<?php
/**
 * Manage blocks.
 *
 * @package News_Widget_Block
 */

namespace News\Widget\Block;

/**
 * Class definition.
 */
class Manage_Blocks {

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
		add_action( 'init', array( $this, 'blocks_init' ) );
	}

	/**
	 * Method used to provide a single instance of this class.
	 *
	 * @return Manage_Blocks|null
	 */
	public static function get_instance(): Manage_Blocks|null {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Registers all blocks.
	 *
	 * @return void
	 */
	public function blocks_init(): void {
		register_block_type( __DIR__ . '/build/us-news/' );
	}
}

Manage_Blocks::get_instance();
