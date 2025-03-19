<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://sociality.gr
 * @since      1.0.0
 *
 * @package    Mydata_Connector
 * @subpackage Mydata_Connector/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Mydata_Connector
 * @subpackage Mydata_Connector/includes
 * @author     Sociality Coop <contact@sociality.gr>
 */
class Mydata_Connector {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Mydata_Connector_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Defines the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MYDATA_CONNECTOR_VERSION' ) ) {
			$this->version = MYDATA_CONNECTOR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'mydata-connector';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Mydata_Connector_Loader. Orchestrates the hooks of the plugin.
	 * - Mydata_Connector_i18n. Defines internationalization functionality.
	 * - Mydata_Connector_Admin. Defines all hooks for the admin area.
	 * - Mydata_Connector_Public. Defines all hooks for the public side of the site.
	 *
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mydata-connector-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mydata-connector-i18n.php';

		/**
		 * The class provides helpers to the public public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mydata-connector-helpers.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mydata-connector-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mydata-connector-public.php';

		$this->loader = new Mydata_Connector_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Mydata_Connector_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Mydata_Connector_Admin( $this->get_plugin_name(), $this->get_version() );

		//Add plugin option page in wp-admin
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_options_page' );
		$this->loader->add_action( 'admin_init',  $plugin_admin, 'init_plugin_options_page' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Mydata_Connector_Public( $this->get_plugin_name(), $this->get_version() );

		//Add action to send invoice when requested by wcpdf
		$this->loader->add_action( 'wpo_wcpdf_before_document', $plugin_public,  'mydata_connector_send_invoice', 10, 2);
		//Add MARK to PDF Invoice
		$this->loader->add_action( 'wpo_wcpdf_after_order_data', $plugin_public,  'mydata_connector_add_mark', 10, 2);
		//Add QR to PDF Invoice
		$this->loader->add_action( 'wpo_wcpdf_after_order_details', $plugin_public,  'mydata_connector_add_qr', 10, 2);
		//Change PDF Invoice Title to Receipt
		$this->loader->add_filter( 'wpo_wcpdf_document_title', $plugin_public,  'mydata_connector_change_title', 10, 2);


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Mydata_Connector_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
