<?php
/**
 * The installation class
 *
 * Checks for options and upgrades
 *
 * Also checks for minimum requirements
 *
 * @package WP Category Posts List Plugin
 * @subpackage System Classes
 *
 * @author Swashata Ghosh <swashata@iptms.co>
 */

class WP_CPL_Install {

	public function install( $network_wide = false ) {
		// Check for PHP Version
		if ( version_compare( PHP_VERSION, '5.0.0', '<' ) ) {
			deactivate_plugins( plugin_basename( WP_CPL_Loader::$abs_file ) );
			wp_die( __( 'Sorry, WP Category Posts List plugin requires PHP 5 or better!', 'wp-cpl' ) );
			return;
		}
		// Check for WordPress Version
		if ( version_compare( get_bloginfo( 'version' ), '4.0.0', '<' ) ) {
			deactivate_plugins( plugin_basename( WP_CPL_Loader::$abs_file ) );
			wp_die( __( 'Sorry, WP Category Posts List plugin requires WordPress version 4.0.0 or better!', 'wp-cpl' ) );
			return;
		}

		// No errors, so activate
		$this->checkop();
	}

	public function checkop() {
		// Default Options
		$default_op = array(
			'version' => WP_CPL_Loader::$version,
			'enqueue_css' => true,
			'tile_thumb_widgets' => array( 50, 50 ),
			'tile_thumb_shortcodes' => array( 150, 150 ),
			'card_thumb_widgets' => array( 480, 200 ),
			'card_thumb_shortcodes' => array( 480, 200 ),
		);

		$current_op = get_option( 'wp-cpl-itg-op', false );

		// If fresh installation
		if ( ! $current_op ) {
			add_option( 'wp-cpl-itg-op', $default_op );
		} else {
			// Previous build found
			// Clone the options to migrate
			$new_op = $default_op;
			// Set the version for super legecy
			if ( ! isset( $current_op['version'] ) ) {
				$current_op['version'] = '1.0.0';
			}
			// If upgrading from legecy
			if ( version_compare( $current_op['version'], '3.0.0', '<' ) ) {
				// We be good samaritan and migrate
				$new_op['enqueue_css'] = $current_op['wp_cpl_use_def_css'];
				$new_op['tile_thumb_widgets'] = is_array( $current_op['wp_cpl_thumb_size'] ) ? $current_op['wp_cpl_thumb_size'] : array( $current_op['wp_cpl_thumb_size'], $current_op['wp_cpl_thumb_size'] );
				$new_op['tile_thumb_shortcodes'] = is_array( $current_op['wp_cpl_sc_thumb_size'] ) ? $current_op['wp_cpl_sc_thumb_size'] : array( $current_op['wp_cpl_sc_thumb_size'], $current_op['wp_cpl_sc_thumb_size'] );
			} else {
				// Check if versions aren't equal
				if ( WP_CPL_Loader::$version != $current_op['version'] ) {
					// Version-wise special upgade
					switch ( $current_op['version'] ) {
						case '3.0.0':
							// Nothing for now
							// Just upgrading from the legecy
							break;
					}
				}

				// Merge the new settings
				$new_op = wp_parse_args( $current_op, $default_op );
			}

			// Set the new version
			$new_op['version'] = WP_CPL_Loader::$version;
			// Update
			update_option( 'wp-cpl-itg-op', $new_op );
		}
	}
}
