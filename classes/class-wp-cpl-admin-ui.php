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
		wp_enqueue_style( 'wp-cpl-jquery-icon', $static_location . 'css/fonts/jquery.iconfont/jquery-ui.icon-font.css', array(), $version );
		wp_enqueue_style( 'ipt-icomoon-fonts', $static_location . 'css/fonts/icomoon/icomoon.css', array(), $version );
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
			'select2'                 => array( $static_location . 'js/select2.min.js', array( 'jquery' ) ),
			'wp-cpl-admin-ui-js'      => array( $static_location . 'js/jquery.wp-cpl-admin-ui.min.js', array() ),
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
					'L10n' => array(
						'ajax_loader' => __( 'Please Wait', 'wp-cpl' ),
						'delete_title' => __( 'Confirm Deletion', 'wp-cpl' ),
						'delete_msg' => __( '<p>Are you sure you want to delete?</p><p>The action can not be undone</p>', 'wp-cpl' ),
						'got_it' => __( 'Got it', 'wp-cpl' ),
						'help' => __( 'Help!', 'wp-cpl' ),
					),
				),
			),
		);
		// WooCommerce Compatibility
		wp_deregister_script( 'select2' );
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
		// Standardize the tabs
		foreach ( $tabs as $key => $tab ) {
			$tabs[ $key ] = wp_parse_args( $tab, array(
				'id' => '',
				'label' => '',
				'callback' => '',
				'scroll' => true,
				'classes' => array(),
				'has_inner_tab' => false,
				'icon' => 'none',
			) );
		}
?>
<div<?php echo $data_collapsible; ?> class="<?php echo implode( ' ', $classes ); ?>">
	<ul>
		<?php foreach ( $tabs as $tab ) : ?>
		<li><a href="#<?php echo $tab['id']; ?>"><?php $this->print_icon( $tab['icon'] ); ?><?php echo $tab['label']; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<?php foreach ( $tabs as $tab ) : ?>
		<?php
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

	/**
	 * Prints a jQuery UI DatePicker
	 *
	 * @param      string  $name         HTML Name
	 * @param      string  $value        Element Value
	 * @param      string  $placeholder  Element Placeholder
	 */
	public function datepicker( $name, $value, $placeholder = '' ) {
		echo '<div class="ipt_uif_datepicker">';
		$this->text( $name, $value, $placeholder );
		echo '</div>';
	}

	/**
	 * Prints a jQuery UI DateTimePicker
	 *
	 * @param      string  $name         HTML Name
	 * @param      string  $value        Element Value
	 * @param      string  $placeholder  Element Placeholder
	 */
	public function datetimepicker( $name, $value, $placeholder = '', $now = false ) {
		echo '<div class="ipt_uif_datetimepicker">';
		$this->text( $name, $value, $placeholder );
		echo '</div>';
	}

	/**
	 * Generates a simple jQuery UI Progressbar Minumum value is 0 and maximum
	 * is 100. So always calculate in percentage.
	 *
	 * @param      string   $id       The HTML ID
	 * @param      numeric  $start    The start value
	 * @param      array    $classes  Additional classes
	 */
	public function progressbar( $id = '', $start = 0, $classes = array() ) {
		if ( ! is_array( $classes ) ) {
			$classes = (array) $classes;
		}
		$classes[] = 'ipt_uif_progress_bar';
		$id_attr = '';
		if ( $id != '' ) {
			$id_attr = ' id="' . esc_attr( $id ) . '"';
		}
		?>
<div class="<?php echo implode( ' ', $classes ); ?>" data-start="<?php echo $start; ?>"<?php echo $id_attr; ?>>
	<div class="ipt_uif_progress_value"></div>
</div>
		<?php
	}

	/**
	 * Generate a horizontal slider to select between numerical values
	 *
	 * @param      string  $name   HTML name
	 * @param      string  $value  Initial value of the range
	 * @param      int     $min    Minimum of the range
	 * @param      int     $max    Maximum of the range
	 * @param      int     $step   Slider move step
	 */
	public function slider( $name, $value, $min = 0, $max = 100, $step = 1 ) {
		// Other stuff
		$min = (float) $min;
		$max = (float) $max;
		$step = (float) $step;
		$value = $value == '' ? $min : (float) $value;
		if ( $value < $min )
			$value = $min;
		if ( $value > $max )
			$value = $max;
		?>
<div class="ipt_uif_slider_box">
	<input type="number" min="<?php echo $min; ?>" max="<?php echo $max; ?>" step="<?php echo $step; ?>" class="ipt_uif_slider ipt_uif_text" data-min="<?php echo $min; ?>" data-max="<?php echo $max; ?>" data-step="<?php echo $step; ?>" name="<?php echo esc_attr( trim( $name ) ); ?>" id="<?php echo $this->generate_id_from_name( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" />
</div>
		<?php
	}

	/**
	 * Generate a horizontal slider to select a range between numerical values
	 *
	 * @param      mixed(array|string)  $names   $names HTML names in the order
	 *                                           Min value -> Max value. If
	 *                                           string is given the [max] and
	 *                                           [min] is added to make an array
	 * @param      array                $values  Initial values of the range in
	 *                                           the same order
	 * @param      int                  $min     Minimum of the range
	 * @param      int                  $max     Maximum of the range
	 * @param      int                  $step    Slider move step
	 */
	public function slider_range( $names, $values, $min = 0, $max = 100, $step = 1 ) {
		if ( ! is_array( $names ) ) {
			$name = (string) $names;
			$names = array(
				$name . '[min]', $name . '[max]',
			);
		}

		if ( ! is_array( $values ) ) {
			$value = (int) $values;
			$values = array(
				$value, $value,
			);
		}

		// Main stuff
		$min = (float) $min;
		$max = (float) $max;
		$step = (float) $step;

		if ( ! isset( $values[0] ) ) {
			$values[0] = $values['min'];
			$values[1] = $values['max'];
		}
		$value_min = $values[0] != '' ? $values[0] : $min;
		$value_max = $values[1] != '' ? $values[1] : $min;

		if ( $value_min < $min )
			$value_min = $min;
		if ( $value_min > $max )
			$value_min = $max;
		if ( $value_max < $min )
			$value_max = $min;
		if ( $value_max > $max )
			$value_max = $max;
		?>
<div class="ipt_uif_slider_box ipt_uif_slider_range_box">
	<input type="number" min="<?php echo $min; ?>" max="<?php echo $max; ?>" step="<?php echo $step; ?>" class="ipt_uif_slider slider_range ipt_uif_text" data-min="<?php echo $min; ?>" data-max="<?php echo $max; ?>" data-step="<?php echo $step; ?>" name="<?php echo esc_attr( trim( $names[0] ) ); ?>" id="<?php echo $this->generate_id_from_name( $names[0] ); ?>" value="<?php echo esc_attr( $value_min ); ?>" />
	<input type="number" min="<?php echo $min; ?>" max="<?php echo $max; ?>" step="<?php echo $step; ?>" class="ipt_uif_slider_range_max ipt_uif_text" name="<?php echo esc_attr( trim( $names[1] ) ); ?>" id="<?php echo $this->generate_id_from_name( $names[1] ); ?>" value="<?php echo esc_attr( $value_max ); ?>" />
</div>
		<?php
	}

	/*==========================================================================
	 * WordPress Core UI
	 *========================================================================*/
	/**
	 * Link a textarea with a wordpress editor
	 *
	 * Things are done through JavaScript
	 *
	 * @param      string   $name         HTML Name
	 * @param      string   $value        The value
	 * @param      string   $placeholder  The placeholder
	 * @param      string   $size         The size
	 * @param      string   $state        The state
	 * @param      array    $classes      The classes
	 * @param      boolean  $data         The data
	 * @param      boolean  $validation   The validation
	 */
	public function textarea_linked_wp_editor( $name, $value, $placeholder, $size = 'regular', $state = 'normal', $classes = array(), $data = false, $validation = false ) {
		if ( ! is_array( $classes ) ) {
			$classes = (array) $classes;
		}
		$classes[] = 'wp_editor';
		$this->textarea( $name, $value, $placeholder, 4, $size, $state, $classes, $data, array(), $validation );
	}

	/**
	 * Prints WP Editor with some additional settings
	 *
	 * @param      string  $name                 HTML Name
	 * @param      string  $value                Value
	 * @param      array   $additional_settings  The additional settings
	 */
	public function wp_editor( $name, $value, $additional_settings = array() ) {
		if ( ! is_array( $additional_settings ) ) {
			$additional_settings = (array) $additional_settings;
		}
		$additional_settings['textarea_name'] = $name;
		$editor_id = $this->generate_id_from_name( $name );
		wp_editor( $value, $editor_id, $additional_settings );
	}

	/**
	 * Prints a WP Color Picker
	 *
	 * Creates iris color picker internally
	 *
	 * @param      string  $name         HTML name
	 * @param      string  $value        Default value ( checks for existance of # which is required )
	 * @param      string  $placeholder  HTML placeholder
	 */
	public function colorpicker( $name, $value, $placeholder = '' ) {
		$value = '#' . ltrim( $value, '#' );
		$this->text( $name, $value, $placeholder, 'text', 'small', 'normal', array( 'ipt_uif_colorpicker', 'code' ) );
	}

	public function upload( $name, $value, $title_name = '', $label = 'Upload', $title = 'Choose Image', $select = 'Use Image', $width = '', $height = '', $background_size = '' ) {
		$data = array(
			'title' => $title,
			'select' => $select,
			'settitle' => $this->generate_id_from_name( $title_name ),
		);
		$buttons = array();
		$buttons[] = array(
			$label, '', 'small', 'secondary', 'normal', array( 'ipt_uif_upload_button' ), 'button', array(), array(), '', 'upload'
		);
		$buttons[] = array(
			'', '', 'small', 'secondary', 'normal', array( 'ipt_uif_upload_cancel' ), 'button', array(), array(), '', 'close'
		);
		$preview_style = '';
		$container_style = '';
		if ( $width != '' ) {
			$container_style .= 'max-width: none; width: ' . $width . ';';
		}
		if ( $height != '' ) {
			$container_style .= 'height: ' . $height . ';';
		}
		$preview_style .= 'height: 100%;';
		if ( $background_size != '' ) {
			$preview_style .= 'background-size: ' . $background_size . ';';
		}
?>
<div class="ipt_uif_upload">
	<div class="ipt_uif_upload_bg" style="<?php echo esc_attr( $container_style ); ?>">
		<div style="<?php echo esc_attr( $preview_style ); ?>" class="ipt_uif_upload_preview"></div>
	</div>
	<input<?php echo $this->convert_data_attributes( $data ); ?> type="text" name="<?php echo $name; ?>" id="<?php echo $this->generate_id_from_name( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" class="ipt_uif_text fit" />
	<?php //$this->button( $label, '', 'small', 'secondary', 'normal', array(), 'button', false ); ?>
	<?php $this->buttons( $buttons, '', 'center' ); ?>
</div>
		<?php
	}

	public function dropdown_pages( $args = '' ) {
		$defaults = array(
			//Dropdown arguments
			'name' => 'page_id',
			'selected' => 0,
			'validation' => false,
			'disabled' => false,
			'show_option_none' => '',
			'option_none_value' => '0',
			//Page arguments
			'depth' => 0,
			'child_of' => 0,
		);
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		$pages = get_pages( $r );

		$items = array();

		if ( '' != $show_option_none ) {
			$items[] = array(
				'value' => $option_none_value,
				'label' => $show_option_none,
			);
		}

		foreach ( $pages as $page ) {
			$items[] = array(
				'value' => $page->ID,
				'label' => $page->post_title,
			);
		}

		$this->select( $name, $items, $selected, $validation, false, $disabled );
	}


	/*==========================================================================
	 * Helper UI
	 *========================================================================*/
	/**
	 * Select the heading type
	 *
	 * @param      string  $name      HTML Name
	 * @param      string  $selected  Selected item
	 */
	public function heading_type( $name, $selected ) {
		$items = array();
		for ( $i = 1; $i <= 6; $i++ ) {
			$items[] = array(
				'label' => sprintf( _x( 'Heading %1$d', 'wp-cpl-ui-heading', 'wp-cpl' ), $i ),
				'value' => 'h' . $i,
			);
		}

		$this->select( $name, $items, $selected, false, false, array( 'ipt_uif_heading_type' ) );
	}

	/**
	 * Layout Selector
	 *
	 * @param      string   $name         HTML Name
	 * @param      string   $selected     Selected item
	 * @param      array    $additionals  Additional layout elements, must come
	 *                                    with URL to images as <span
	 *                                    title="{$title}"><img src="{$url}"
	 *                                    /></span>
	 * @param      boolean  $no_defaults  Whether to include default set of
	 *                                    layouts
	 */
	public function layout_select( $name, $selected, $additionals = array(), $no_defaults = false ) {
		$id = $this->generate_id_from_name( $name );
		// Layouts
		$layouts = array();
		// Add the basic ones
		if ( ! $no_defaults ) {
			// 1-4 column layouts
			for( $i = 1; $i <= 4; $i++ ) {
				$layouts[] = array(
					'value' => (string) $i,
					'label' => '<span title="' . sprintf( _nx( '%d Column', '%d Columns', $i, 'ui-admin-layout', 'wp-cpl' ), $i ) . '"><img src="' . $this->static_location . 'images/layout-' . $i . '.png" /></span>',
				);
			}
			// Automatic columns
			$layouts[] = array(
				'value' => 'random',
				'label' => '<span title="' . __( 'Automatic Columns', 'wp-cpl' ) . '"><img src="' . $this->static_location . 'images/layout-random.png" /></span>',
			);
		}
		// Merge additionals
		if ( ! empty( $additionals ) ) {
			$layouts = array_merge( $layouts, $additionals );
		}

		// Print our radio
		echo '<div class="ipt_uif_radio_layout_wrap ipt_uif_rc_image_wrap">';
		$this->radios( $name, $layouts, $selected );
		echo '</div>';
	}

	/**
	 * Position Selector
	 *
	 * @param      string   $name         HTML Name
	 * @param      string   $selected     Selected item
	 * @param      array    $additionals  Additional position elements, must come
	 *                                    with URL to images as <span
	 *                                    title="{$title}"><img src="{$url}"
	 *                                    /></span>
	 * @param      boolean  $no_defaults  Whether to include default set of
	 *                                    layouts
	 */
	public function position_select( $name, $selected, $additionals = array(), $no_defaults = false ) {
		$id = $this->generate_id_from_name( $name );
		/* TODO Create Position Images: @link http://www.freepik.com/free-vector/4-icons-web_1052385.htm?utm_source=piktab&utm_medium=extension&utm_campaign=freepik-web */
		$positions = array();
		if ( ! $no_defaults ) {
			// Left
			$positions[] = array(
				'value' => 'left',
				'label' => '<span title="' . __( 'Left', 'wp-cpl' ) . '"><img src="' . $this->static_location . 'images/position-left.png" /></span>',
			);
			// Top
			$positions[] = array(
				'value' => 'top',
				'label' => '<span title="' . __( 'Top', 'wp-cpl' ) . '"><img src="' . $this->static_location . 'images/position-top.png" /></span>',
			);
			// Right
			$positions[] = array(
				'value' => 'right',
				'label' => '<span title="' . __( 'Right', 'wp-cpl' ) . '"><img src="' . $this->static_location . 'images/position-right.png" /></span>',
			);
			// Bottom
			$positions[] = array(
				'value' => 'bottom',
				'label' => '<span title="' . __( 'Bottom', 'wp-cpl' ) . '"><img src="' . $this->static_location . 'images/position-bottom.png" /></span>',
			);
		}
		// Add additionals
		if ( ! empty( $additionals ) ) {
			$positions = array_merge( $positions, $additionals );
		}

		// Print our radio
		echo '<div class="ipt_uif_radio_position_wrap ipt_uif_rc_image_wrap">';
		$this->radios( $name, $positions, $selected );
		echo '</div>';
	}

	/**
	 * Alignment Selector
	 *
	 * @param      string  $name      HTML Name
	 * @param      string  $selected  Selected one
	 */
	public function alignment_select( $name, $selected ) {
		$items = array(
			'left' => __( 'Align Left', 'wp-cpl' ),
			'center' => __( 'Align Center', 'wp-cpl' ),
			'right' => __( 'Align Right', 'wp-cpl' ),
			'justify' => __( 'Align Justify', 'wp-cpl' ),
		);
		$alignments = array();
		foreach ( $items as $key => $val ) {
			$alignments[] = array(
				'value' => $key,
				'label' => '<span title="' . $val . '"><img src="' . $this->static_location . 'images/alignment-' . $key . '.png" /></span>'
			);
		}
		// Print our radio
		echo '<div class="ipt_uif_radio_alignment_wrap ipt_uif_rc_image_wrap">';
		$this->radios( $name, $alignments, $selected );
		echo '</div>';
	}

	/**
	 * Web Font selector
	 *
	 * Works only for google fonts
	 *
	 * @param      string  $name      HTML name
	 * @param      string  $selected  Selected fonts
	 * @param      array   $fonts     Associative array of fonts
	 */
	public function webfonts( $name, $selected, $fonts ) {
		$items = array();
		foreach ( $fonts as $f_key => $font ) {
			$items[] = array(
				'label' => $font['label'],
				'value' => $f_key,
				'data' => array(
					'fontinclude' => $font['include'],
				),
			);
		}

		echo '<div class="ipt_uif_font_selector">';

		$this->select( $name, $items, $selected );
		echo ' <span class="ipt_uif_font_preview">Grumpy <strong>wizards</strong> <em>make</em> <strong><em>toxic brew</em></strong> for the evil Queen and Jack.</span>';

		echo '</div>';
	}

	/*==========================================================================
	 * ICON SELECTOR
	 *========================================================================*/

	/**
	 * Print a font Icon Picker
	 *
	 * @param      string       $name           HTML Name
	 * @param      string|int   $selected_icon  Selected Icon Code
	 * @param      string|bool  $no             Placeholder text or false if
	 *                                          there has to be an icon
	 * @param      string       $by             What to pick by -> hex | class
	 * @param      boolean      $print_cancel   The print cancel
	 * @return     void
	 */
	public function icon_selector( $name, $selected_icon, $no = 'Do not show', $by = 'hex', $print_cancel = false ) {
		$this->clear();
		$buttons = array();
		$buttons[] = array(
			'', '', 'small', 'secondary', 'normal', array( 'ipt_uif_icon_cancel' ), 'button', array(), array(), '', 'close'
		);
		if ( false === $no ) {
			$print_cancel = false;
		}
?>
<input type="text"<?php if ( false === $no ) echo ' data-no-empty="true"'; else echo ' placeholder="' . esc_attr( $no ) . '"'; ?> data-icon-by="<?php echo esc_attr( $by ); ?>" class="ipt_uif_icon_selector code small-text" size="15" name="<?php echo $name; ?>" id="<?php echo $this->generate_id_from_name( $name ); ?>" value="<?php echo esc_attr( $selected_icon ); ?>" />
<?php if ( $print_cancel ) : ?>
<?php $this->buttons( $buttons, '', 'ipt_uif_fip_button' ); ?>
<?php endif; ?>
		<?php
		$this->clear();
	}

	public function print_icon_by_class( $icon = 'none', $size = 24 ) {
		if ( is_numeric( $icon ) ) {
			$this->print_icon_by_data( $icon, $size );
			return;
		}
?>
<?php if ( $icon != 'none' ) : ?>
<i class="ipt-icomoon-<?php echo esc_attr( $icon ); ?> ipticm" style="font-size: <?php echo $size; ?>px;"></i>
<?php endif; ?>
		<?php
	}

	public function print_icon_by_data( $data = 'none', $size = 24 ) {
		if ( ! is_numeric( $data ) ) {
			$this->print_icon_by_class( $data, $size );
			return;
		}
?>
<?php if ( $data != 'none' ) : ?>
<i class="ipticm" data-ipt-icomoon="&#x<?php echo dechex( $data ); ?>;" style="font-size: <?php echo $size; ?>px;"></i>
<?php endif; ?>
		<?php
	}

	public function print_icon( $icon = 'none', $size = 24 ) {
		if ( 'none' == $icon || empty( $icon ) ) {
			return;
		}
		if ( is_numeric( $icon ) ) {
			$this->print_icon_by_data( $icon, $size );
		} else {
			$this->print_icon_by_class( $icon, $size );
		}
	}

	/*==========================================================================
	 * Form Elements
	 *========================================================================*/
	/**
	 * Prints a group of radio items for a single HTML name
	 *
	 * @param      string  $name         The HTML name of the radio group
	 * @param      array   $items        Associative array of all the radio
	 *                                   items. array( 'value' => '', 'label' =>
	 *                                   '', 'disabled' => true|false,//optional
	 *                                   'data' => array('key' =>
	 *                                   'value'[,...]), //optional HTML 5 data
	 *                                   attributes inside an associative array
	 *                                   )
	 * @param      string  $checked      The value of the checked item
	 * @param      bool    $conditional  Whether the group represents
	 *                                   conditional questions. This will wrap
	 *                                   it inside a conditional div which will
	 *                                   be fired using jQuery. It does not
	 *                                   populate or create anything inside the
	 *                                   conditional div. The id of the
	 *                                   conditional divs should be given inside
	 *                                   the data value of the items in the form
	 *                                   condID => 'ID_OF_DIV'
	 * @param      string  $col          The column layout. Could be inline, 1,
	 *                                   2, 3, 4
	 */
	public function radios( $name, $items, $checked, $conditional = false, $col = 'inline' ) {
		$this->_radio_checkbox_helper( $name, $items, $checked, $conditional, $col );
	}

	/**
	 * Prints a group of checkbox items for a single HTML name
	 *
	 * @param      string  $name         The HTML name of the checkbox group
	 * @param      array   $items        Associative array of all the checkbox
	 *                                   items. array( 'value' => '', 'label' =>
	 *                                   '', 'disabled' => true|false,//optional
	 *                                   'data' => array('key' =>
	 *                                   'value'[,...]), //optional HTML 5 data
	 *                                   attributes inside an associative array
	 *                                   )
	 * @param      string  $checked      The value of the checked item
	 * @param      bool    $conditional  Whether the group represents
	 *                                   conditional questions. This will wrap
	 *                                   it inside a conditional div which will
	 *                                   be fired using jQuery. It does not
	 *                                   populate or create anything inside the
	 *                                   conditional div. The id of the
	 *                                   conditional divs should be given inside
	 *                                   the data value of the items in the form
	 *                                   condID => 'ID_OF_DIV'
	 * @param      string  $col          The column layout. Could be inline, 1,
	 *                                   2, 3, 4
	 */
	public function checkboxes( $name, $items, $checked, $conditional = false, $col = 'inline' ) {
		$this->_radio_checkbox_helper( $name, $items, $checked, $conditional, $col, 'checkbox' );
	}

	public function select( $name, $items, $selected, $multiple = false, $conditional = false, $classes = array(), $print_select = true ) {
		if ( ! is_array( $items ) || empty( $items ) ) {
			return;
		}

		if ( ! is_array( $classes ) ) {
			$classes = (array) $classes;
		}

		$classes[] = 'ipt_uif_select';
		$items = $this->standardize_items( $items );
		$id = $this->generate_id_from_name( $name );

		if ( $conditional ) {
			echo '<div class="ipt_uif_conditional_select">';
		}

		$select = '';
		if ( $print_select ) {
			$select .= '<select class="' . implode( ' ', $classes ) . '" name="' . esc_attr( $name ) . '" id="' . $id . '" '. ( true === $multiple ? ' multiple="multiple"' : '' ) . '>';
		}

		foreach ( $items as $item ) {
			$select .= '<option value="' . $item['value'] . '"' . ' ' . $this->convert_data_attributes( $item['data'] ) .
				$this->selected( $selected, $item['value'] ) . '>' . $item['label'] . '</option>';
		}

		if ( $print_select ) {
			$select .= '</select>';
		}

		echo $select;

		if ( $conditional ) {
			echo '</div>';
		}
	}

	/**
	 * Prints a single checkbox element
	 *
	 * @param      string   $name         HTML Name
	 * @param      string   $value        Item value
	 * @param      string   $label        Item label
	 * @param      boolean  $is_checked   Indicates if checked
	 * @param      boolean  $conditional  Whether conditional logic should be applied on it
	 * @param      string   $condid       data condId
	 */
	public function checkbox( $name, $value, $label, $is_checked, $conditional = false, $condid = '' ) {
		$item = array(
			'value' => $value,
			'label' => $label,
		);
		$checked = array();
		if ( true == $is_checked ) {
			$checked[] = $value;
		}
		if ( true == $conditional ) {
			$item['data'] = array(
				'condid' => $condid,
			);
		}
		$this->checkboxes( $name, array( $item ), $checked, $conditional );
	}

	/**
	 * Print a Toggle HTML item
	 *
	 * @param      string  $name         The HTML name of the toggle
	 * @param      string  $on           ON text
	 * @param      string  $off          OFF text
	 * @param      bool    $checked      TRUE if checked
	 * @param      string  $value        The HTML value of the toggle checkbox
	 *                                   (Optional, default to '1')
	 * @param      bool    $conditional  Whether the group represents
	 *                                   conditional questions. This will wrap
	 *                                   it inside a conditional div which will
	 *                                   be fired using jQuery. It does not
	 *                                   populate or create anything inside the
	 *                                   conditional div. The id of the
	 *                                   conditional divs should be given inside
	 *                                   the data value of the items in the form
	 *                                   condID => 'ID_OF_DIV'
	 * @param      array   $data         HTML 5 data attributes in the form
	 *                                   array('key' => 'value'[,...])
	 */
	public function toggle( $name, $on, $off, $checked, $value = '1', $conditional = false, $data = array() ) {
		if ( $conditional == true ) {
			echo '<div class="ipt_uif_conditional_input">';
		}

		$id = $this->generate_id_from_name( $name );
?>
<div class="switch">
	<label for="<?php echo $id; ?>" data-on="<?php echo $on; ?>" data-off="<?php echo $off; ?>">
		<?php echo $off; ?>
		<input<?php echo $this->convert_data_attributes( $data ); ?> type="checkbox"<?php if ( $checked ) : ?> checked="checked"<?php endif; ?> class="ipt_uif_switch" name="<?php echo $name; ?>" id="<?php echo $id; ?>" value="<?php echo esc_attr( $value ); ?>" />
		<span class="lever"></span>
		<?php echo $on; ?>
	</label>
</div>
<?php
		if ( $conditional == true ) {
			echo '</div>';
		}
	}

	/**
	 * Generate input type text HTML
	 *
	 * @param      string  $name         HTML name of the text input
	 * @param      string  $value        Initial value of the text input
	 * @param      string  $placeholder  Default placeholder
	 * @param      string  $type         Input Type text|email|url|number
	 * @param      string  $size         Size of the text input. By default
	 *                                   takes 300px width. Pass 'fit' or
	 *                                   'large-text' to take 100%
	 * @param      string  $state        readonly or disabled state
	 * @param      array   $classes      Array of additional classes
	 * @param      array   $data         HTML 5 data attributes in associative
	 *                                   array
	 * @param      array   $attr         Other HTML attributes
	 * @param      array   $validation   Associative array of all validation
	 *                                   clauses
	 */
	public function text( $name, $value, $placeholder, $type = 'text', $size = '', $state = 'normal', $classes = array(), $data = false, $attr = array(), $validation = false ) {
		$id = $this->generate_id_from_name( $name );
		if ( ! is_array( $classes ) ) {
			$classes = (array) $classes;
		}
		$classes[] = 'ipt_uif_text';

		$validation_attr = $this->convert_validation_attr( $validation );

		if ( ! empty( $size ) ) {
			$classes[] = $size;
		}

		$data_attr = $this->convert_data_attributes( $data );

		if ( ! is_array( $attr ) ) {
			$attr = (array) $attr;
		}

		$attr['type'] = $type;

		$html_attr = $this->convert_html_attributes( $attr );

		$input = '<input class="' . implode( ' ', $classes ) . '"' . ' placeholder="' . esc_attr( $placeholder ) . '"' .
			' name="' . esc_attr( $name ) . '" id="' . $id . '" value="' . esc_textarea( $value ) . '"' .
			$data_attr . $this->convert_state_to_attribute( $state ) . $html_attr . $validation_attr . ' />';

		echo $input;
	}

	/**
	 * Prints a password element
	 *
	 * @param      string  $name         HTML name of the text input
	 * @param      string  $value        Initial value of the text input
	 * @param      string  $placeholder  Default placeholder
	 * @param      string  $size         Size of the text input. By default
	 *                                   takes 300px width. Pass 'fit' or
	 *                                   'large-text' to take 100%
	 * @param      string  $state        readonly or disabled state
	 * @param      array   $classes      Array of additional classes
	 * @param      array   $data         HTML 5 data attributes in associative
	 *                                   array
	 * @param      array   $attr         Other HTML attributes
	 * @param      array   $validation   Associative array of all validation
	 *                                   clauses
	 */
	public function password( $name, $value, $placeholder, $size = 'fit', $state = 'normal', $classes = array(), $data = false, $attr = array(), $validation = false ) {
		if ( ! is_array( $classes ) ) {
			$classes = (array) $classes;
		}
		$classes[] = 'ipt_uif_password';

		$this->text( $name, $value, $placeholder, 'password', $size, $state, $classes, $data, $attr, $validation );
	}

	/**
	 * Generate a horizontal slider to select between numerical values
	 *
	 * @param      string  $name         HTML name
	 * @param      string  $value        Initial value of the range
	 * @param      string  $placeholder  HTML placeholder
	 * @param      int     $min          Minimum of the range
	 * @param      int     $max          Maximum of the range
	 * @param      int     $step         Slider move step
	 */
	public function spinner( $name, $value, $placeholder = '', $min = '', $max = '', $step = 1 ) {
		?>
<input type="number" placeholder="<?php echo $placeholder; ?>" class="ipt_uif_text code ipt_uif_uispinner" min="<?php echo $min; ?>" max="<?php echo $max; ?>" step="<?php echo $step; ?>" name="<?php echo esc_attr( trim( $name ) ); ?>" id="<?php echo $this->generate_id_from_name( $name ); ?>" value="<?php echo esc_attr( $value ); ?>" />
		<?php
	}

	/**
	 * Generate textarea HTML
	 *
	 * @param      string   $name         HTML name of the text input
	 * @param      string   $value        Initial value of the text input
	 * @param      string   $placeholder  Default placeholder
	 * @param      integer  $rows         The rows
	 * @param      string   $size         Size of the text input
	 * @param      string   $state        readonly or disabled state
	 * @param      array    $classes      Array of additional classes
	 * @param      array    $data         HTML 5 data attributes in associative
	 *                                    array
	 * @param      array    $attr         The attribute
	 * @param      array    $validation   Associative array of all validation
	 *                                    clauses
	 */
	public function textarea( $name, $value, $placeholder, $rows = 4, $size = 'fit', $state = 'normal', $classes = array(), $data = false, $attr = array(), $validation = false ) {
		$id = $this->generate_id_from_name( $name );
		if ( ! is_array( $classes ) ) {
			$classes = (array) $classes;
		}
		$classes[] = 'ipt_uif_textarea';

		$validation_attr = $this->convert_validation_attr( $validation );

		if ( ! empty( $size ) ) {
			$classes[] = $size;
		}

		$data_attr = $this->convert_data_attributes( $data );

		if ( ! is_array( $attr ) ) {
			$attr = (array) $attr;
		}
		$html_attr = $this->convert_html_attributes( $attr );

		$textarea = '<textarea rows="' . esc_attr( $rows ) . '" class="' . implode( ' ', $classes ) .
			' placeholder="' . esc_attr( $placeholder ) . ' name="' . esc_attr( $name ) . '" id="' . $id . '"' .
			$data_attr . $html_attr . $this->convert_state_to_attribute( $state ) . '>' .
			esc_textarea( $value ) .
			'</textarea>';
		echo $textarea;
	}

	/**
	 * Prints Group of buttons
	 *
	 * @param      array    $buttons            The associative array of
	 *                                          buttons. Every array is called
	 *                                          upon $this->button
	 * @param      boolean  $container          Whether to group inside a
	 *                                          container div
	 * @param      string   $container_id       The container identifier
	 * @param      array    $container_classes  The container classes
	 */
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

	/**
	 * Helper function for printing checkbox and radio elements
	 *
	 * Checks for types and processes data accordingly
	 *
	 * @param      string               $name         HTML Name
	 * @param      array                $items        Items
	 * @param      mixed(array|string)  $checked      The value(s) of checked
	 *                                                elements
	 * @param      boolean              $conditional  Whether conditional
	 *                                                element
	 * @param      string               $col          Number of columns in
	 *                                                options
	 * @param      string               $type         The type - radio|checkbox
	 */
	private function _radio_checkbox_helper( $name, $items, $checked, $conditional = false, $col = 'inline', $type = 'radio' ) {
		if ( ! is_array( $items ) || empty( $items ) ) {
			return;
		}

		if ( 'radio' == $type ) {
			if ( ! is_string( $checked ) ) {
				$checked = (string) $checked;
			}
		} elseif ( 'checkbox' == $type ) {
			if ( ! is_array( $checked ) ) {
				$checked = (array) $checked;
			}
		}

		$id_prefix = $this->generate_id_from_name( $name );
		$items = $this->standardize_items( $items );

		if ( true == $conditional ) {
			echo '<div class="ipt_uif_conditional_input">';
		}
		echo '<div class="ipt_uif_label_group col-' . $col . '">';
		foreach ( (array) $items as $item ) {
			$data_attr = $this->convert_data_attributes( $item['data'] );
			$html_attr = $this->convert_html_attributes( $item['attr'] );
			$id = $this->generate_id_from_name( '', $id_prefix . '_' . $item['value'] );
			$item['class'] .= ' ' . ( 'radio' == $type ? 'ipt_uif_radio' : 'ipt_uif_checkbox' );
			$input =
				'<div class="ipt_uif_lc">' .
					'<input type="' . $type . '" ' . $this->checked( $checked, $item['value'] ) .
					$data_attr . ' class="' . $item['class'] . '" ' .
					'name="' . esc_attr( $name ) . '" id="' . $id . '" ' .
					'value="' . esc_attr( $item['value'] ) . '" />' .
					'<label for="' . $id . '">' . $item['label'] . '</label>' .
				'</div>';
			echo $input;
		}
		echo '</div>';
		if ( true == $conditional ) {
			echo '</div>';
		}
	}

	/*==========================================================================
	 * Container Elements
	 *========================================================================*/
	/**
	 * Prints a form table
	 *
	 * Just pass in the item information and everything else will be taken care
	 * of
	 *
	 * @param      array    $items  Associative array of items. 'name', 'label'(
	 *                              optional ), 'ui'( valid UI callback ),
	 *                              'param'( ui parameters ), 'help'( optional )
	 * @param      boolean  $table  Whether to print the table body
	 */
	public function form_table( $items, $table = true ) {
		if ( $table ) {
			echo '<table class="form-table"><tbody>';
		}

		foreach ( $items as $item ) {
			$item = wp_parse_args( $item, array(
				'name' => '',
				'label' => '',
				'ui' => '',
				'param' => array(),
				'help' => '',
			) );
			echo '<tr>';

			$item_colspan = 1;
			if ( '' == $item['label'] ) {
				$item_colspan++;
			}
			if ( '' == $item['help'] ) {
				$item_colspan++;
			}

			if ( '' != $item['label'] ) {
				echo '<th>';
				$this->generate_label( $item['name'], $item['label'] );
				echo '</th>';
			}

			echo '<td colspan="' . $item_colspan . '">';
			if ( $this->check_callback( array( array( $this, $item['ui'] ), $item['param'] ) ) ) {
				call_user_func_array( array( $this, $item['ui'] ), $item['param'] );
			} else {
				$this->msg_error( __( 'Invalid Callback', 'wp-cpl' ) );
			}
			echo '</td>';

			if ( '' != $item['help'] ) {
				echo '<td>';
				$this->help( $item['help'] );
				echo '</td>';
			}

			echo '</tr>';
		}

		if ( $table ) {
			echo '</tbody></table>';
		}
	}

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
		$output = '<div class="ipt_uif_message ' . $style . '"><a href="javascript:;" class="ipt_uif_message_dismiss" title="' . __( 'Dismiss', 'wp-cpl' ) . '">&times;</a><p>' . $msg . '</p></div>';
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

	/**
	 * Prints a clickable help icon
	 *
	 * Which will show a jQuery UI Dialog with help
	 *
	 * @param      string   $msg    The message
	 * @param      string   $title  The title
	 * @param      boolean  $align  Float left or right or inline, default
	 *                              right, float right
	 */
	public function help( $msg, $title = '', $align = 'right' ) {
		$this->help_head( $title, $align );
		echo wpautop( $msg );
		$this->help_tail();
	}

	/**
	 * Print the head portion of help UI
	 *
	 * Must call help_tail afterwards
	 *
	 * @param      string   $title  The title
	 * @param      boolean  $align  Float left or right or inline, default
	 *                              right, float right
	 */
	public function help_head( $title = '', $align = 'right' ) {
	?>
<div class="ipt_uif_msg ipt_uif_msg_<?php echo esc_attr( $align ); ?>">
	<a href="javascript:;" class="ipt_uif_msg_icon" title="<?php echo $title; ?>"><i class="ipt-icomoon-live_help"></i></a>
	<div class="ipt_uif_msg_body">
	<?php
	}

	/**
	 * Closes the help_head
	 */
	public function help_tail() {
	?>
	</div>
</div>
	<?php
	}

	public function collapsible( $label, $callback, $open = false ) {
		$this->collapsible_head( $label, $open );
		if ( ! $this->check_callback( $callback ) ) {
			$this->msg_error( __( 'Invalid callback', 'wp-cpl' ) );
		} else {
			call_user_func_array( $callback[0], $callback[1] );
		}
		$this->collapsible_tail();
	}

	public function collapsible_head( $label, $open = false ) {
?>
<div class="ipt_uif_shadow ipt_uif_collapsible" data-opened="<?php echo $open; ?>">
	<div class="ipt_uif_box">
		<h3><a class="ipt_uif_collapsible_handle_anchor" href="javascript:;"><span class="ipt-icomoon-arrow-down3 collapsible_state"></span><span class="ipt-icomoon-folder-open2 heading_icon"></span> <?php echo $label; ?></a></h3>
	</div>
	<div class="ipt_uif_collapsed">
		<?php
	}

	public function collapsible_tail() {
?>
		<?php $this->clear(); ?>
	</div>
</div>
		<?php
	}

	/**
	 * Create a box container nested inside a shadow container.
	 *
	 * @param      array   $callback  The callback function to populate.
	 * @param      array   $style     Array of shadow style and box style.
	 * @param      int     $scroll    The scroll height value in pixels. 0 if no
	 *                                scroll. Default is 400.
	 * @param      string  $id        HTML ID
	 * @param      array   $classes   HTML classes
	 */
	public function shadowbox( $callback, $style = 'normal', $scroll = 0, $id = '', $classes = array() ) {
		$class = 'ipt_uif_shadow shadow-' . $style;
		$this->div( $class, $callback, $scroll, $id, $classes );
	}

	/**
	 * Creates a nice looking container with an icon on top
	 *
	 * @param      string  $label        The heading
	 * @param      array  $callback     The callback function to populate
	 * @param      string  $icon         The icon. Consult the
	 *                                   /static/fonts/fonts.css to pass class
	 *                                   name
	 * @param      int     $scroll       The scroll height value in pixels. 0 if
	 *                                   no scroll. Default is 400.
	 * @param      string  $id           HTML ID
	 * @param      string  $head_button  The head button
	 * @param      array   $classes      HTML classes
	 */
	public function iconbox( $label, $callback, $icon = 'info2', $scroll = 0, $id = '', $head_button = '', $classes = array() ) {
		if ( ! $this->check_callback( $callback ) ) {
			$this->msg_error( __( 'Invalid Callback supplied', 'wp-cpl' ) );
			return;
		}

		$this->iconbox_head( $label, $icon, $scroll, $id, $head_button, $classes );
		call_user_func_array( $callback[0], $callback[1] );
		$this->iconbox_tail();
	}

	public function iconbox_head( $label, $icon = 'info2', $scroll = 0, $id = '', $head_button = '', $classes = array() ) {
		if ( '' != $head_button ) {
			$head_button = '<div class="ipt_uif_float_right">' . $head_button . '</div>';
		}
		$id_attr = '';
		if ( '' != $id ) {
			$id_attr = ' id="' . esc_attr( $id ) . '"';
		}
		if ( ! is_array( $classes ) ) {
			$classes = (array) $classes;
		}
		$classes[] = 'ipt_uif_iconbox';
		$inner_classes = array( 'ipt_uif_iconbox_inner' );
		$scroll = (int) $scroll;
		$scroll_style = '';
		if ( $scroll > 0 ) {
			$scroll_style = 'max-height: ' . $scroll . 'px; overflow-y: auto;';
			$inner_classes[] = 'ipt_uif_scroll';
		}
?>
<div class="<?php echo implode( ' ', $classes ); ?>"<?php echo $id_attr; ?>>
	<div class="ipt_uif_box ipt_uif_container_head">
		<?php echo $head_button; ?><h3><span class="ipt-icomoon-<?php echo esc_attr( $icon ); ?>"></span><?php echo $label; ?></h3>
	</div>
	<div class="<?php echo implode( ' ', $inner_classes ); ?>" style="<?php echo $scroll_style; ?>">
		<?php
	}

	public function iconbox_tail() {
?>
	</div>
</div>
		<?php
	}

	/**
	 * Creates a div
	 *
	 * Optionally with nested divs inside
	 *
	 * @param      array   $styles    $styles The HTML style. Can be a single
	 *                                string when only one div will be produced,
	 *                                or array in which case the 0th style will
	 *                                be used to create the main div and other
	 *                                styles will be nested inside as individual
	 *                                divs.
	 * @param      array   $callback  The callback function to populate.
	 * @param      int     $scroll    The scroll height value in pixels. 0 if no
	 *                                scroll.
	 * @param      string  $id        HTML ID
	 * @param      array   $classes   HTML classes
	 */
	public function div( $styles, $callback, $scroll = 0, $id = '', $classes = array() ) {
		if ( ! $this->check_callback( $callback ) ) {
			$this->msg_error( __( 'Invalid Callback supplied', 'wp-cpl' ) );
			return;
		}
		if ( ! is_array( $classes ) ) {
			$classes = (array) $classes;
		}

		if ( is_array( $styles ) && count( $styles ) > 1 ) {
			$classes = array_merge( $classes, (array) $styles[0] );
		} else {
			$classes[] = (string) $styles;
		}
		$style_attr = '';
		$scroll = (int) $scroll;
		if ( $scroll > 0 ) {
			$style_attr = ' style="max-height: ' . $scroll . 'px; overflow: auto;"';
			$classes[] = 'ipt_uif_scroll';
		}
		$id_attr = '';
		if ( trim( $id ) != '' ) {
			$id_attr = ' id="' . esc_attr( trim( $id ) ) . '"';
		}
?>
<div class="<?php echo implode( ' ', $classes ); ?>"<?php echo $id_attr . $style_attr; ?>>
	<?php if ( is_array( $styles ) && count( $styles ) > 1 ) : ?>
		<?php for ( $i = 1; $i < count( $styles ); $i++ ) : ?>
			<div class="<?php echo implode( ' ', (array) $styles[$i] ); ?>">
		<?php endfor; ?>
	<?php endif; ?>
				<?php call_user_func_array( $callback[0], $callback[1] ); ?>
	<?php if ( is_array( $styles ) && count( $styles ) > 1 ) : ?>
		<?php for ( $i = 1; $i < count( $styles ); $i++ ) : ?>
			</div>
		<?php endfor; ?>
	<?php endif; ?>
</div>
		<?php
	}

	/*==========================================================================
	 * SORTABLE DRAGGABLE & ADDABLE LIST
	 *========================================================================*/
	/**
	 * Creates a Sortable, Draggable and/or Addable container UI.
	 *
	 * @param array   $settings An associative array of settings. The format is
	 * <code>
	 * array(
	 *      'key' => '__SDAKEY__',
	 *      'columns' => array(
	 *          0 => array(
	 *              'label' => 'Heading',
	 *              'size' => '10',
	 *              'type' => 'text', //This is the callback function from IPT_Plugin_UIF_Admin
	 *          ),
	 *      ),
	 *      'features' => array(
	 *          'sortable' => true,
	 *          'draggable' => true,
	 *          'addable' => true,
	 *      ),
	 *      'labels' => array(
	 *          'confirm' => 'Confirm delete. The action can not be undone.',
	 *          'add' => 'Add New Item',
	 *          'del' => 'Click to delete',
	 *          'drag' => 'Drag this to rearrange',
	 *      ),
	 * );
	 * </code>
	 * @param array   $items    An associative array of items. The format is
	 * <code>
	 * array(
	 *      array([...,])[,...]
	 * )
	 * </code>
	 * Each array should be a list of parameters to the callback function.
	 * @param array   $data     An associative array of callbacks for the data section. The key passed here should match with settings[key]
	 * <code>
	 * array([,...])
	 * </code>
	 */
	public function sda_list( $settings, $items, $data, $max_key, $id = '' ) {
		$default = array(
			'key' => '__SDAKEY__',
			'columns' => array(),
			'features' => array(),
			'labels' => array(),
		);
		$settings = wp_parse_args( $settings, $default );
		$settings['labels'] = wp_parse_args( $settings['labels'], array(
			'confirm' => 'Confirm delete. The action can not be undone.',
			'confirmtitle' => 'Confirm Deletion',
			'add' => 'Add New Item',
			'del' => 'Click to delete',
			'drag' => 'Drag this to rearrange',
		) );
		$settings['features'] = wp_parse_args( $settings['features'], array(
			'draggable' => true,
			'addable' => true,
		) );
		$data_total = 0;
		$feature_attr = $this->convert_data_attributes( $settings['features'] );

		if ( $max_key == null && empty( $items ) ) { //No items
			$max_key = 0;
		} else { //Passed the largest key for the items, so should start from the very next key
			$max_key = $max_key + 1;
		}

		$sda_body_classes = array( 'ipt_uif_sda_body' );
		if ( true == $settings['features']['draggable'] || true == $settings['features']['addable'] ) {
			$sda_body_classes[] = 'eform-sda-has-toolbar';
		}
?>
<div class="ipt-backoffice-sda-wrap"<?php echo ($id != '' ? ' id="' . esc_attr( $id ) . '"' : '') ?>>
	<div class="ipt-backoffice-sda-inner">
		<div class="ipt-eform-sda ipt_uif_sda"<?php echo $feature_attr; ?>>
			<div class="<?php echo implode( ' ', $sda_body_classes ); ?>" data-buttontext="<?php printf( _x( 'please click on %1$s button to get started', 'ipt_uif_sda', 'ipt_fsqm' ), strtoupper( $settings['labels']['add'] ) ); ?>">
				<?php foreach ( $items as $item ) : ?>
				<div class="ipt_uif_sda_elem">
					<?php if ( true == $settings['features']['draggable'] || true == $settings['features']['addable'] ) : ?>
						<div class="ipt-eform-sda-toolbar">
							<?php if ( true == $settings['features']['draggable'] ) : ?>
								<div class="ipt_uif_sda_drag"><i class="ipt-icomoon-bars"></i></div>
							<?php endif; ?>
							<?php if ( true == $settings['features']['addable'] ) : ?>
								<div class="ipt_uif_sda_del"><i class="ipt-icomoon-times"></i></div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php foreach ( $settings['columns'] as $col_key => $column ) : ?>
					<div class="ipt_uif_sda_column_<?php echo $column['size']; ?>">
						<?php $this->generate_label( is_string( $item[ $col_key ][0] ) ? $item[ $col_key ][0] : '', $column['label'] ); ?>
						<?php call_user_func_array( array( $this, $column['type'] ), (array)$item[$col_key] ); ?>
					</div>
					<?php endforeach; ?>

					<div class="clear"></div>
				</div>
				<?php $data_total++; endforeach; ?>
			</div>

			<script type="text/html" class="ipt_uif_sda_data">
				<?php ob_start(); ?>
				<?php if ( true == $settings['features']['draggable'] || true == $settings['features']['addable'] ) : ?>
					<div class="ipt-eform-sda-toolbar">
						<?php if ( true == $settings['features']['draggable'] ) : ?>
							<div class="ipt_uif_sda_drag"><i class="ipt-icomoon-bars"></i></div>
						<?php endif; ?>
						<?php if ( true == $settings['features']['addable'] ) : ?>
							<div class="ipt_uif_sda_del"><i class="ipt-icomoon-times"></i></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php foreach ( $settings['columns'] as $col_key => $column ) : ?>
				<div class="ipt_uif_sda_column_<?php echo $column['size']; ?>">
					<?php $this->generate_label( is_string( $data[ $col_key ][0] ) ? $data[ $col_key ][0] : '', $column['label'] ); ?>
					<?php call_user_func_array( array( $this, $column['type'] ), $data[$col_key] ); ?>
				</div>
				<?php endforeach; ?>

				<div class="clear"></div>

				<?php
		$output = ob_get_clean();
		echo htmlspecialchars( $output );
?>
			</script>
			<?php
			if ( true == $settings['features']['addable'] ) {
				$buttons = array();
				$buttons[] = array(
					$settings['labels']['add'],
					'',
					'small',
					'secondary',
					'normal',
					array( 'ipt_uif_sda_button' ),
					'button',
					array(
						'total' => $data_total,
						'count' => $max_key,
						'key' => $settings['key'],
						'confirm' => $settings['labels']['confirm'],
					),
					array(),
					'',
					'plus',
				);

				$this->buttons( $buttons, '', array( 'ipt_uif_sda_foot' ) );
			}
			?>
		</div>
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
	 * @param      array  $attributes   Associative array of data attributes
	 *
	 * @return     string  Readily printable HTML attribute
	 */
	public function convert_html_attributes( $attributes ) {
		if ( false == $attributes || ! is_array( $attributes ) || empty( $attributes ) ) {
			return '';
		}

		$html_attr = '';
		foreach ( $attributes as $attr => $val ) {
			$html_attr .= ' ' . $attr . '="' . esc_attr( $val ) . '"';
		}

		return $html_attr;
	}

	/**
	 * Converts validation filters into
	 *
	 * @param      array   $validation  Associative array of validation
	 *                                  parameters
	 *
	 * @return     string  HTML5 validation that can be put into the HTML tag
	 */
	public function convert_validation_attr( $validation ) {
		$html_attr = array();
		if ( isset( $validation['required'] ) && true == $validation['required'] ) {
			$html_attr['required'] = 'required';
		}
		foreach ( array( 'min', 'max', 'maxlength', 'pattern' ) as $filter ) {
			if ( isset( $validation[ $filter ] ) && ! empty( $validation[ $filter ] ) ) {
				$html_attr[ $filter ] = $validation[ $filter ];
			}
		}
		return $this->convert_html_attributes( $html_attr );
	}

	/**
	 * Generate Label for an element
	 *
	 * @param      string  $name     The name of the element
	 * @param      string  $text
	 * @param      string  $id
	 * @param      array   $classes
	 */
	public function generate_label( $name, $text, $id = '', $classes = array() ) {
		if ( !is_array( $classes ) ) {
			$classes = (array) $classes;
		}
		$classes[] = 'ipt_uif_label';
		?>
<label class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" for="<?php echo $this->generate_id_from_name( $name, $id ); ?>"><?php echo $text; ?></label>
		<?php
	}

	/**
	 * Generates HTML ID from given name attribute
	 *
	 * Converts all non-supporting characters, like [,] and formulates a string
	 * that can be used as the ID
	 *
	 * @param      string  $name   HTML name
	 * @param      string  $id     HTML ID which if provided will be returned
	 *                             with some filters
	 *
	 * @return     string  HTML ID that can be put directly
	 */
	public function generate_id_from_name( $name, $id = '' ) {
		if ( '' == trim( $id ) ) {
			return esc_attr( str_replace( array( '[', ']' ), array( '_', '' ), trim( $name ) ) );
		} else {
			return esc_attr( trim( $id ) );
		}
	}

	/**
	 * Convert a valid state of HTML form elements to proper attribute="value"
	 * pair
	 *
	 * @param      string  $state  The state of the HTML item
	 *
	 * @return     string
	 */
	public function convert_state_to_attribute( $state ) {
		$output = '';
		switch ( $state ) {
			case 'disable' :
			case 'disabled' :
				$output = ' disabled="disabled"';
				break;
			case 'readonly' :
			case 'noedit' :
				$output = ' readonly="readonly"';
				break;
		}
		return $output;
	}

	/**
	 * Standardizes items for radios and checkboxes
	 *
	 * @param      array  $items  The associative array of items
	 *
	 * @return     array   The items that is accepted by the radios and checkboxes of this UI element
	 */
	public function standardize_items( $items ) {
		$new_items = array();
		if ( ! is_array( $items ) ) {
			$items = (array) $items;
		}
		foreach ( $items as $i_key => $item ) {
			if ( is_array( $item ) ) {
				if ( isset( $item['value'] ) ) {
					$new_items[] = array(
						'label' => isset( $item['label'] ) ? $item['label'] : ucfirst( $item['value'] ),
						'value' => esc_attr( (string) $item['value'] ),
						'data' => isset( $item['data'] ) ? (array) $item['data'] : array(),
						'class' => isset( $item['class'] ) ? $item['class'] : '',
						'attr' => isset( $item['attr'] ) ? (array) $item['attr'] : array(),
					);
				}
			} elseif ( is_string( $item ) ) {
				if ( is_numeric( $i_key ) ) {
					$new_items[] = array(
						'label' => ucfirst( $item ),
						'value' => esc_attr( (string) $item ),
						'data' => array(),
						'class' => '',
						'attr' => array(),
					);
				} else {
					$new_items[] = array(
						'label' => $item,
						'value' => esc_attr( (string) $i_key ),
						'data' => array(),
						'class' => '',
						'attr' => array(),
					);
				}
			}
		}

		return $new_items;
	}

	/**
	 * A helper function to put checked="checked" on inputs
	 *
	 * Compares the value $checked and $value
	 *
	 * If both are equal with === operator, then prints the attribute
	 *
	 * @param      string   $checked  The checked value
	 * @param      string   $value    Current element value
	 * @param      boolean  $echo     Whether to echo or not
	 *
	 * @return     string   The HTML attribute
	 */
	public function checked( $checked, $value, $echo = false ) {
		return $this->_checked_selected_helper( $checked, $value, $echo );
	}

	/**
	 * A helper function to put selected="selected" on inputs
	 *
	 * Compares the value $selected and $value
	 *
	 * If both are equal with === operator, then prints the attribute
	 *
	 * @param      string   $selected  The selected value
	 * @param      string   $value     Current element value
	 * @param      boolean  $echo      Whether to echo or not
	 *
	 * @return     string   The HTML attribute
	 */
	public function selected( $selected, $value, $echo = false ) {
		return $this->_checked_selected_helper( $selected, $value, $echo, 'selected' );
	}

	/**
	 * Helper for checked and selected generators
	 *
	 * An internal abstraction because comparison is same
	 *
	 * @access private
	 *
	 * @param      string   $actual  The actual value
	 * @param      string   $value   Current element value
	 * @param      boolean  $echo    Whether to echo
	 * @param      string   $helper  HTML attribute ( checked|selected )
	 *
	 * @return     string   The resulting HTML attribute
	 */
	private function _checked_selected_helper( $actual, $value, $echo = false, $helper = 'checked' ) {
		$return = '';
		if ( is_array( $actual ) ) {
			if ( in_array( $value, $actual, true ) ) {
				$return = ' ' . $helper . '="' . $helper . '"';
			}
		} elseif ( is_string( $actual ) ) {
			if ( $value === $actual ) {
				$return = ' ' . $helper . '="' . $helper . '"';
			}
		}
		if ( $echo ) {
			echo $return;
		}
		return $return;
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
