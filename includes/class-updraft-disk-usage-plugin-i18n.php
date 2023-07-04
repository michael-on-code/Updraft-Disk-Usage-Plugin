<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.animashaunmichael.com
 * @since      1.0.0
 *
 * @package    Updraft_Disk_Usage_Plugin
 * @subpackage Updraft_Disk_Usage_Plugin/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Updraft_Disk_Usage_Plugin
 * @subpackage Updraft_Disk_Usage_Plugin/includes
 * @author     Michael ANIMASHAUN <michaeloncode@gmail.com>
 */
class Updraft_Disk_Usage_Plugin_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'updraft-disk-usage-plugin',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
