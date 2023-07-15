<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.animashaunmichael.com
 * @since      1.0.0
 *
 * @package    Updraft_Disk_Usage_Plugin
 * @subpackage Updraft_Disk_Usage_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Updraft_Disk_Usage_Plugin
 * @subpackage Updraft_Disk_Usage_Plugin/admin
 * @author     Michael ANIMASHAUN <michaeloncode@gmail.com>
 */
class Updraft_Disk_Usage_Plugin_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Updraft_Disk_Usage_Plugin_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Updraft_Disk_Usage_Plugin_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/updraft-disk-usage-plugin-admin.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Updraft_Disk_Usage_Plugin_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Updraft_Disk_Usage_Plugin_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/updraft-disk-usage-plugin-admin.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name, 'diskUsageData', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'file_extension_uri' => plugin_dir_url(__FILE__).'file-type/'
            )
        );

    }

}
