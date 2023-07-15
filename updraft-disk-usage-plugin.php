<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.animashaunmichael.com
 * @since             1.0.0
 * @package           Updraft_Disk_Usage_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Updraft Disk Usage Plugin
 * Plugin URI:        https://www.animashaunmichael.com
 * Description:       A WordPress plugin that displays the disk usage of a website so a user could potentially take action and free up space.
 * Version:           1.0.0
 * Author:            Michael ANIMASHAUN
 * Author URI:        https://www.animashaunmichael.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       updraft-disk-usage-plugin
 * Domain Path:       /languages
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
define( 'UPDRAFT_DISK_USAGE_PLUGIN_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-updraft-disk-usage-plugin-activator.php
 */
function activate_updraft_disk_usage_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-updraft-disk-usage-plugin-activator.php';
	Updraft_Disk_Usage_Plugin_Activator::activate();

    require_once plugin_dir_path(__FILE__).'includes/DBHandler.php';
    $dbHandler = new DBHandler();
    $dbHandler->createTable();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-updraft-disk-usage-plugin-deactivator.php
 */
function deactivate_updraft_disk_usage_plugin() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-updraft-disk-usage-plugin-deactivator.php';
	Updraft_Disk_Usage_Plugin_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_updraft_disk_usage_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_updraft_disk_usage_plugin' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-updraft-disk-usage-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_updraft_disk_usage_plugin() {

	$plugin = new Updraft_Disk_Usage_Plugin();
	$plugin->run();

}
run_updraft_disk_usage_plugin();
