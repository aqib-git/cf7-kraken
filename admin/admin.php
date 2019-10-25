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
			$this->add_admin_hooks();
		}

		/**
		 * Register admin hooks.
		 *
		 * @since 1.0.0
		 * @access private
		 *
		 * @return void
		 */
		private function add_admin_hooks() {
			add_action( 'admin_menu', [ $this, 'register_admin_welcome_page' ] );
			add_action( 'init', [ $this, 'register_integration_cpt' ] );
			add_action( 'add_meta_boxes', [ $this, 'register_meta_boxes' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ] );
			add_action( 'save_post_cf7k_integrations', [ $this, 'save_cpt_meta_boxes' ] );
		}

		/**
		 * Register plugin admin page.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return void
		 */
		public function register_admin_welcome_page() {
			add_menu_page(
				__( 'CF7 Kraken', 'cf7_kraken' ),
				__( 'CF7 Kraken', 'cf7_kraken' ),
				'manage_options',
				'cf7_kraken',
				function () {
					ob_start();
					include_once cf7k_init()->plugin_path() . 'admin/views/welcome.php';
					ob_end_flush();
				},
				'dashicons-rest-api',
				20
			);
		}

		/**
		 * Register integration custom post type.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @return void
		 */
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
			include_once __DIR__ . '/metaboxes/general-settings.php';
		}

		/**
		 * Save meta box(es).
		 *
		 * @since 1.0.0
		 *
		 * @param string $post_id Post Id.
		 *
		 * @access public
		 */
		public function save_cpt_meta_boxes( $post_id ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			$data = filter_input_array( INPUT_POST );

			if ( ! isset( $data['cf7k_cpt_meta_box_nonce'] ) ) {
				return;
			}

			if ( ! wp_verify_nonce( $data['cf7k_cpt_meta_box_nonce'], 'cf7k_cpt_meta_box_nonce' ) ) {
				return;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			if ( isset( $data['integrations'] ) ) {
				update_post_meta( $post_id, 'integrations', $data['integrations'] );
			}

			if ( isset( $data['cf7_id'] ) ) {
				update_post_meta( $post_id, 'cf7_id', $data['cf7_id'] );
			}

			if ( isset( $data['slack'] ) ) {
				update_post_meta( $post_id, 'slack', $data['slack'] );
			}
		}

		/**
		 * Register admin assets.
		 *
		 * @since 1.0.0
		 * @access public
		 */
		public function register_assets() {
			$plugin = cf7k_init();

			wp_enqueue_style(
				'cf7-select2-styles',
				'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css',
				[],
				$plugin->plugin_version()
			);
			wp_enqueue_script(
				'cf7-select2-script',
				'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js',
				[ 'jquery' ],
				$plugin->plugin_version()
			);

			wp_enqueue_style(
				'cf7-admin-styles',
				$plugin->plugin_assets_url( 'css/admin' . CF7K_MIN_CSS . '.css' ),
				[],
				$plugin->plugin_version()
			);

			wp_enqueue_style( 'wp-color-picker' );

			wp_enqueue_script(
				'cf7-admin-script',
				$plugin->plugin_assets_url( 'js/admin' . CF7K_MIN_JS . '.js' ),
				[ 'jquery', 'wp-color-picker', 'cf7-select2-script' ],
				$plugin->plugin_version(),
				true
			);
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
