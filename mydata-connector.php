<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://sociality.gr
 * @since             1.0.0
 * @package           Mydata_Connector
 *
 * @wordpress-plugin
 * Plugin Name:       MyData Connector for WooCommerce
 * Plugin URI:        https://github.com/socialityCoop/wc-mydata-connector
 * Description:       A plugin that integrates PDF Invoices & Packing Slips for WooCommerce with myData greek tax system
 * Version:           1.0.0
 * Author:            Sociality Coop
 * Author URI:        https://sociality.gr/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       mydata-connector
 * Domain Path:       /languages
 * Requires Plugins: woocommerce-pdf-invoices-packing-slips
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MYDATA_CONNECTOR_VERSION', '1.0.0' );

/**
 * Main plugin file
 */
define( 'WC_MYDATA_CONNECTOR_FILE', __FILE__ );

/**
 * This code adds composer depedentecies
 */
require_once(plugin_dir_path(__FILE__) . '/vendor/autoload.php');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mydata-connector-activator.php
 */
function activate_mydata_connector() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mydata-connector-activator.php';
	Mydata_Connector_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mydata-connector-deactivator.php
 */
function deactivate_mydata_connector() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mydata-connector-deactivator.php';
	Mydata_Connector_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mydata_connector' );
register_deactivation_hook( __FILE__, 'deactivate_mydata_connector' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mydata-connector.php';


//Add plugin setting page in archive plugin view (Temporary position)
function add_plugin_options_page_url($actions) {
		$mylinks = array(
		'<a href="'.admin_url('options-general.php?page=mydata-connector-admin').'">'.__('Settings','mydata-connector').'</a>');
		$actions = array_merge( $actions, $mylinks );
		return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_plugin_options_page_url');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mydata_connector() {

	$plugin = new Mydata_Connector();
	$plugin->run();

}
run_mydata_connector();
