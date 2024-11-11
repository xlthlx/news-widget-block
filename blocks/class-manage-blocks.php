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
		register_block_type(
			__DIR__ . '/build/us-news/',
			array(
				'api_version'     => 2,
				'render_callback' => array( $this, 'us_news_render_callback' ),

			)
		);
	}

	/**
	 * Callback for dynamic frontend rendering of US News details.
	 *
	 * @param array  $block_attributes The block attributes.
	 * @param string $content The block content.
	 *
	 * @return string
	 */
	public function us_news_render_callback( $block_attributes, $content ): string {

		$post_meta   = get_post_meta( get_the_ID(), 'news_number', true );
		$nwb_api_key = get_option( 'nwb-api-key' );
		$output      = '';

		if ( empty( $post_meta ) ) {
			return '<div ' . get_block_wrapper_attributes() . '>
				<p>' . __( 'Please select a number of news items.', 'news-widget-block' ) . '</p>
			</div>';
		}

		if ( '' === $nwb_api_key ) {
			return '<div ' . get_block_wrapper_attributes() . '>
				<p>' . __( 'Please insert you News API key in the ', 'news-widget-block' ) . '
				<a href="/wp-admin/admin.php?page=news-widget-block-settings">' . __( 'Settings page', 'news-widget-block' ) . '</a>
				</p>
			</div>';
		}

		$news = $this->get_news();
		if ( ! empty( $news ) ) {
			$output .= '<ul>';

			foreach ( $news as $article ) {
				$output .= '<li class="wp-block-post">

							<div class="wp-block-group">
								<h2 class="wp-block-post-title">
									<a href="' . $article['url'] . '" target="_blank">
									' . $article['title'] . '
									</a>
								</h2>

								<div class="wp-block-post-excerpt">
									<p class="wp-block-post-excerpt__excerpt">
									' . $article['description'] . '
									</p>
									<p class="wp-block-post-excerpt__more-text">
										<a class="wp-block-post-excerpt__more-link" href="' . $article['url'] . '" target="_blank">
											' . __( 'Read more', 'news-widget-block' ) . '
										</a>
									</p>
								</div>

								<div class="wp-block-post-date">
									<time datetime="' . gmdate( 'c', strtotime( $article['date'] ) ) . '">' . $article['date'] . '</time>
								</div>
							</div>

					</li>';
			}
			$output .= '</ul>';
		} else {
			$output = '<p>' . __( 'No News here.', 'news-widget-block' ) . '</p>';
		}

		if ( '' !== $output ) {
			$content = '<div ' . get_block_wrapper_attributes() . '>' . $output . '</div>';
		}

		return $content;
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
		$news_number = get_post_meta( get_the_ID(), 'news_number', true );
		$items       = array();

		if ( '' !== $nwb_api_key && ! empty( $news_number ) ) {

			$url  = 'https://newsapi.org/v2/top-headlines';
			$args = array(
				'headers' => array(
					'Content-Type' => 'application/json',
					'X-Api-Key'    => $nwb_api_key,
					'user-agent'   => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:130.0) Gecko/20100101 Firefox/130.0',
				),
				'body' => array(
					'pageSize' => $news_number,
					'country'  => 'us',
				),
			);

			$news = $this->remote_get( $url, $args );

			if ( isset( $news['status'] ) ) {
				if ( 'ok' === $news['status'] ) {
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
				'description'       => 'Number of News',
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

Manage_Blocks::get_instance();
