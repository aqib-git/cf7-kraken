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

    public function get_name() {
		return 'mailchimp';
	}

	public function get_title() {
		return __( 'Mailchimp', 'cf7_kraken' );
    }

    public function __construct() {
        $this->add_hooks();
    }

    public function add_hooks() {
        add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
    }

    /**
     * Register meta box(es).
     */
    public function register_meta_boxes() {
        add_meta_box(
            'cf7k_mailchimp_integration_metabox',
            __( 'Mailchimp Settings', 'cf7_kraken' ),
            [ $this, 'mailchimp_integration_metabox_cb'],
            'cf7k_integrations',
            'normal',
            'high'
        );
    }

    public function mailchimp_integration_metabox_cb() {
       ob_start();
       include_once __DIR__ . '/settings_metabox.php';
       ob_end_flush();
    }

    public function handler( array $data ) {

		return true;
    }
}

