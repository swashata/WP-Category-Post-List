<?php
class WP_CPL_UI_Check extends WP_CPL_Admin {
	/**
	 * Constructor
	 */
	public function __construct() {
		$this->capability = 'manage_options';
		$this->action_nonce = 'wp_cpl_settings_nonce';

		parent::__construct();

		$this->icon = 'th-list';
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
			'icon' => 'pages',
		);

		// jQuery UI Elements
		$tabs[] = array(
			'id' => 'wp_cpl_jqui',
			'label' => __( 'jQuery UI', 'wp-cpl' ),
			'callback' => array( array( $this, 'jquery_ui' ), array() ),
			'scroll' => false,
			'classes' => array(),
			'has_inner_tab' => false,
			'icon' => 'pages',
		);

		// HTML UI Elements
		$tabs[] = array(
			'id' => 'wp_cpl_htmlui',
			'label' => __( 'HTML UI', 'wp-cpl' ),
			'callback' => array( array( $this, 'html_ui' ), array() ),
			'scroll' => false,
			'classes' => array(),
			'has_inner_tab' => true,
			'icon' => 'pages',
		);

		// Invalid callback
		$tabs[] = array(
			'id' => 'wp_cpl_invalid',
			'label' => __( 'Invalid Callback', 'wp-cpl' ),
			'callback' => array( 'something_bogus', array() ),
			'scroll' => false,
			'classes' => array(),
			'has_inner_tab' => false,
			'icon' => 'pages',
		);
		$this->ui->ui_loader();
		echo '<div class="ipt_uif_ui_hidden_init">';
		$this->ui->tabs( $tabs );
		echo '</div>';
		$this->index_foot( true, true );
	}

	public function html_ui() {
		$tabs = array();
		$tabs[] = array(
			'id' => 'wp_cpl_choice_ui',
			'label' => 'Choice UI',
			'callback' => array( array( $this, 'choice_ui' ), array() ),
			'scroll' => false,
			'has_inner_tab' => false,
		);
		$tabs[] = array(
			'id' => 'wp_cpl_input_ui',
			'label' => 'Input UI',
			'callback' => array( array( $this, 'input_ui' ), array() ),
			'scroll' => false,
			'has_inner_tab' => false,
		);
		$tabs[] = array(
			'id' => 'wp_cpl_helper_ui',
			'label' => 'Helper UI',
			'callback' => array( array( $this, 'helper_ui' ), array() ),
			'scroll' => false,
			'has_inner_tab' => false,
		);

		$this->ui->tabs( $tabs, false, true );
	}

	public function helper_ui() {
		$items = array();
		// Heading Type
		$items[] = array(
			'name' => 'hp_ht',
			'label' => 'Heading Type',
			'ui' => 'heading_type',
			'param' => array( 'hp_ht', 'h2' ),
			'help' => 'Desc',
		);
		// Layout Select
		$items[] = array(
			'name' => 'layout_select',
			'label' => 'Layout Select',
			'ui' => 'layout_select',
			'param' => array( 'layout_select', '2' ),
			'help' => 'Desc',
		);
		// Position Select
		$items[] = array(
			'name' => 'position_select',
			'label' => 'Position Select',
			'ui' => 'position_select',
			'param' => array( 'position_select', 'bottom', array( array( 'value' => 'cover', 'label' => '<span title="Testing Cover"><img src="https://i.imgur.com/0jdGwTy.jpg" /></span>' ) ) ),
			'help' => 'Desc',
		);
		// Alignment Select
		$items[] = array(
			'name' => 'alignment_select',
			'label' => 'Alignment Select',
			'ui' => 'alignment_select',
			'param' => array( 'alignment_select', 'right' ),
			'help' => 'Desc',
		);

		$this->ui->form_table( $items );
	}

	public function input_ui() {
		$items = array();
		// Text
		$items[] = array(
			'name' => 'ip_text',
			'label' => 'Input Text',
			'ui' => 'text',
			'param' => array( 'ip_text', 'value', 'placeholder' ),
			'help' => 'Desc',
		);
		// Number
		$items[] = array(
			'name' => 'ip_num',
			'label' => 'Input Number',
			'ui' => 'text',
			'param' => array( 'ip_num', '10', 'placeholder', 'number' ),
			'help' => 'Desc',
		);
		// Password
		$items[] = array(
			'name' => 'ip_pass',
			'label' => 'Input Password',
			'ui' => 'password',
			'param' => array( 'ip_pass', 'value', 'placeholder' ),
			'help' => 'Desc',
		);
		// Spinner
		$items[] = array(
			'name' => 'ip_spinner',
			'label' => 'Input Spinner',
			'ui' => 'spinner',
			'param' => array( 'ip_spinner', '50', 'placeholder', -10, 100, 2 ),
			'help' => 'Desc',
		);
		// Textarea
		$items[] = array(
			'name' => 'ip_textarea',
			'label' => 'Input Textarea',
			'ui' => 'textarea',
			'param' => array( 'ip_textarea', '<p>HTML</p>', 'placeholder', 10 ),
			'help' => 'Desc',
		);

		$this->ui->form_table( $items );
	}

	public function choice_ui() {
		$radio_items = array();
		$radio_items[] = array(
			'value' => '0',
			'label' => 'Item One (CondID)',
			'data' => array(
				'condid' => 'wp-cpl-form-ui-cond-rd',
			),
		);
		$radio_items[] = array(
			'value' => '1',
			'label' => 'Item Two',
		);
		$radio_items[] = array(
			'value' => '2',
			'label' => 'Another Option',
		);
		$radio_items[] = array(
			'value' => '3',
			'label' => 'Why Not Another Option',
		);
		$radio_items[] = array(
			'value' => '4',
			'label' => 'The Last One',
		);
		$checkbox_items = $radio_items;
		$checkbox_items[0]['data']['condid'] = 'wp-cpl-form-ui-cond-cb';

		$select_items = $radio_items;
		$select_items[0]['data']['condid'] = 'wp-cpl-form-ui-cond-sl';
		?>
<table class="form-table">
	<tbody>
		<tr>
			<th><?php $this->ui->generate_label( 'cond_radio', 'Conditional Radio Elements' ); ?></th>
			<td><?php $this->ui->radios( 'cond_radio', $radio_items, '3', true, 4 ); ?></td>
			<td><?php $this->ui->help( 'Desc' ); ?></td>
		</tr>
		<tr id="wp-cpl-form-ui-cond-rd">
			<td colspan="3">
				<label>Conditional Radio Output</label>
			</td>
		</tr>
		<tr>
			<th><?php $this->ui->generate_label( 'cond_checkbox', 'Conditional Checkbox Elements' ); ?></th>
			<td><?php $this->ui->checkboxes( 'cond_checkbox[]', $checkbox_items, '3', true, 4 ); ?></td>
			<td><?php $this->ui->help( 'Desc' ); ?></td>
		</tr>
		<tr id="wp-cpl-form-ui-cond-cb">
			<td colspan="3">
				<label>Conditional Checkbox Output</label>
			</td>
		</tr>
		<tr>
			<td colspan="2"><?php $this->ui->checkbox( 'single_cb', '1', 'Check me', false, true, 'wp-cpl-form-ui-cond-scb' ) ?></td>
			<td><?php $this->ui->help( 'Desc' ); ?></td>
		</tr>
		<tr id="wp-cpl-form-ui-cond-scb">
			<td colspan="3">
				<label>Conditional Single Checkbox Output</label>
			</td>
		</tr>
		<tr>
			<th><?php $this->ui->generate_label( 'cond_toggle', 'Conditional Toggle Element' ); ?></th>
			<td><?php $this->ui->toggle( 'cond_toggle', 'On', 'Off', true, '1', true, $data = array( 'condid' => 'wp-cpl-form-ui-cond-tog' ) ); ?></td>
			<td><?php $this->ui->help( 'Desc' ); ?></td>
		</tr>
		<tr id="wp-cpl-form-ui-cond-tog">
			<td colspan="3">
				<label>Conditional Toggle Output</label>
			</td>
		</tr>
		<tr>
			<th><?php $this->ui->generate_label( 'cond_select', 'Conditional Select Element' ); ?></th>
			<td>
				<?php $this->ui->select( 'cond_select', $select_items, array( '1', '4' ), true, true ); ?>
			</td>
			<td><?php $this->ui->help( 'Desc' ); ?></td>
		</tr>
		<tr id="wp-cpl-form-ui-cond-sl">
			<td colspan="3">
				<label>Conditional Select Output</label>
			</td>
		</tr>
	</tbody>
</table>
		<?php
	}

	public function interactions() {
		// Msgs
		$this->ui->msg_okay( __( 'This is an OKAY message', 'wp-cpl' ) );
		$this->ui->msg_update( __( 'This is an Update message', 'wp-cpl' ) );
		$this->ui->msg_error( __( 'This is an ERROR message', 'wp-cpl' ) );
		$this->ui->clear();
		$this->ui->help( __( 'I am useful', 'wp-cpl' ), __( 'Align Left', 'wp-cpl' ), 'left' );
		$this->ui->help( __( 'I am useful', 'wp-cpl' ), __( 'Align Right', 'wp-cpl' ), 'right' );
		$this->ui->help( __( 'I am useful', 'wp-cpl' ), __( 'Align Inline', 'wp-cpl' ), 'inline' );
	}

	public function jquery_ui() {
		?>
<table class="form-table">
	<tbody>
		<tr>
			<th><?php $this->ui->generate_label( 'datepicker', __( 'DatePicker', 'wp-cpl' ) ); ?></th>
			<td><?php $this->ui->datepicker( 'datepicker', current_time( 'mysql' ), __( 'Enter', 'wp-cpl' ) ); ?></td>
		</tr>
		<tr>
			<th><?php $this->ui->generate_label( 'datetimepicker', __( 'DateTimePicker', 'wp-cpl' ) ); ?></th>
			<td><?php $this->ui->datetimepicker( 'datetimepicker', current_time( 'mysql' ), __( 'Enter', 'wp-cpl' ) ); ?></td>
		</tr>
		<tr>
			<td colspan="2">
				<?php $this->ui->progressbar( '', 50 ); ?>
			</td>
		</tr>
		<tr>
			<th><?php $this->ui->generate_label( 'slider', __( 'Slider', 'wp-cpl' ) ); ?></th>
			<td><?php $this->ui->slider( 'slider', 50, -100, 100, 10 ); ?></td>
		</tr>
		<tr>
			<th><?php $this->ui->generate_label( 'range', __( 'Range', 'wp-cpl' ) ); ?></th>
			<td><?php $this->ui->slider_range( 'range', array( 20, 80 ), -100, 100, 10 ); ?></td>
		</tr>
		<tr>
			<th><?php $this->ui->generate_label( 'spinner', __( 'Spinner', 'wp-cpl' ) ); ?></th>
			<td><?php $this->ui->spinner( 'spinner', 50, __( 'Number', 'wp-cpl' ), -100, 100, 10 ); ?></td>
		</tr>
	</tbody>
</table>
		<?php
	}
}
