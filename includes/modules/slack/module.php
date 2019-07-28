<?php
/**
 * Adds slack integration.
 *
 * @package cf7_kraken
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || die();

/**
 * CF7 Kraken Slack Integration module class.
 *
 * @since 1.0.0
 */
class CF7_Kraken_Slack_Module {

	/**
	 * Module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name() {
		return 'slack';
	}

	/**
	 * Module title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Slack', 'cf7_kraken' );
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		$this->add_hooks();
	}

	/**
	 * Register Hooks.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function add_hooks() {
		add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
	}

	/**
	 * Register metaboxes.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * Register meta box(es).
	 */
	public function register_meta_boxes() {
		add_meta_box(
			'cf7k_slack_integration_metabox',
			__( 'Slack Settings', 'cf7_kraken' ),
			[ $this, 'slack_integration_metabox_cb' ],
			'cf7k_integrations',
			'normal',
			'high'
		);
	}

	/**
	 * Metabox callback.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function slack_integration_metabox_cb() {
		ob_start();

		include_once __DIR__ . '/settings-metabox.php';

		ob_end_flush();
	}

	/**
	 * Contact Form 7 submit hook.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param integer $post_id Post Id.
	 * @param array   $form_fields Form Fields.
	 *
	 * @return boolean
	 */
	public function handler( $post_id, array $form_fields ) {

		$settings = get_post_meta( $post_id, 'slack', true );

		if (
			empty( $settings['webhook_url'] ) ||
			false === strpos( $settings['webhook_url'], 'https://hooks.slack.com/services/' )
		) {
			return;
		}

		$payload = [
			'channel' => empty( $settings['channel'] ) ? '' : $settings['channel'],
			'username' => empty( $settings['username'] ) ? '' : $settings['username'],
		];

		$attachment = [
			'text'       => __( 'A new Form Submission has been received.', 'raven' ),
			'title'      => __( 'A new Submission', 'raven' ),
			'color'      => '#007bff',
			'title_link' => '',
			'fallback'   => __( 'A new Form Submission has been received.', 'raven' ),
		];

		if ( ! empty( $settings['title'] ) ) {
			$attachment['title'] = $settings['title'];
		}

		if ( ! empty( $settings['text'] ) ) {
			$attachment['text'] = $settings['text'];
		}

		if ( ! empty( $settings['pretext'] ) ) {
			$attachment['pretext'] = $settings['pretext'];
		}

		if ( ! empty( $settings['webhook_color'] ) ) {
			$attachment['color'] = $settings['webhook_color'];
		}

		if ( ! empty( $settings['fallback_text'] ) ) {
			$attachment['fallback'] = $settings['fallback_text'];
		}

		if ( ! empty( $settings['send_form_data'] ) && 'yes' === $settings['send_form_data'] ) {

			$fields = [];
			foreach ( $form_fields as $field_name => $field_value ) {
				$fields[] = [
					'title' => $field_name ? $field_name : '',
					'value' => $field_value,
					'short' => false,
				];
			}

			$attachment['fields'] = $fields;
		}

		if ( ! empty( $settings['show_footer'] ) && 'yes' === $settings['show_footer'] ) {
			$attachment = array_merge(
				$attachment,
				[
					'footer' => sprintf( __( 'Powered by %s', 'cf7_kraken' ), 'CF7 Kraken' ),
					'footer_icon' => '',
				]
			);
		}

		if ( ! empty( $settings['show_timestamp'] ) && 'yes' === $settings['show_timestamp'] ) {
			$attachment['ts'] = time();
		}

		$payload['attachments'] = [ $attachment ];

		$response = wp_remote_post(
			$settings['webhook_url'],
			[
				'headers' => [
					'Content-Type' => 'application/json',
				],
				'body'    => wp_json_encode( $payload ),
			]
		);

		return true;
	}
}
