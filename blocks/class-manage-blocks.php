<?php
/**
 * Manage blocks.
 *
 * @package News_Widget_Block
 */

namespace News\Widget\Block;

use JsonException;

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

	/**
	 * Get and decode an API response.
	 *
	 * @param string $url The REST API url.
	 * @param array  $args The REST API args.
	 *
	 * @return array|string
	 */
	public function remote_get( $url, $args = array() ): array|string {

		$data = array();

		if ( ! empty( $args ) ) {
			$response = wp_remote_get( esc_url_raw( $url ), $args );
		} else {
			$response = wp_remote_get( $url );
		}

		if ( is_wp_error( $response ) ) {
			error_log( 'WP Error: ' . $response->get_error_message() );
		} else {
			$body = wp_remote_retrieve_body( $response );

			try {
				$data = json_decode( $body, true, 512, JSON_THROW_ON_ERROR );
			} catch ( JsonException $error ) {
				error_log( 'Json Decode Error: ' . $error->getMessage() . $pages );
			}
		}

		return $data;
	}
}

Manage_Blocks::get_instance();
