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
	 * Static location of plugin files
	 *
	 * Generally the lib directory
	 *
	 * @var        string
	 */
	private $static_location = null;

	/**
	 * Constructor made private for singleton usage
	 */
	private function __construct() {
		$this->static_location = plugins_url( '/static/admin/', WP_CPL_Loader::$abs_file );
	}

	/**
	 * Enqueues all needed stuff
	 *
	 * @param      array  $ignore_js  JS handles to not enqueue
	 */
	public function enqueue( $ignore_js = array() ) {
		// WP locale for translating the datepicker
		global $wp_locale;

		// Shortcuts to variables
		$version = WP_CPL_Loader::$version;
		$static_location = $this->static_location;

		// Enqueue all styles
		wp_enqueue_style( 'wp-cpl-material-font', '//fonts.googleapis.com/css?family=Noto+Sans|Roboto:300,400,400i,700', array(), $version );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_style( 'wp-cpl-admin-ui', $static_location . 'css/wp-cpl-admin-ui.css', array(), $version );

		// Enqueue all scripts
		$scripts = array(
			'jquery-ui-core'          => array(),
			'jquery-ui-widget'        => array(),
			'jquery-ui-mouse'         => array(),
			'jquery-ui-button'        => array(),
			// Add touch punch
			'jquery-touch-punch'      => array(),
			'jquery-ui-draggable'     => array(),
			'jquery-ui-droppable'     => array(),
			'jquery-ui-sortable'      => array(),
			'jquery-ui-datepicker'    => array(),
			'jquery-ui-dialog'        => array(),
			'jquery-ui-tabs'          => array(),
			'jquery-ui-slider'        => array(),
			'jquery-ui-spinner'       => array(),
			'jquery-ui-progressbar'   => array(),
			'thickbox'                => array(),
			'jquery-color'            => array(),
			'wp-color-picker'         => array(),
			'dashicons'               => array(),
			'jquery-timepicker-addon' => array( $static_location . 'js/jquery-ui-timepicker-addon.js', array( 'jquery', 'jquery-ui-datepicker' ) ),
			'wp-cpl-admin-ui-js'      => array( $static_location . 'js/jquery.wp-cpl-admin-ui.min.js', array(  ) ),
		);

		// Localization

		$datetime_l10n = array(
			'closeText'         => __( 'Done', 'wp-cpl' ),
			'currentText'       => __( 'Today', 'wp-cpl' ),
			'tcurrentText' => __( 'Now', 'wp-cpl' ),
			'monthNames'        => array_values( $wp_locale->month ),
			'monthNamesShort'   => array_values( $wp_locale->month_abbrev ),
			'monthStatus'       => __( 'Show a different month', 'wp-cpl' ),
			'dayNames'          => array_values( $wp_locale->weekday ),
			'dayNamesShort'     => array_values( $wp_locale->weekday_abbrev ),
			'dayNamesMin'       => array_values( $wp_locale->weekday_initial ),
			// get the start of week from WP general setting
			'firstDay'          => get_option( 'start_of_week' ),
			// is Right to left language? default is false
			'isRTL'             => $wp_locale->is_rtl(),
			/* translators: A is for AM */
			'amNames' => array( _x( 'AM', 'timeMeridiem', 'wp-cpl' ), _x( 'A', 'timeMeridiem', 'wp-cpl' ) ),
			/* translators: P is for PM */
			'pmNames' => array( _x( 'PM', 'timeMeridiem', 'wp-cpl' ), _x( 'P', 'timeMeridiem', 'wp-cpl' ) ),
			/* translators: Change %s to the time suffix. %s is always replaced by an empty string */
			'timeSuffix' => sprintf( _x( '%s', 'timeSuffix', 'wp-cpl' ), '' ),
			'timeOnlyTitle' => __( 'Choose Time', 'wp-cpl' ),
			'timeText' => __( 'Time', 'wp-cpl' ),
			'hourText' => __( 'Hour', 'wp-cpl' ),
			'minuteText' => __( 'Minute', 'wp-cpl' ),
			'secondText' => __( 'Second', 'wp-cpl' ),
			'millisecText' => __( 'Millisecond', 'wp-cpl' ),
			'microsecText' => __( 'Microsecond', 'wp-cpl' ),
			'timezoneText' => __( 'Timezone', 'wp-cpl' ),
		);
		$scripts_localize = array(
			'jquery-timepicker-addon' => array(
				'object_name' => 'WPCPLl10n',
				'l10n' => $datetime_l10n,
			),
			'wp-cpl-admin-ui-js' => array(
				'object_name' => 'initCPLUI',
				'l10n' => array(
					'ajax_loader' => __( 'Please Wait', 'wp-cpl' ),
					'delete_title' => __( 'Confirm Deletion', 'wp-cpl' ),
					'delete_msg' => __( '<p>Are you sure you want to delete?</p><p>The action can not be undone</p>', 'wp-cpl' ),
					'got_it' => __( 'Got it', 'wp-cpl' ),
					'help' => __( 'Help!', 'wp-cpl' ),
				),
			),
		);
		foreach ( $scripts as $script_id => $script_prop ) {
			if ( ! in_array( $script_id, $ignore_js ) ) {
				if ( empty( $script_prop ) ) {
					wp_enqueue_script( $script_id );
				} else {
					wp_enqueue_script( $script_id, $script_prop[0], $script_prop[1], $version );
				}
				if ( isset( $scripts_localize[$script_id] ) && is_array( $scripts_localize[$script_id] ) && isset( $scripts_localize[$script_id]['object_name'] ) && isset( $scripts_localize[$script_id]['l10n'] ) ) {
					wp_localize_script( $script_id, $scripts_localize[$script_id]['object_name'], $scripts_localize[$script_id]['l10n'] );
				}
			}
		}

		// Enqueue the media for uploader
		wp_enqueue_media();

	}

	/*==========================================================================
	 * jQuery UI Elements
	 *========================================================================*/
	/**
	 * Generate Tabs with callback populators
	 * Generates all necessary HTMLs. No need to write any classes manually.
	 *
	 * @param array   $tabs        Associative array of all the tab elements.
	 * $tab = array(
	 *      'id' => 'ipt_fsqm_form_name',
	 *      'label' => 'Form Name',
	 *      'callback' => 'function',
	 *      'scroll' => false,
	 *      'classes' => array(),
	 *      'has_inner_tab' => false,
	 *  );
	 * @param type    $collapsible
	 * @param type    $vertical
	 */
	public function tabs( $tabs, $collapsible = false, $vertical = false ) {
		$data_collapsible = ( $collapsible == true ) ? ' data-collapsible="true"' : '';
		$classes = array( 'ipt_uif_tabs' );
		$classes[] = ( $vertical == true ) ? 'vertical' : 'horizontal';
?>
<div<?php echo $data_collapsible; ?> class="<?php echo implode( ' ', $classes ); ?>">
	<ul>
		<?php foreach ( $tabs as $tab ) : ?>
		<li><a href="#<?php echo $tab['id']; ?>"><?php echo $tab['label']; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<?php foreach ( $tabs as $tab ) : ?>
		<?php
		$tab = wp_parse_args( $tab, array(
			'id' => '',
			'label' => '',
			'callback' => '',
			'scroll' => true,
			'classes' => array(),
			'has_inner_tab' => false,
		) );

		if ( ! $this->check_callback( $tab['callback'] ) ) {
			$tab['callback'] = array(
				array( $this, 'msg_error' ), array( __( 'Invalid Callback', 'wp-cpl' ) ),
			);
		}

		$tab_classes = isset( $tab['classes'] ) && is_array( $tab['classes'] ) ? $tab['classes'] : array();
		if ( $tab['has_inner_tab'] ) {
			$tab_classes[] = 'has-inner-tab';
		} else if ( $tab['scroll'] ) {
			$tab_classes[] = 'scroll-vertical';
		}
		?>
		<div id="<?php echo $tab['id']; ?>" class="<?php echo implode( ' ', $tab_classes ); ?>">
			<?php if ( true == $tab['scroll'] && false == $tab['has_inner_tab'] ) : ?>
				<div class="ipt_uif_tabs_inner">
			<?php endif; ?>
					<?php call_user_func_array( $tab['callback'][0], $tab['callback'][1] ); ?>
					<?php $this->clear(); ?>
			<?php if ( true == $tab['scroll'] && false == $tab['has_inner_tab'] ) : ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
<?php $this->clear(); ?>
		<?php
	}

	/*==========================================================================
	 * WordPress Core UI
	 *========================================================================*/


	/*==========================================================================
	 * Form Elements
	 *========================================================================*/

	public function buttons( $buttons, $container = true, $container_id = '', $container_classes = array() ) {
		// Add out UI container class
		if ( ! is_array( $container_classes ) ) {
			$container_classes = (array) $container_classes;
		}
		$container_classes[] = 'ipt_uif_button_container';

		// Print the container
		if ( $container ) {
			echo '<div' . ( ! empty( $container_id ) ? ' id="' . esc_attr( $container_id ) . '"' : '' ) . ' class="' . esc_attr( implode( ' ', $container_classes ) ) . '">';
		}
		foreach ( $buttons as $button ) {
			call_user_func_array( array( $this, 'button' ), $button );
			echo "\n";
		}
		if ( $container ) {
			echo '</div>';
		}
	}

	/**
	 * Print a button
	 *
	 * @param      string   $text     Button Text
	 * @param      string   $type     (optional) Button type for 'button' elements,
	 *                                submit|reset|button etc
	 * @param      string   $size     (optional) Size of the button large|medium|small
	 *                                Default medium
	 * @param      string   $tag      (optional) HTML tag Defaults button could be button|a
	 * @param      string   $url      (optional) URL in case of anchor button
	 * @param      boolean  $newtab   (optional) Whether to open the button in the new tab,
	 *                                works in case of 'a' tag
	 * @param      array    $classes  (optional) Additional classes
	 */
	public function button( $text, $type = '', $size = 'medium', $tag = 'button', $url = '', $newtab = false, $classes = array(), $data = array(), $attr = array() ) {
		if ( ! is_array( $classes ) ) {
			$classes = (array) $classes;
		}
		// Add UI classes
		$classes[] = 'ipt_uif_button';
		// Add size
		$classes[] = $size;

		// Make the button
		$button = '<' . $tag . ' class="' . esc_attr( implode( ' ', $classes ) ) . '"';
		if ( 'a' == $tag ) {
			if ( ! empty( $url ) ) {
				$button .= ' href="' . esc_url( $url ) . '"';
			}
			if ( $newtab ) {
				$button .= ' target="_blank" rel="noopener"';
			}
		}
		if ( 'a' != $tag && ! empty( $type ) ) {
			$button .= ' type="' . esc_attr( $type ) . '"';
		}

		// Convert our data & html attributes
		if ( is_array( $data ) && ! empty( $data ) ) {
			$button .= $this->convert_data_attributes( $data );
		}
		if ( is_array( $attr ) && ! empty( $attr ) ) {
			$button .= $this->convert_html_attributes( $attr );
		}

		$button .= '>' . $text . '</' . $tag . '>';

		echo $button;
	}

	/*==========================================================================
	 * Container Elements
	 *========================================================================*/

	/*==========================================================================
	 * Interactions
	 *========================================================================*/
	/**
	 * Prints an error message in style.
	 *
	 * @param      string  $msg    The message
	 * @param      bool    $echo   TRUE(default) to echo the output, FALSE to
	 *                             just return
	 *
	 * @return     string  The HTML output
	 */
	public function msg_error( $msg = '', $echo = true ) {
		return $this->print_message( 'red', $msg, $echo );
	}

	/**
	 * Prints an update message in style.
	 *
	 * @param      string  $msg    The message
	 * @param      bool    $echo   TRUE(default) to echo the output, FALSE to
	 *                             just return
	 *
	 * @return     string  The HTML output
	 */
	public function msg_update( $msg = '', $echo = true ) {
		return $this->print_message( 'yellow', $msg, $echo );
	}

	/**
	 * Prints an okay message in style.
	 *
	 * @param      string  $msg    The message
	 * @param      bool    $echo   TRUE(default) to echo the output, FALSE to
	 *                             just return
	 *
	 * @return     string  The HTML output
	 */
	public function msg_okay( $msg = '', $echo = true ) {
		return $this->print_message( 'green', $msg, $echo );
	}

	/**
	 * Internal function to print all messeges
	 *
	 * @param      string   $style  The style
	 * @param      string   $msg    The message
	 * @param      boolean  $echo   Whether to echo
	 *
	 * @return     string   The message
	 */
	private function print_message( $style, $msg = '', $echo = true ) {
		$icon = 'dashicons ';
		if ( 'yellow' == $style || 'update' == $style ) {
			$icon .= 'dashicons-warning';
		} else if ( 'red' == $style || 'error' == $style ) {
			$icon .= 'dashicons-no-alt';
		} else {
			$icon .= 'dashicons-yes';
		}
		$output = '<div class="ipt_uif_message ' . $style . '"><a href="javascript:;" class="ipt_uif_message_dismiss" title="' . __( 'Dismiss', 'wp-cpl' ) . '">&times;</a><p><i class="inline-dash ' . $icon . '"></i> ' . $msg . '</p></div>';
		if ( $echo ) {
			echo $output;
		}
		return $output;
	}

	/**
	 * Prints an UI loader which will be hidden when the UI script runs
	 *
	 * @param      boolean  $inline  Whether show inline
	 * @param      string   $id      The HTML id
	 * @param      string   $text    Text to show
	 */
	public function ui_loader( $inline = true, $id = '', $text = null ) {
		$this->ajax_loader( false, $inline, $text, $id, array( 'ipt_uif_ui_init_loader' ) );
	}

	/**
	 * Creates the HTML for the CSS3 Loader.
	 *
	 * @param      bool    $hidden   TRUE(default) if hidden in inital state
	 *                               (Optional).
	 * @param      string  $default  Default text
	 * @param      bool    $inline   Whether inline(true) or overlay (false)
	 * @param      string  $id       HTML ID (Optional).
	 * @param      array   $classes  Array of additional classes (Optional).
	 * @param      array   $labels   Labels which will be converted to HTML data
	 *                               attribute
	 */
	public function ajax_loader( $hidden = true, $inline = false, $default = null, $id = '', $classes = array(), $labels = array() ) {
		if ( !is_array( $classes ) ) {
			$classes = (array) $classes;
		}
		if ( !$inline ) {
			$classes[] = 'ipt_uif_ajax_loader';
		} else {
			$classes[] = 'ipt_uif_ajax_loader_inline';
		}
		$id_attr = '';
		if ( $id != '' ) {
			$id_attr = ' id="' . esc_attr( trim( $id ) ) . '"';
		}
		$style_attr = '';
		if ( $hidden == true ) {
			$style_attr = ' style="display: none;"';
		}
		$data_attr = $this->convert_data_attributes( $labels );
		if ( $default === null ) {
			$default = __( 'Loading', 'wp-cpl' );
		}
?>
<div class="<?php echo implode( ' ', $classes ); ?>"<?php echo $id_attr . $style_attr . $data_attr; ?>>
	<div class="ipt_uif_ajax_loader_inner ipt_uif_ajax_loader_animate">
		<div class="ipt_uif_ajax_loader_icon ipt_uif_ajax_loader_spin">
			<svg class="eform-loader-circular" viewBox="25 25 50 50">
				<circle class="eform-loader-path" cx="50" cy="50" r="20" fill="none" stroke-width="3" stroke-miterlimit="10"></circle>
			</svg>
		</div>
		<div class="ipt_uif_ajax_loader_hellip">
			<span class="dot1">.</span><span class="dot2">.</span><span class="dot3">.</span>
		</div>
		<div class="ipt_uif_ajax_loader_text"><?php echo $default; ?></div>
		<div class="clear"></div>
	</div>
</div>
		<?php
	}

	/*==========================================================================
	 * Other CSS Helper stuff
	 *========================================================================*/

	/**
	 * Clear floating elements
	 *
	 * @param      string  $direction  Clear left or right
	 */
	public function clear( $direction = 'both' ) {
		if ( 'both' == $direction ) {
			echo '<div class="clear"></div>';
		} else {
			echo '<div class="clear-' . esc_attr( $direction ) . '"></div>';
		}
	}

	/*==========================================================================
	 * Helper HTML Stuff
	 *========================================================================*/

	/**
	 * Converts array to HTML data-key=value attributes
	 *
	 * This can be echoed directly inside the HTML tag
	 *
	 * @param      array  $data   The associative array of data and values
	 *
	 * @return     string  Readily printable HTML attribute
	 */
	public function convert_data_attributes( $data ) {
		if ( false == $data || ! is_array( $data ) || empty( $data ) ) {
			return '';
		}

		$data_attr = '';
		foreach ( $data as $d_key => $d_val ) {
			$data_attr .= ' data-' . esc_attr( $d_key ) . '="' . esc_attr( $d_val ) . '"';
		}

		return $data_attr;
	}

	/**
	 * Convert array into HTML attr=value pair
	 *
	 * This can be echoed directly inside the HTML tag
	 *
	 * @param      array  $atts   Associative array of data attributes
	 *
	 * @return     string  Readily printable HTML attribute
	 */
	public function convert_html_attributes( $atts ) {
		if ( false == $atts || ! is_array( $atts ) || empty( $atts ) ) {
			return '';
		}

		$html_atts = '';
		foreach ( $atts as $attr => $val ) {
			$html_atts .= ' ' . $attr . '="' . esc_attr( $val ) . '"';
		}

		return $html_atts;
	}


	/*==========================================================================
	 * System Methods
	 *========================================================================*/
	/**
	 * Checks if a valid callback is passed
	 *
	 * @param      array    $callback  The callback with optional parameters
	 *
	 * @return     boolean  True if valid, false otherwise
	 */
	public function check_callback( $callback ) {
		if ( ! is_array( $callback ) || ! isset( $callback[0] ) ) {
			return false;
		}
		if ( is_callable( $callback[0] ) ) {
			return true;
		}
		return false;
	}

}
