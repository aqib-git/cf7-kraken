<?php
/**
 * Plugin Name: Contact Form 7 Kraken
 * Plugin URI:  http://codecrud.com/contact-form-7-kraken
 * Description: Integrate your Contact Form 7 with Slack and more are comming...
 * Version:     1.0.0
 * Author:      CodeCrud
 * Author URI:  https://codecrud.com/
 * Text Domain: cf7_kraken
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /includes/languages
 *
 * @package cf7_kraken
 * @author  CodeCrud
 * @license GPL-2.0+
 * @copyright  2019, CodeCrud
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// If class `Jet_Elements` doesn't exists yet.
if ( ! class_exists( 'CF7_Kraken' ) ) {

	/**
	 * Sets up and initializes the plugin.
	 */
	class CF7_Kraken {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

        /**
         * The plugin name.
         *
         * @since 1.0.0
         * @access public
         *
         * @var string
         */
        private $plugin_name;

		/**
		 * Holder for base plugin URL
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_url;

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		private $plugin_version;

		/**
		 * Holder for base plugin path
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
        private $plugin_path;

        /**
         * The plugin assets URL.
         *
         * @since 1.0.0
         * @access private
         *
         * @var string
         */
        private $plugin_assets_url;

		/**
		 * The minimum Contact Form 7 version number required.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @var string
		 */
        public static $minimum_cf7_version = '5.1.1';

        /**
         * Modules.
         *
         * @since 1.0.0
         * @access public
         *
         * @var object
         */
        public $modules = [];

        /**
         * Disables class cloning and throw an error on object clone.
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object. Therefore, we don't want the object to be cloned.
         *
         * @access public
         * @since 1.0.0
         */
        public function __clone() {
            // Cloning instances of the class is forbidden.
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'cf7_kraken' ), '1.0.0' );
        }

        /**
         * Disables unserializing of the class.
         *
         * @access public
         * @since 1.0.0
         */
        public function __wakeup() {
            // Unserializing instances of the class is forbidden.
            _doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'cf7_kraken' ), '1.0.0' );
        }

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		private function __construct() {
            add_action( 'plugins_loaded', [ $this, 'check_cf7_version' ] );
			add_action( 'init', [ $this, 'i18n' ] );
			add_filter( 'wpcf7_posted_data', [ $this, 'submit_handler' ]);
        }

        /**
         * Load plugin textdomain.
         *
         * @since 1.0.0
         */
        public function i18n() {
            load_plugin_textdomain( 'cf7_kraken', false, $this->plugin_path . 'includes/languages' );
        }

		/**
		 * Returns plugin version
		 *
		 * @return string
		 */
		public function plugin_version() {
			return $this->plugin_version;
        }

        /**
		 * Returns path to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_path( $path = null ) {
			return $this->plugin_path . $path;
        }

		/**
		 * Returns url to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_url( $path = null ) {
			return $this->plugin_url . $path;
        }

        /**
		 * Returns path to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_assets_url( $path = null ) {
			return $this->plugin_assets_url . $path;
        }

		/**
		 * Show required plugins notice.
		 *
		 * @return void
		 */
		public function check_cf7_version() {
            if ( ! cf7k_utils::is_plugin_installed( 'contact-form-7' ) ) {
                if ( current_user_can( 'install_plugins' ) ) {
                    add_action( 'admin_notices', [ $this, 'admin_notice_missing_cf7_plugin' ] );
                }
                // don't go further.
                return;
            }

			// Check if Contact Form 7 is installed and active.
			if ( ! class_exists( 'WPCF7' ) ) {
				if ( current_user_can( 'activate_plugins' ) ) {
					add_action( 'admin_notices', [ $this, 'admin_notice_inactive_cf7_plugin' ] );
				}
				// don't go further.
				return;
			}

			// Check for the minimum required Contact Form 7 version.
			if ( ! version_compare( WPCF7_VERSION, self::$minimum_cf7_version, '>=' ) ) {
				if ( current_user_can( 'update_plugins' ) ) {
					add_action(
						'admin_notices',
						[ $this, 'admin_notice_minimum_cf7_version' ]
					);
				}
				// don't go further.
				return;
            }

            $this->init();
        }


        /**
		 * Initialize plugin.
		 *
		 * @return void
		 */
		public function init() {
            $this->define_constants();
            $this->add_hooks();

            require_once $this->plugin_path . 'admin/admin.php';
        }

        /**
         * Displays notice on the admin dashboard if Elementor is not installed.
         *
         * @since 1.0.0
         * @access public
         */
        public function admin_notice_missing_cf7_plugin() {
            if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security
                unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security
            }

            $message = sprintf(
                '<span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">'
                /* translators: 1: Plugin name 2: Elementor */
                . esc_html__( '%1$s requires %2$s plugin to be installed and activated.', 'raven' )
                . '</span>',
                '<strong>' . esc_html__( 'Kraken', 'cf7_kraken' ) . '</strong>',
                '<strong>' . esc_html__( 'Contact Form 7', 'cf7_kraken' ) . '</strong>'
            );

            $install_link = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=contact-form-7' ), 'install-plugin_contact-form-7' );

            $message .= sprintf(
                '<span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">' .
                '<a class="button-primary" href="%1$s">%2$s</a></span>',
                $install_link, esc_html__( 'Install Contact Form 7 Now', 'raven' )
            );

            printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
        }

        /**
         * Displays notice on the admin dashboard if Contact Form 7 is not active.
         *
         * @since 1.0.0
         * @access public
         */
        public function admin_notice_inactive_cf7_plugin() {
            if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security
                unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security
            }

            $message = sprintf(
                '<span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">'
                /* translators: 1: Plugin name 2: Elementor */
                . esc_html__( '%1$s requires %2$s plugin to be activated.', 'raven' )
                . '</span>',
                '<strong>' . esc_html__( 'Kraken', 'cf7_kraken' ) . '</strong>',
                '<strong>' . esc_html__( 'Contact Form 7', 'cf7_kraken' ) . '</strong>'
            );

            $plugin          = 'contact-form-7/wp-contact-form-7.php';
            $activation_link = wp_nonce_url( sprintf( self_admin_url( 'plugins.php?action=activate&plugin=%1$s&plugin_status=all&paged=1&s' ), $plugin ), 'activate-plugin_' . $plugin );

            $message .= sprintf(
                '<span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">' .
                '<a class="button-primary" href="%1$s">%2$s</a></span>',
                $activation_link, esc_html__( 'Activate Contact Form 7 Now', 'raven' )
            );

            printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
        }

        /**
         * Displays notice on the admin dashboard if Contact Form 7 version is lower than the
         * required minimum.
         *
         * @since 1.0.0
         * @access public
         */
        public function admin_notice_minimum_cf7_version() {
            if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security
                unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security
            }

            $message = sprintf(
                '<span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">'
                /* translators: 1: Plugin name 2: Contact Form 7 */
                . esc_html__( '%1$s requires version %3$s or greater of %2$s plugin.', 'raven' )
                . '</span>',
                '<strong>' . esc_html__( 'Kraken', 'cf7_kraken' ) . '</strong>',
                '<strong>' . esc_html__( 'Contact Form 7', 'cf7_kraken' ) . '</strong>',
                self::$minimum_cf7_version
            );

            $file_path   = 'contact-form-7/wp-contact-form-7.php';
            $update_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );

            $message .= sprintf(
                '<span style="display: block; margin: 0.5em 0.5em 0 0; clear: both;">' .
                '<a class="button-primary" href="%1$s">%2$s</a></span>',
                $update_link, esc_html__( 'Update Contact Form 7 Now', 'raven' )
            );

            printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
        }

        /**
         * Defines constants used by the plugin.
         *
         * @since 1.0.0
         * @access private
         */
        private function define_constants() {
            $plugin_data = get_file_data( __FILE__, array( 'Plugin Name', 'Version' ), 'cf7_kraken' );

            $this->plugin_name       = array_shift( $plugin_data );
            $this->plugin_version    = array_shift( $plugin_data );
            $this->plugin_path       = trailingslashit( plugin_dir_path( __FILE__ ) );
            $this->plugin_url        = trailingslashit( plugin_dir_url( __FILE__ ) );
            $this->plugin_assets_url = trailingslashit( $this->plugin_url . 'assets' );
        }

        /**
         * Adds required hooks.
         *
         * @since 1.0.0
         * @access private
         */
        private function add_hooks() {
            add_action( 'init', [ $this, 'register_modules' ] );
        }

        /**
         * Register modules.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_modules() {
            $modules = glob( trailingslashit( $this->plugin_path ) . 'includes/modules/*', GLOB_ONLYDIR );

            foreach ( $modules as $module ) {
				$module_name = pathinfo( $module, PATHINFO_BASENAME );

                $class_name = str_replace( '-', ' ', $module_name );
                $class_name = str_replace( ' ', '_', ucwords( $class_name ) );
                $class_name = 'CF7_Kraken_' . $class_name . '_Module';

				require_once $this->plugin_path . 'includes/modules/' . $module_name . '/module.php';

				$this->modules[ $module_name ] = new $class_name();
            }
		}

		/**
		 * Capture Contact Form 7 post data.
		 *
		 * @param [type] $response
		 * @return void
		 */
		public function submit_handler( $response ) {
			$posts = get_posts( [
				'post_type'  => 'cf7k_integrations',
				'numberpost' => -1,
				'meta_key'   => 'cf7_id',
				'meta_value' => $response['_wpcf7'],
			] );

			foreach ( $posts as $post ) {
				$integrations = get_post_meta( $post->ID, 'integrations', true );
				$modules      = [];

				foreach ( $this->modules as $module_name => $module ) {
					if ( in_array( $module_name, $integrations, true ) ) {
						$modules[] = $module;
					}
				}

				$data = cf7k_utils()::get_cf7_data( $response );

				foreach ( $modules as $module ) {
					if ( ! $module->handler( $data ) ) {
						break;
					}
				}
			}

			return $response;
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function activation() {
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function deactivation() {
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}
	}
}

if ( ! function_exists( 'cf7k_init' ) ) {
	/**
	 * Returns instanse of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function cf7k_init() {
		return CF7_Kraken::get_instance();
	}
}

cf7k_init();

if ( ! function_exists( 'cf7k_utils' ) ) {
	require_once cf7k_init()->plugin_path() . 'includes/utils.php';

	/**
	 * Returns instanse of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function cf7k_utils() {
		return CF7K_Utils::class;
	}
}

cf7k_utils();
