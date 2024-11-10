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
		add_action( 'init', array( $this, 'blocks_register_fields' ) );
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
	 * Gets and decode an API response.
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

	/**
	 * Gets the news.
	 *
	 * @return array
	 */
	public function get_news(): array {

		$nwb_api_key = get_option( 'nwb-api-key' );
		$items       = array();

		if ( '' !== $nwb_api_key ) {

			$url  = 'https://newsapi.org/v2/top-headlines';
			$args = array(
				'headers' => array(
					'Content-Type' => 'application/json',
					'X-Api-Key'    => $nwb_api_key,
					'user-agent'   => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:130.0) Gecko/20100101 Firefox/130.0',
				),
				'body' => array(
					'pageSize' => 3,
					'country'  => 'us',
				),
			);

			$news = $this->remote_get( $url, $args );

			if ( isset( $news['status'] ) ) {
				if ( 'ok' === $news['status'] ) {
					// $items = $news;
					$i = 0;
					foreach ( $news['articles'] as $article ) {
						$items[ $i ]['title']       = $article['title'];
						$items[ $i ]['description'] = isset( $article['description'] ) ? $article['description'] : '';
						$items[ $i ]['content']     = isset( $article['content'] ) ? $article['content'] : '';
						$items[ $i ]['author']      = isset( $article['author'] ) ? $article['author'] : '';
						$items[ $i ]['source']      = isset( $article['source']['name'] ) ? $article['source']['name'] : '';
						$items[ $i ]['url']         = isset( $article['url'] ) ? $article['url'] : '';
						$items[ $i ]['date']        = isset( $article['publishedAt'] ) ? gmdate( 'd/m/Y H:i', strtotime( $article['publishedAt'] ) ) : '';
						$items[ $i ]['img']         = isset( $article['urlToImage'] ) ? $article['urlToImage'] : '';
						$i++;
					}
				}
				if ( 'error' === $news['status'] ) {
					error_log( 'Response Error: ' . print_r( $news, true ) );
				}
			}
		}

		return $items;
	}

	/**
	 * Register custom meta fields for all blocks.
	 *
	 * @return void
	 */
	public function blocks_register_fields(): void {

		register_post_meta(
			'',
			'news_number',
			array(
				'description'       => 'Nember of News',
				'show_in_rest'      => true,
				'type'              => 'string',
				'single'            => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}

$block = Manage_Blocks::get_instance();
