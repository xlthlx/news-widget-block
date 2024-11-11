<?php
/**
 * General options page.
 *
 * @package News_Widget_Block
 */

namespace News\Widget\Block;

/**
 * Class definition.
 */
class Options_Page {

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
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
	}

	/**
	 * Method used to provide a single instance of this class.
	 *
	 * @return Options_Page|null
	 */
	public static function get_instance(): Options_Page|null {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Custom option and settings.
	 *
	 * @return void
	 */
	public function settings_init(): void {
		register_setting( 'nwb-group', 'nwb-settings' );

		add_settings_section(
			'nwb_section_settings',
			__( 'News Widget Block Settings', 'news-widget-block' ),
			array( $this, 'section_callback' ),
			'nwb-group'
		);

		add_settings_field(
			'nwb_api_key',
			__( 'API key', 'news-widget-block' ),
			array( $this, 'api_key_callback' ),
			'nwb-group',
			'nwb_section_settings'
		);
	}

	/**
	 * Section callback function.
	 *
	 * @param array $args The settings array, defining title, id, callback.
	 */
	public function section_callback( $args ) {
		?>
		<hr class="hr"/>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Insert your API key.', 'news-widget-block' ); ?></p>
		<?php
	}

	/**
	 * API key field callback function.
	 */
	public function api_key_callback() {

		$nwb_api_key = get_option( 'nwb-api-key' );
		?>
		<input type="password" name="nwb-api-key" id="nwb-api-key" value="<?php echo esc_attr( $nwb_api_key ); ?>"/>
		<p class="description">
			<?php
			esc_html_e( 'NEWS API key. You can get it here: ', 'news-widget-block' );
			echo ' <a href="https://newsapi.org/register" target="_blank" title="Register for API key">NEWS API key</a>';
			?>
		</p>
		<?php
	}

	/**
	 * Set up the options page.
	 *
	 * @return void
	 */
	public function add_options_page(): void {
		add_menu_page(
			page_title: __( 'News Widget Block Settings', 'news-widget-block' ),
			menu_title: __( 'NWB Settings', 'news-widget-block' ),
			capability: 'manage_options',
			menu_slug: 'news-widget-block-settings',
			callback: array( $this, 'options_page_callback' )
		);
	}

	/**
	 * Options page callback.
	 *
	 * @return void
	 */
	public function options_page_callback(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Save all options.
		if ( ! empty( $_POST ) && check_admin_referer( 'nwb-update', 'nwb-update' ) ) {
			$options = ( isset( $_POST['nwb-api-key'] ) ) ? sanitize_text_field( wp_unslash( $_POST['nwb-api-key'] ) ) : '';

			if ( false !== get_option( 'nwb-api-key' ) ) {
				update_option( 'nwb-api-key', $options );
			} else {
				add_option( 'nwb-api-key', $options );
			}

			add_settings_error( 'nwb-messages', 'nwb-message', __( 'Settings saved.', 'news-widget-block' ), 'updated' );
		}

		settings_errors( 'nwb-messages' );
		?>
		<div class="wrap">
			<form method="post">
				<?php
				wp_nonce_field( 'nwb-update', 'nwb-update' );
				do_settings_sections( 'nwb-group' );
				submit_button( __( 'Save', 'news-widget-block' ) );
				?>
			</form>
		</div>
		<?php
	}
}

Options_Page::get_instance();
