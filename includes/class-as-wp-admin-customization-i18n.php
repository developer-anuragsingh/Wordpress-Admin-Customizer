<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://anurag-singh.github.io/
 * @since      1.0.0
 *
 * @package    Wordpress_Admin_Customizer
 * @subpackage Wordpress_Admin_Customizer/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wordpress_Admin_Customizer
 * @subpackage Wordpress_Admin_Customizer/includes
 * @author     Anurag Singh <developer.anuragsingh@outlook.com>
 */
class As_Wp_Admin_Customization_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'as-wp-admin-customization',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
