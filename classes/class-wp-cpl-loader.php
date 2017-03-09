<?php
/**
 * The primary Loader class for loading all plugin functionality
 *
 * A complete rewrite of the older class
 * from scratch
 *
 * @package WP Category Posts List Plugin
 * @subpackage System Classes
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
	 * Admin classes to instantiate
	 *
	 * @var        array
	 */
	private static $init_classes = array();

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

		// Set Admin classes
		self::$init_classes = array( 'WP_CPL_Settings', 'WP_CPL_UI_Check' );
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

		// Auto upgrade
		add_action( 'plugins_loaded', array( $this, 'auto_upgrade' ) );

		// Do some admin related stuff
		if ( is_admin() ) {
			// Extendible WP CPL Settings
			add_action( 'plugins_loaded', array( $this, 'init_admin_menus' ), 20 );
			// Add our glorified settings page
			add_action( 'admin_init', array( $this, 'gen_admin_menu' ), 20 );
			// Add some CSS/JS to the widgets and customizer area
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_menu_style' ) );
			// A little modification for the plugin actions ( from plugin listing )
			add_filter( 'plugin_action_links_' . plugin_basename( self::$abs_file ), array( $this, 'plugin_action_links' ), 10, 2 );
		}

		// Frontend Enqueue
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ) );

		// After setup theme to enable post thumbnail
		// With a delayed priority
		add_action( 'after_setup_theme', array( $this, 'enable_post_thumbnail' ), 11 );

		// Init the widgets
		add_action( 'widgets_init', array( $this, 'cpl_widget_init' ) );

		// Init the shortcode
		add_shortcode( 'wp_cpl_sc', array( $this, 'cpl_shortcode' ) );
	}

	/*==========================================================================
	 * Main Functionality Handling
	 *========================================================================*/
	/**
	 * Init the main widget
	 */
	public function cpl_widget_init() {
		// TODO
	}

	/**
	 * Shortcode handler
	 *
	 * Calls the Shortcode Class and provides the output
	 */
	public function cpl_shortcode( $atts = array(), $content = null ) {
		// TODO
	}

	/*==========================================================================
	 * System Hooks
	 *========================================================================*/
	/**
	 * Enables the post thumbnail.
	 */
	public function enable_post_thumbnail() {
		// Check if post thumbnail is already enabled
		if ( ! current_theme_supports( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' );
		}
	}

	/**
	 * Filters the plugin action for this plugin and adds in the Settings page
	 * and the Widgets page
	 *
	 * @param      array  $links  The array of links
	 */
	public function plugin_action_links( $links ) {
		// Settings
		$settings = sprintf( '<a href="%1$s">%2$s</a>', add_query_arg( 'page', 'wp_cpl_itg_page', admin_url( 'admin.php' ) ), __( 'Settings', 'wp-cpl' ) );
		// Widgets
		$widgets = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'widgets.php' ), __( 'Widgets', 'wp-cpl' ) );

		// Insert before
		array_unshift( $links, $settings, $widgets );
		return $links;
	}

	public function init_admin_menus() {
		self::$init_classes = apply_filters( 'wp_cpl_admin_menus', self::$init_classes );
		foreach ( (array) self::$init_classes as $class ) {
			if ( class_exists( $class ) ) {
				global ${'admin_menu' . $class};
				${'admin_menu' . $class} = new $class();
			}
		}
	}

	/**
	 * Hooks to the admin_menu to create WP CPL Settings page
	 */
	public function gen_admin_menu() {
		$admin_menus = array();
		foreach ( (array) self::$init_classes as $class ) {
			if ( class_exists( $class ) ) {
				global ${'admin_menu' . $class};
				$admin_menus[] = ${'admin_menu' . $class}->get_pagehook();
			}
		}

		foreach ( $admin_menus as $menu ) {
			add_action( 'admin_print_styles-' . $menu, array( $this, 'admin_enqueue_script_style' ) );
		}
	}

	/*==========================================================================
	 * Enqueues
	 *========================================================================*/

	/**
	 * Admin related enqueues
	 */
	public function admin_menu_style() {
		global $pagenow;
		// Just our expanding JS + CSS for the advanced options
		if ( 'widgets.php' == $pagenow || 'customize.php' == $pagenow ) {
			// TODO
		}
	}

	/**
	 * Enqueues on pages handled by WP CPL
	 */
	public function admin_enqueue_script_style() {
		// All files needed by UI
		$ui = WP_CPL_Admin_UI::get_instance();
		$ui->enqueue( apply_filters( 'wp_cpl_admin_ignore_js', array() ) );

		// Other files needed by the main plugin
		// Nothing Yet!
	}

	/**
	 * Frontend Enqueue
	 */
	public function frontend_enqueue() {
		// TODO
	}

	/*==========================================================================
	 * Activation & Deactivation
	 *========================================================================*/
	/**
	 * Does a few stuff on plugin install
	 *
	 * It basically checks if the option is installed If it is, then merge with
	 * the new one
	 *
	 * If not, then create it
	 *
	 * @param      boolean  $network_wide  Whether network activation
	 */
	public function plugin_install( $network_wide = false ) {
		$install = new WP_CPL_Install();
		$install->install( $network_wide );
	}

	public function auto_upgrade() {
		global $wp_cpl_settings;
		if ( ! isset( $wp_cpl_settings['version'] ) || version_compare( $wp_cpl_settings['version'], self::$version, '<' ) ) {
			$install = new WP_CPL_Install();
			$install->checkop();
		}
	}
}
