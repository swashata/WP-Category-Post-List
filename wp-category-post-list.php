<?php
/*
Plugin Name: WP Category Post List Widget
Plugin URI: http://www.intechgrity.com/wp-plugins/wp-category-post-list-wordpress-plugin/
Description: Lists down Posts filtered by category. You can show thumbnail, modify the HTML structure of the widget and do almost whatever you want. Access it from the Widgets option under the Appearance. The shortcode is [wp_cpl_sc] Check the settings page for more info or check the documentation <a href="http://www.intechgrity.com/wp-plugins/wp-category-post-list-wordpress-plugin/">here</a>
Version: 2.5.1
Author: Swashata
Author URI: http://www.swashata.com/
License: GPL2
*/

/*  Copyright 2010-2017  Swashata Ghosh  (email : swashata@iptms.co)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/**
 * TODO List for development 3.0.0
 * 1. Theme Types
 * 	a. Legecy Themes - Leave as is
 * 	b. Tile Theme - With 5 color options including a grey
 * 	c. Card Theme - With 5 color options including a grey
 * 	d. Metro Theme - With 5 color options including a grey
 * 	e. Basic Listing - Just the titles
 * 2. New Widget - Tabbed
 * 	a. Show list from multiple category
 * 	b. Show Basic Listing only
 * 3. Support Multiple Categories
 * 	a. Have multiple categories by AND|OR|NOT ( id="1,2,3" :: id="1+2+3" :: id="1,2+3,-10" )
 * 4. Support Infinite scrolling for shortcode output
 */

if ( ! function_exists( 'ipt_error_log' ) ) {
	/**
	 * Logs error in the WordPress debug mode
	 *
	 * @param      mixed  $var    The variable
	 */
	function ipt_error_log( $var ) {
		// Do nothing if not in debugging environment
		if ( ! defined( 'WP_DEBUG' ) || true != WP_DEBUG ) {
			return;
		}
		// Log the variable
		error_log( print_r( $var, true ) );
	}
}

/**
 * Class for automatic lazy loader.
 */
class WP_CPL_AutoLoader {
	/**
	 * Loads functionality classes.
	 *
	 * @param      string  $name   The class name
	 */
	public static function load_functionality_classes( $name ) {
		$path = trailingslashit( dirname( __FILE__ ) ) . 'inc' . DIRECTORY_SEPARATOR;
		$filename = 'class-' . str_replace( '_', '-', strtolower( $name ) ) . '.php';
		if ( file_exists( $path . $filename ) ) {
			require_once $path . $filename;
		}
	}

	/**
	 * Loads system classes.
	 *
	 * @param      string  $name   The class name
	 */
	public static function load_system_classes( $name ) {
		$path = trailingslashit( dirname( __FILE__ ) ) . 'classes' . DIRECTORY_SEPARATOR;
		$filename = 'class-' . str_replace( '_', '-', strtolower( $name ) ) . '.php';
		if ( file_exists( $path . $filename ) ) {
			require_once $path . $filename;
		}
	}
}
spl_autoload_register( 'WP_CPL_AutoLoader::load_functionality_classes' );
spl_autoload_register( 'WP_CPL_AutoLoader::load_system_classes' );

global $wp_cpl_settings;
$wp_cpl_settings = get_option( 'wp-cpl-itg-op' );

// Load the admin file
if ( is_admin() ) {
	require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'class-wp-cpl-admin.php';
}

// /**
//  * Include the loader
//  */
// include_once dirname(__FILE__) . '/classes/loader.php';
// $itgdb_wp_cpl_plugin = new itgdb_wp_cpl_loader(__FILE__, $text_domain);

/**
 * Include common files
 */
// include_once itgdb_wp_cpl_loader::$abs_path . '/includes/wp_cpl_css_filters.php';
// include_once itgdb_wp_cpl_loader::$abs_path . '/includes/wp_cpl_widget.php';

/**
 * Ignite
 */
$wp_cpl = WP_CPL_Loader::instance( __FILE__, '2.5.1' );
$wp_cpl->load();
