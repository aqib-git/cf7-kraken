<?php
/**
 * Mailchimp helpers.
 *
 * @package cf7_kraken
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || die();

/**
 * Mailchimp Helper Class.
 *
 * @since 1.0.0
 */
class CF7_Kraken_Mailchimp_Helper {

	/**
	 * Get list of audience
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return mixed
	 */
	public static function get_audience() {
		$plugin = cf7k_init();

		$plugin->load_files( [
			'modules/mailchimp/classes/mailchimp-api',
		] );

		if ( empty( $_POST['api_key'] ) ) { // phpcs:ignore WordPress.Security
			wp_send_json_error( 'api_key field is empty' );
		}

		$api_key = wp_unslash( $_POST['api_key'] ); // phpcs:ignore WordPress.Security

		try {
			$handler = new CF7_Kraken_MailChimp_API( $api_key );

			$lists = $handler->get( 'lists?count=999' );

			if ( $handler->success() ) {
				wp_send_json_success( $lists );
			}

			return wp_send_json_error( $handler->getLastError() );
		} catch ( \Exception $e ) {

			return wp_send_json_error( $e->getMessage() );
		}
	}


	/**
	 * Get list of group.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return mixed
	 */
	public static function get_audience_groups() {
		$plugin = cf7k_init();

		$plugin->load_files( [
			'modules/mailchimp/classes/mailchimp-api',
		] );

		if ( empty( $_POST['api_key'] ) ) { // phpcs:ignore WordPress.Security
			wp_send_json_error( 'api_key field is empty' );
		}

		if ( empty( $_POST['list_id'] ) ) { // phpcs:ignore WordPress.Security
			wp_send_json_error( 'list_id field is empty' );
		}

		$api_key = wp_unslash( $_POST['api_key'] ); // phpcs:ignore WordPress.Security
		$list_id = wp_unslash( $_POST['list_id'] ); // phpcs:ignore WordPress.Security

		try {
			$handler = new CF7_Kraken_MailChimp_API( $api_key );
			$groups  = [];

			$categories = $handler->get( 'lists/' . $list_id . '/interest-categories?count=999' );
			if ( ! empty( $categories['categories'] ) ) {
				foreach ( $categories['categories'] as $category ) {
					$interests = $handler->get( 'lists/' . $list_id . '/interest-categories/' . $category['id'] . '/interests?count=999' );

					foreach ( $interests['interests'] as $interest ) {
						$groups[ $interest['id'] ] = $category['title'] . ' - ' . $interest['name'];
					}
				}
			}

			if ( $handler->success() ) {
				wp_send_json_success( $groups );
			}

			return wp_send_json_error( $handler->getLastError() );
		} catch ( \Exception $e ) {

			return wp_send_json_error( $e->getMessage() );
		}
	}

	/**
	 * Get list of merge fields.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return mixed
	 */
	public static function get_audience_fields() {
		$plugin = cf7k_init();

		$plugin->load_files( [
			'modules/mailchimp/classes/mailchimp-api',
		] );

		if ( empty( $_POST['api_key'] ) ) { // phpcs:ignore WordPress.Security
			wp_send_json_error( 'api_key field is empty' );
		}

		if ( empty( $_POST['list_id'] ) ) { // phpcs:ignore WordPress.Security
			wp_send_json_error( 'list_id field is empty' );
		}

		$api_key = $_POST['api_key']; // phpcs:ignore WordPress.Security
		$list_id = $_POST['list_id']; // phpcs:ignore WordPress.Security
		$handler = new CF7_Kraken_MailChimp_API( $api_key );

		$merge_fields = $handler->get( 'lists/' . $list_id . '/merge-fields?count=999' );

		$fields = [
			[
				'remote_label' => 'Email',
				'remote_type' => 'email',
				'remote_tag' => 'EMAIL',
				'remote_required' => true,
			],
		];

		if ( ! empty( $merge_fields['merge_fields'] ) ) {
			foreach ( $merge_fields['merge_fields'] as $field ) {
				$fields[] = [
					'remote_label' => $field['name'],
					'remote_type' => $field['type'],
					'remote_tag' => $field['tag'],
					'remote_required' => $field['required'],
				];
			}
		}

		wp_send_json_success( $fields );
	}
}
