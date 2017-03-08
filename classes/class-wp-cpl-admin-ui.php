<?php
/**
 * The administrative UI Class
 *
 * Creates UI Elements in the plugin settings page
 *
 * It is a singleton class
 *
 * @package WP Category Posts List Plugin
 * @subpackage System Classes
 * @author Swashata Ghosh <swashata@iptms.co>
 */

class WP_CPL_Admin_UI {
	/**
	 * Instance variable
	 *
	 * @var        WP_CPL_Admin_UI
	 */
	private static $instance = null;

	/**
	 * Gets the instance.
	 *
	 * @return     WP_CPL_Admin_UI  The instance.
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor made private for singleton usage
	 */
	private function __construct() {

	}

	public function enqueue() {

	}
}
