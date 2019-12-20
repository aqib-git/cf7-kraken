<?php
/**
 * Adds mailchimp integration.
 *
 * @package cf7_kraken
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || die();

/**
 * CF7 Kraken Mailchimp Integration module class.
 *
 * @since 1.0.0
 */
class CF7_Kraken_Mailchimp_Module {

	/**
	 * Module name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_name() {
		return 'mailchimp';
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
		return __( 'Mailchimp', 'cf7-kraken' );
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
	 * Register metaboxes.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function add_hooks() {
		$plugin = cf7k_init();

		$plugin->load_files( [
			'modules/mailchimp/classes/mailchimp-helper',
		] );

		add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
		add_action( 'wp_ajax_cf7k_mailchimp_get_audience', [ CF7_Kraken_Mailchimp_Helper::class, 'get_audience' ] );
		add_action( 'wp_ajax_cf7k_mailchimp_get_audience_groups', [ CF7_Kraken_Mailchimp_Helper::class, 'get_audience_groups' ] );
		add_action( 'wp_ajax_cf7k_mailchimp_get_audience_fields', [ CF7_Kraken_Mailchimp_Helper::class, 'get_audience_fields' ] );
	}

	/**
	 * Register metaboxes.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_meta_boxes() {
		add_meta_box(
			'cf7k_mailchimp_integration_metabox',
			__( 'Mailchimp Settings', 'cf7-kraken' ),
			[ $this, 'mailchimp_integration_metabox_cb'],
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
	 *
	 * @return void
	 */
	public function mailchimp_integration_metabox_cb() {
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
		cf7k_init()->load_files( [
			'modules/mailchimp/classes/mailchimp-api',
		] );

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
				'email_address' => $field_mapping[ 'email_address' ],
				'status' => $double_optin ? 'pending' : 'subscribed',
			];

			if ( ! empty( $field_mapping['merge_fields'] ) ) {
				$post_data['merge_fields'] = $field_mapping['merge_fields'];
			}

			if ( ! empty( $audience_interests ) ) {
				$post_data['interests'] = $audience_interests;
			}

			$handler->put(
				'lists/' . $audience . '/members/' . md5( strtolower( $field_mapping[ 'email_address' ] ) ),
				$post_data
			);
		} catch ( \Exception $e ) {}
	}

	/**
	 * Map form fields to Mailchimp fields.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @static
	 *
	 * @param array $field_mapping Fields Mapping.
	 * @param array $form_fields Form Fields.
	 * @return void
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
