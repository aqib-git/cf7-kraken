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
class Utils {
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
}
