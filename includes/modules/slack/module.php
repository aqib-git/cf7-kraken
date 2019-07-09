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

    public function get_name() {
		return 'slack';
	}

	public function get_title() {
		return __( 'Slack', 'cf7_kraken' );
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
            'cf7k_slack_integration_metabox',
            __( 'Slack Settings', 'cf7_kraken' ),
            [ $this, 'slack_integration_metabox_cb'],
            'cf7k_integrations',
            'normal',
            'high'
        );
    }

    public function slack_integration_metabox_cb() {
       ob_start();
       include_once __DIR__ . '/settings_metabox.php';
       ob_end_flush();
    }

    public function handler( $response ) {
		error_log('slack handler');
		error_log(print_r($response, true));
		$data = cf7k_util::get_cf7_data( $response );
		return true;
    }
}

