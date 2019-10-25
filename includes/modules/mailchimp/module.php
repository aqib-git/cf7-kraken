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
		add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
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
		return true;
	}
}
