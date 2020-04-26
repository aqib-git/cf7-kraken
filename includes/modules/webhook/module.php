<?php
/**
 * Adds webhook integration.
 *
 * @package cf7_kraken
 * @since NEXT
 */

defined( 'ABSPATH' ) || die();

/**
 * CF7 Kraken Webhook Integration module class.
 *
 * @since NEXT
 */
class CF7_Kraken_Webhook_Module {

	/**
	 * Module name.
	 *
	 * @since NEXT
	 *
	 * @return string
	 */
	public function get_name() {
		return 'webhook';
	}

	/**
	 * Module title.
	 *
	 * @since NEXT
	 * @access public
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Webhook', 'cf7-kraken' );
	}

	/**
	 * Constructor.
	 *
	 * @since NEXT
	 * @access public
	 */
	public function __construct() {
		$this->add_hooks();
	}

	/**
	 * Register metaboxes.
	 *
	 * @since NEXT
	 * @access public
	 */
	public function add_hooks() {
		$plugin = cf7k_init();

		$plugin->load_files( [
			'modules/mailchimp/classes/mailchimp-helper',
		] );

		add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
	}

	/**
	 * Register metaboxes.
	 *
	 * @since NEXT
	 * @access public
	 */
	public function register_meta_boxes() {
		add_meta_box(
			'cf7k_webhook_integration_metabox',
			__( 'Webhook Settings', 'cf7-kraken' ),
			[ $this, 'webhook_integration_metabox_cb' ],
			'cf7k_integrations',
			'normal',
			'default'
		);
	}

	/**
	 * Metabox callback.
	 *
	 * @since NEXT
	 * @access public
	 *
	 * @return void
	 */
	public function webhook_integration_metabox_cb() {
		ob_start();

		include_once __DIR__ . '/settings-metabox.php';

		ob_end_flush();
	}

	/**
	 * Contact Form 7 submit hook.
	 *
	 * @since NEXT
	 * @access public
	 *
	 * @param integer $post_id Post Id.
	 * @param array   $form_fields Form Fields.
	 *
	 * @return boolean
	 */
	public function handler( $post_id, array $form_fields ) {
		$settings = get_post_meta( $post_id, 'webhook', true );

		if ( empty( $settings['webhook_url'] ) ) {
			return;
		}

		if ( empty( $settings['field_mapping'] ) ) {
			return;
		}

		$field_mapping = json_decode( $settings['field_mapping'], true );

		if ( ! empty( $field_mapping ) ) {
			$form_fields = $this->map_fields( $field_mapping, $form_fields );
		}

		try {
			$response = wp_remote_post( $settings['webhook_url'], [
				'timeout' => 60,
				'body' => $form_fields,
			] );
		} catch (Exception $e) {
			return false;
		}

		return true;
	}

	/**
	 * Map form fields to Mailchimp fields.
	 *
	 * @since NEXT
	 * @access protected
	 * @static
	 *
	 * @param array $field_mapping Fields Mapping.
	 * @param array $form_fields Form Fields.
	 * @return array
	 */
	protected function map_fields( $field_mapping, $form_fields ) {
		foreach ( $field_mapping as $map_item ) {
			$merge_field = $map_item['merge_field'];
			$form_field  = $map_item['form_field'];

			if ( empty( $merge_field ) || empty( $form_field ) ) {
				continue;
			}

			if ( ! isset( $form_fields[ $form_field ] ) ) {
				continue;
			}

			$form_fields[ $merge_field ] = $form_fields[ $form_field ];

			unset( $form_fields[ $form_field ] );
		}

		return $form_fields;
	}
}
