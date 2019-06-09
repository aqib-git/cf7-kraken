<?php
/**
 * Controls admin area.
 *
 * @package cf7_kraken
 * @since 1.0.0
 */

if ( ! class_exists( 'CF7_kraken_Admin' ) ) {
    /**
     * Admin class.
     *
     * @since 1.0.0
     */
    class CF7_Kraken_Admin {

		/**
         * The plugin admin assets URL.
         *
         * @since 1.0.0
         * @access private
         *
         * @var string
         */
		private $admin_assets_url;

        /**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
        private static $instance = null;

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
		public function __construct() {
			$this->admin_assets_url = trailingslashit( cf7_kraken()->plugin_url() . '/admin/assets' );

            $this->add_admin_hooks();
        }

        private function add_admin_hooks() {
            add_action( 'admin_menu', [ $this, 'register_admin_welcome_page' ] );
            add_action( 'init', [ $this, 'register_integration_cpt' ] );
			add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ] );
        }

        public function register_admin_welcome_page() {
            add_menu_page(
                __( 'CF7 Kraken', 'cf7_kraken' ),
                __( 'CF7 Kraken', 'cf7_kraken' ),
                'manage_options',
                'cf7_kraken',
                function () {
                    ob_start();
                    include_once cf7_kraken()->plugin_path() . 'admin/views/welcome.php';
                    ob_end_flush();
                },
                'dashicons-rest-api',
                20
            );
        }

        public function register_integration_cpt() {
            $labels = array(
                'name'               => _x( 'Integrations', 'post type general name', 'cf7_kraken' ),
                'singular_name'      => _x( 'Integration', 'post type singular name', 'cf7_kraken' ),
                'menu_name'          => _x( 'Integrations', 'admin menu', 'cf7_kraken' ),
                'name_admin_bar'     => _x( 'Integration', 'add new on admin bar', 'cf7_kraken' ),
                'add_new'            => _x( 'Add New', 'integration', 'cf7_kraken' ),
                'add_new_item'       => __( 'Add New Integration', 'cf7_kraken' ),
                'new_item'           => __( 'New Integration', 'cf7_kraken' ),
                'edit_item'          => __( 'Edit Integration', 'cf7_kraken' ),
                'view_item'          => __( 'View Integration', 'cf7_kraken' ),
                'all_items'          => __( 'All Integrations', 'cf7_kraken' ),
                'search_items'       => __( 'Search Integrations', 'cf7_kraken' ),
                'parent_item_colon'  => __( 'Parent Integrations:', 'cf7_kraken' ),
                'not_found'          => __( 'No integrations found.', 'cf7_kraken' ),
                'not_found_in_trash' => __( 'No integrations found in Trash.', 'cf7_kraken' )
            );

            $args = array(
                'labels'             => $labels,
                'description'        => __( 'Description.', 'cf7_kraken' ),
                'public'             => true,
                'publicly_queryable' => true,
                'show_ui'            => true,
                'show_in_menu'       => 'cf7_kraken',
                'query_var'          => true,
                'rewrite'            => array( 'slug' => 'cf7_kraken_integrations' ),
                'capability_type'    => 'post',
                'has_archive'        => true,
                'hierarchical'       => false,
                'menu_position'      => null,
                'supports'           => array( 'title' )
            );

            register_post_type( 'cf7k_integrations', $args );
        }

        /**
         * Register meta box(es).
         *
         * @since 1.0.0
         * @access public
         */
        public function register_meta_boxes() {
            add_meta_box(
                'cf7k_general_settings_metabox',
                __( 'General Settings', 'cf7_kraken' ),
                [ $this, 'general_settings_metabox_cb'],
                'cf7k_integrations',
                'normal',
                'high'
            );
        }

        /**
         * General setting metabox callback.
         *
         * @since 1.0.0
         * @access public
         */
        public function general_settings_metabox_cb() {
            ob_start();
            include_once __DIR__ . '/metaboxes/general_settings.php';
            ob_end_flush();
		}

		/**
         * Register admin assets.
         *
         * @since 1.0.0
         * @access public
         */
        public function register_assets() {
            wp_enqueue_style(
				'cf7-admin-styles',
				$this->admin_assets_url( 'css/main.css' ),
				[],
				cf7_kraken()->plugin_version()
			);
		}

		/**
		 * Returns url to admin assets.
		 *
		 * @param  string $path Path inside admin assets.
		 * @return string
		 */
		public function admin_assets_url( $path = null ) {
			return $this->admin_assets_url . $path;
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

if ( ! function_exists( 'cf7_kraken_admin' ) ) {
	/**
	 * Returns instanse of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function cf7_kraken_admin() {
		return CF7_Kraken_Admin::get_instance();
	}
}

cf7_kraken_admin();
