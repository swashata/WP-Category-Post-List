<?php

/**
 * The primary Loader class for loading all plugin functionality
 *
 * A complete rewrite of the older class
 * from scratch
 *
 * @package WP Category Posts List Plugin
 * @subpackage Functionality Classes
 * @author Swashata Ghosh <swashata@iptms.co>
 */

class WP_CPL_Loader {
	/**
	 * Absolute path of this plugin
	 */
	public static $abs_path;

	/**
	 * Absolute filepath of the main plugin file
	 */
	public static $abs_file;

	/**
	 * Current script version of the plugin
	 */
	public static $version;

	/**
	 * The instance variable
	 *
	 * This is a singleton class and we are going to use this
	 * for getting the only instance
	 */
	private static $instance = null;

	/**
	 * Get the singleton instance
	 *
	 * @param      string         $plugin_file  The plugin file
	 * @param      string         $version      The version
	 *
	 * @return     WP_CPL_Loader  The singleton instance
	 */
	public static function instance( $plugin_file, $version ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $plugin_file, $version );
		}
		return self::$instance;
	}

	/**
	 * Constructor function
	 *
	 * We declare as private to make the class singleton
	 *
	 * @param      string  $plugin_file  The plugin file
	 * @param      string  $version      The version
	 */
	private function __construct( $plugin_file, $version ) {
		// Set the plugin file
		self::$abs_file = $plugin_file;

		// Set the plugin directory
		self::$abs_path = dirname( $plugin_file );

		// Set version
		self::$version = $version;
	}

	/**
	 * The loader function
	 *
	 * Does all sorts of hooking, filtering & enqueue
	 */
	public function load() {
		// First the activation hook
		register_activation_hook( self::$abs_file, array( $this, 'plugin_install' ) );

		// Load text domain for translation
		load_plugin_textdomain( 'wp-cpl', false, dirname( plugin_basename( self::$abs_file ) ) . '/translations' );

		// Do some admin related stuff
		if ( is_admin() ) {
			// Add our glorified settings page
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			// Add some CSS/JS to the widgets and customizer area
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_menu_style' ) );
		}
	}

	/**
	 * Hooks to the admin_menu to create WP CPL Settings page
	 */
	public function admin_menu() {
		// TODO
	}

	public function admin_menu_style() {
		global $pagenow
		// Just our expanding JS + CSS for the advanced options
		if ( 'widgets.php' == $pagenow || 'customize.php' == $pagenow ) {
			// TODO
		}
	}


	/*==========================================================================
	 * Activation & Deactivation
	 *========================================================================*/
	/**
	 * Does a few stuff on plugin install
	 *
	 * It basically checks if the option is installed
	 * If it is, then merge with the new one
	 *
	 * If not, then create it
	 */
	public function plugin_install() {

	}
}
