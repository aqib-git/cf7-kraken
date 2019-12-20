<?php
/**
 * Adds utils.
 *
 * @package cf7_kraken
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || die();

/**
 * CF7 Kraken utils class.
 *
 * @since 1.0.0
 */
class CF7K_Utils {
	/**
	 * Wrapper around the core WP get_plugins function, making sure it's actually available.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string $plugin_folder Optional. Relative path to single plugin folder.
	 *
	 * @return array Array of installed plugins with plugin information.
	 */
	public static function get_plugins( $plugin_folder = '' ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return get_plugins( $plugin_folder );
	}

	/**
	 * Checks if a plugin is installed. Does not take must-use plugins into account.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param string $slug Required. Plugin slug.
	 *
	 * @return bool True if installed, false otherwise.
	 */
	public static function is_plugin_installed( $slug ) {
		return ! empty( self::get_plugins( '/' . $slug ) );
	}

	/**
	 * Get Filtered Contact Form data.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param array $response Form data.
	 * @return array
	 */
	public static function get_cf7_data( array $response ) {
		if ( empty( $response['_wpcf7'] ) ) {
			return [];
		}

		$fields = WPCF7_ContactForm::get_instance( $response['_wpcf7']  )
			->scan_form_tags();

		$field_types = self::get_cf7_field_types();

		$data = [ 'form_id' => $response['_wpcf7'] ];

		foreach ( $response as $name => $value ) {
			foreach ( $fields as $field ) {
				if ( $field->name !== $name ) {
					continue;
				}

				if( ! in_array( $field->basetype, $field_types, true ) ) {
					continue;
				}

				if ( is_array( $value ) ) {
					$data[ $name ] = implode( ', ', $value );

					continue;
				}

				$data[ $name ] = $value;
			}
		}

		return $data;
	}

	/**
	 * Supported CF7 field types.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return void
	 */
	public static function get_cf7_field_types() {
		return [
			'text',
			'email',
			'tel',
			'number',
			'url',
			'date',
			'textarea',
			'select',
			'radio',
			'checkbox',
			'acceptance',
			'quiz',
		];
	}
}
