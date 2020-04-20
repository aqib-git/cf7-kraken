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
		$settings = get_post_meta( $post_id, 'mailchimp', true );

		if ( empty( $settings['api_key'] ) ) {
			return;
		}

		if ( empty( $settings['audience'] ) ) {
			return;
		}

		if ( empty( $settings['field_mapping'] ) ) {
			return;
		}

		$email_field   = [];
		$field_mapping = json_decode( $settings['field_mapping'], true );

		foreach ( $field_mapping as $item ) {
			if ( 'EMAIL' === $item['merge_field'] ) {
				$email_field = $item;

				break;
			}
		}

		if ( empty( $email_field ) || empty( $form_fields[ $email_field['form_field'] ] ) ) {
			return;
		}

		try {
			$api_key            = $settings['api_key'];
			$audience           = $settings['audience'];
			$handler            = new CF7_Kraken_MailChimp_API( $api_key );
			$double_optin       = ! empty( $settings['double_optin'] );
			$field_mapping      = $this->map_fields( $field_mapping, $form_fields );
			$audience_interests = [];

			if (
				! empty( $settings['groups'] ) &&
				is_array( $settings['groups'] )
			) {
				$audience_groups = $settings['groups'];

				foreach ( $audience_groups as $audience_group ) {
					$audience_interests[ $audience_group ] = true;
				}
			}

			$handler->get( 'lists/' . $audience );

			if ( ! $handler->success() ) {
				return;
			}

			$post_data = [
				'email_address' => $field_mapping['email_address'],
				'status' => $double_optin ? 'pending' : 'subscribed',
			];

			if ( ! empty( $field_mapping['merge_fields'] ) ) {
				$post_data['merge_fields'] = $field_mapping['merge_fields'];
			}

			if ( ! empty( $audience_interests ) ) {
				$post_data['interests'] = $audience_interests;
			}

			$response = $handler->put(
				'lists/' . $audience . '/members/' . md5( strtolower( $field_mapping['email_address'] ) ),
				$post_data
			);

		} catch ( \Exception $e ) {
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
		$mapping = [];

		foreach ( $field_mapping as $map_item ) {
			$merge_field = $map_item['merge_field'];
			$form_field  = $map_item['form_field'];

			if ( empty( $merge_field ) || empty( $form_field ) ) {
				continue;
			}

			if ( empty( $form_fields[ $form_field ] ) ) {
				continue;
			}

			$value = $form_fields[ $form_field ];

			if ( 'EMAIL' === $merge_field ) {
				$mapping['email_address'] = $value;
			} else {
				$mapping['merge_fields'][ $merge_field ] = $value;
			}
		}

		if ( empty( $mapping['email_address'] ) ) {
			return [];
		}

		return $mapping;
	}
}
