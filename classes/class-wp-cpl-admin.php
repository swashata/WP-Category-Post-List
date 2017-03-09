<?php
/**
 * The administrative settings Class
 *
 * Creates pages in the admin area to provide UI for changing plugin settings
 *
 * @package WP Category Posts List Plugin
 * @subpackage System Classes
 * @author Swashata Ghosh <swashata@iptms.co>
 */

/**
 * Main Settings Class
 */
class WP_CPL_Settings extends WP_CPL_Admin {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->capability = 'manage_options';
		$this->action_nonce = 'wp_cpl_settings_nonce';

		parent::__construct();

		$this->icon = 'list-view';
	}

	/**
	 * Add to menu
	 */
	public function admin_menu() {
		$this->pagehook = add_options_page( __( 'WP Category Posts List Plugin Settings', 'wp-cpl' ), __( 'WP CPL', 'wp-cpl' ), $this->capability, 'wp_cpl_itg_page', array( $this, 'index' ) );
		parent::admin_menu();
	}

	/**
	 * Output our sweet admin page
	 */
	public function index() {
		global $wp_cpl_settings;
		$this->index_head( __( 'WP Category Posts List Settings', 'wp-cpl' ) );
		var_dump( $wp_cpl_settings );
		$this->index_foot();
	}
}

class WP_CPL_UI_Check extends WP_CPL_Admin {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->capability = 'manage_options';
		$this->action_nonce = 'wp_cpl_settings_nonce';

		parent::__construct();

		$this->icon = 'editor-kitchensink';
	}

	/**
	 * Add to menu
	 */
	public function admin_menu() {
		$this->pagehook = add_options_page( __( 'Check UI Elements', 'wp-cpl' ), __( 'CPL UI Test', 'wp-cpl' ), $this->capability, 'wp_cpl_ui_test', array( $this, 'index' ) );
		parent::admin_menu();
	}

	/**
	 * Output our sweet admin page
	 */
	public function index() {
		global $wp_cpl_settings;
		$this->index_head( __( 'Checking all UI Elements', 'wp-cpl' ) );
		// Do the fun stuff
		$tabs = array();
		// Interactions
		$tabs[] = array(
			'id' => 'wp_cpl_interactions',
			'label' => __( 'Interactions', 'wp-cpl' ),
			'callback' => array( array( $this, 'interactions' ), array() ),
			'scroll' => false,
			'classes' => array(),
			'has_inner_tab' => false,
		);

		// Invalid callback
		$tabs[] = array(
			'id' => 'wp_cpl_invalid',
			'label' => __( 'Invalid Callback', 'wp-cpl' ),
			'callback' => array( 'something_bogus', array() ),
			'scroll' => false,
			'classes' => array(),
			'has_inner_tab' => false,
		);
		$this->ui->ui_loader();
		echo '<div class="ipt_uif_ui_hidden_init">';
		$this->ui->tabs( $tabs );
		echo '</div>';
		$this->index_foot( true, true );
	}

	public function interactions() {
		// Msgs
		$this->ui->msg_okay( __( 'This is an OKAY message', 'wp-cpl' ) );
		$this->ui->msg_update( __( 'This is an Update message', 'wp-cpl' ) );
		$this->ui->msg_error( __( 'This is an ERROR message', 'wp-cpl' ) );
	}
}

/**
 * Main Abstract class for creating many admin pages
 *
 * Check the methods and examples
 */
abstract class WP_CPL_Admin {
	/**
	 * Duplicates the $_POST content and properly process it
	 * Holds the typecasted (converted int and floats properly and escaped html) value after the constructor has been called
	 *
	 * @var array
	 */
	public $post = array();

	/**
	 * Holds the hook of this page
	 *
	 * @var string Pagehook
	 * Should be set during the construction
	 */
	public $pagehook;

	/**
	 * The nonce for admin-post.php
	 * Should be set the by extending class
	 *
	 * @var string
	 */
	public $action_nonce;

	/**
	 * The class of the admin page icon
	 * Should be set by the extending class
	 *
	 * @var string
	 */
	public $icon;

	/**
	 * This gets passed directly to current_user_can
	 * Used for security and should be set by the extending class
	 *
	 * @var string
	 */
	public $capability;

	/**
	 * Holds the post result message string
	 * Each entry is an associative array with the following options
	 *
	 * $key : The code of the post_result value =>
	 *
	 *      'type' => 'update' : The class of the message div update | error
	 *
	 *      'msg' => '' : The message to be displayed
	 *
	 * @var array
	 */
	public $post_result = array();

	/**
	 * The action value to be used for admin-post.php
	 * This is generated automatically by appending _post_action to the action_nonce variable
	 *
	 * @var string
	 */
	public $admin_post_action;

	/**
	 * Whether or not to print form on the admin wrap page
	 * Mainly for manually printing the form
	 *
	 * @var bool
	 */
	public $print_form;

	/**
	 * The USER INTERFACE Object
	 *
	 * @var WP_CPL_Admin_UI
	 */
	public $ui;

	/**
	 * Constructor function
	 *
	 * Does all the hooking and page loading
	 *
	 * @param      boolean  $gets_hooked  Whether this would actually create a
	 *                                    page in the admin menu
	 */
	public function __construct( $gets_hooked = true ) {
		// A shortcut for getting the post values
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			// we do not need to check on magic quotes
			// as wordpress always adds magic quotes
			// @link {http://codex.wordpress.org/Function_Reference/stripslashes_deep}
			$this->post = wp_unslash( $_POST );
		}

		// Store the UI
		$this->ui = WP_CPL_Admin_UI::get_instance();

		// Default action messages
		$this->post_result = array(
			1 => array(
				'type' => 'update',
				'msg' => __( 'Successfully saved the options.', 'wp-cpl' ),
			),
			2 => array(
				'type' => 'error',
				'msg' => __( 'Either you have not changed anything or some error has occured. Please contact the developer.', 'wp-cpl' ),
			),
			3 => array(
				'type' => 'okay',
				'msg' => __( 'The Master Reset was successful.', 'wp-cpl' ),
			),
		);
		// Calculate the admin post action
		$this->admin_post_action = $this->action_nonce . '_post_action';

		// Add pages
		if ( $gets_hooked ) {
			// register admin_menu hook
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

			// register admin-post.php hook
			add_action( 'admin_post_' . $this->admin_post_action, array( $this, 'save_post' ) );
		}
	}

	/*==========================================================================
	 * System Methods
	 *========================================================================*/
	/**
	 * Hook to the admin menu Should be overriden and also the hook should be
	 * saved in the $this->pagehook In the end, the parent::admin_menu() should
	 * be called for load to hooked properly
	 */
	public function admin_menu() {
		add_action( 'load-' . $this->pagehook, array( $this, 'on_load_page' ) );
		// $this->pagehook = add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function);
		// do the above or similar in the overriden callback function
	}

	/**
	 * Use this to generate the admin page always call parent::index() so the
	 * save post is called also call $this->index_foot() after the generation of
	 * page (the last line of this function) to give some compatibility (mainly
	 * with the metaboxes)
	 *
	 * @access     public
	 */
	abstract public function index();

	/**
	 * Prints the head of the page
	 *
	 * This effectively wraps everything into a class where UI is applied
	 * Also Prints a form if needed
	 *
	 * @param      string   $title       The page title
	 * @param      boolean  $print_form  Whether to print an HTML form
	 * @param      boolean  $apply_ui    Whether to apply User Interface JS & CSS
	 */
	protected function index_head( $title, $print_form = true, $apply_ui = true ) {
		// Store the print form for use in index_foot
		$this->print_form = $print_form;

		// Generic UI class
		$ui_class = array( 'wp-cpl-admin-ui' );
		if ( true == $apply_ui ) {
			$ui_class[] = 'wp-cpl-backoffice'; // This is the class which will get JS init
		}
		// The default WP Core class
		$ui_class[] = 'wrap';
		?>
<div class="<?php echo esc_attr( implode( ' ', $ui_class ) ); ?>" id="<?php echo $this->pagehook; ?>_widgets">
	<div class="icon32">
		<span class="dashicons dashicons-<?php echo $this->icon; ?>"></span>
	</div>
	<h2><?php echo $title; ?></h2>
	<?php $this->ui->clear(); ?>
	<?php
	if ( isset( $_GET['post_result'] ) ) {
		$msg = @$this->post_result[ (int) $_GET['post_result'] ]; // Suppress a PHP Notice if msg not found
		if ( ! empty( $msg ) ) {
			if ( 'update' == $msg['type'] || 'updated' == $msg['type'] ) {
				$this->ui->msg_update( $msg['msg'] );
			} else if ( 'okay' == $msg['type']  ) {
				$this->ui->msg_okay( $msg['msg'] );
			} else {
				$this->ui->msg_error( $msg['msg'] );
			}
		}
	}
	?>
	<?php if ( $this->print_form ) : ?>
		<form method="post" action="admin-post.php" id="<?php echo $this->pagehook; ?>_form_primary">
			<input type="hidden" name="action" value="<?php echo $this->admin_post_action; ?>" />
			<?php wp_nonce_field( $this->action_nonce, $this->action_nonce ); ?>
	<?php endif; ?>
	<?php do_action( $this->pagehook . '_page_before', $this ); ?>
		<?php
	}

	public function index_foot( $submit = true, $tab_submit = false, $do_action = true, $save = '', $reset = '' ) {
		// Calculate the buttons
		$save = ( empty( $save ) ? __( 'Save Changes', 'wp-cpl' ) : $save );
		$reset = ( empty( $reset ) ? __( 'Reset', 'wp-cpl' ) : $reset );
		$buttons = array(
			// Save Button
			0 => array( $save, 'submit', 'medium' ),
			// Reset
			1 => array( $reset, 'reset', 'medium' ),
		);
		$button_container_classes = array( 'ipt_uif_page_buttons' );
		if ( true == $tab_submit ) {
			$button_container_classes[] = 'ipt_uif_tab_buttons';
			$button_container_classes[] = 'ipt_uif_ui_hidden_init';
		}
		// Print the page footer
		?>
	<?php if ( $this->print_form ) : ?>
		<?php if ( $submit ) : ?>
			<?php $this->ui->clear(); ?>
			<?php $this->ui->buttons( $buttons, true, $this->pagehook . '_page_buttons', $button_container_classes ); ?>
		<?php endif; ?>
		</form>
		<?php $this->ui->clear(); ?>
	<?php endif; ?>
	<?php if ( $do_action ) : ?>
		<?php do_action( $this->pagehook . '_page_after', $this ); ?>
	<?php endif; ?>
</div>
		<?php
	}

	/**
	 * Override to manage the save_post This should be written by all the
	 * classes extending this
	 *
	 * For security do a parent::save_post beforehand
	 * It will stop if current user can not do the capability
	 *
	 * Also in case of checking referer, it will check from $_POST variable
	 *
	 * @param      boolean  $check_referer  The check referer
	 */
	public function save_post( $check_referer = true ) {
		// User permission check
		if ( ! current_user_can( $this->capability ) ) {
			wp_die( __( 'Cheatin&#8217; uh?' ) );
		}

		// Check nonce
		if ( $check_referer ) {
			if ( ! wp_verify_nonce( $_POST[$this->action_nonce], $this->action_nonce ) ) {
				wp_die( __( 'Cheatin&#8217; uh?' ) );
			}
		}

		// lets redirect the post request into get request
		// you may add additional params at the url
		// if you need to show save results
		//
		// wp_redirect( add_query_arg( array(), $_POST['_wp_http_referer'] ) );
		// The above should be done by the extending method
		// after calling parent::save_post and processing post
	}

	/**
	 * Hook to the load plugin page
	 * This should be overriden
	 * If you want to add screen options
	 *
	 */
	public function on_load_page() {

	}

	/**
	 * Get the pagehook of this class
	 *
	 * @return     string
	 */
	public function get_pagehook() {
		return $this->pagehook;
	}
}
