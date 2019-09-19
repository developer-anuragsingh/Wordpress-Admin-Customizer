<?php

class WordPressMenu extends WordPressSettings {

	/**
	 * Default options
	 * @var array
	 */
	public $defaultOptions = array(
		'slug' => '', // Name of the menu item
		'title' => '', // Title displayed on the top of the admin panel
		'page_title' => '',
		'parent' => null, // id of parent, if blank, then this is a top level menu
		'id' => '', // Unique ID of the menu item
		'capability' => 'manage_options', // User role
		'icon' => 'dashicons-admin-generic', // Menu icon for top level menus only http://melchoyce.github.io/dashicons/
		'position' => null, // Menu position. Can be used for both top and sub level menus
		'desc' => '', // Description displayed below the title
		'function' => ''
	);

	/**
	 * Gets populated on submenus, contains slug of parent menu
	 * @var null
	 */
	public $parent_id = null;

	/**
	 * Menu options
	 * @var array
	 */
	public $menu_options = array();

	function __construct( $options ) {

		$this->menu_options = array_merge( $this->defaultOptions, $options );

		if( $this->menu_options['slug'] == '' ){

			return;
		}

		$this->settings_id = $this->menu_options['slug'];

		$this->prepopulate();

		add_action( 'admin_menu', array( $this, 'add_page' ) );

		add_action( 'wordpressmenu_page_save_' . $this->settings_id, array( $this, 'save_settings' ) );

	}

	/**
	 * Populate some of required options
	 * @return void
	 */
	public function prepopulate() {

		if( $this->menu_options['title'] == '' ) {
			$this->menu_options['title'] = ucfirst( $this->menu_options['slug'] );
		}

		if( $this->menu_options['page_title'] == '' ) {
			$this->menu_options['page_title'] = $this->menu_options['title'];
		}

	}

	/**
	 * Add the menu page using WordPress API
	 * @return [type] [description]
	 */
	public function add_page() {
		$functionToUse = $this->menu_options['function'];
		if( $functionToUse == '' ) {
			$functionToUse = array( $this, 'create_menu_page' );
		}

		if( $this->parent_id != null ){
			 add_submenu_page( $this->parent_id,
				$this->menu_options['page_title'],
				$this->menu_options['title'],
				$this->menu_options['capability'],
				$this->menu_options['slug'],
				$functionToUse );
		} else {
			add_menu_page( $this->menu_options['page_title'],
				$this->menu_options['title'],
				$this->menu_options['capability'],
				$this->menu_options['slug'],
				$functionToUse,
				$this->menu_options['icon'],
				$this->menu_options['position'] );
		}

	}

	/**
	 * Create the menu page
	 * @return void
	 */
	public function create_menu_page() {
		$this->save_if_submit();
		$tab = 'general';
		if( isset( $_GET['tab'] ) ) {
			$tab = $_GET['tab'];
		}
		$this->init_settings();
		?>
		<div class="wrap">
			<h2><?php echo $this->menu_options['page_title'] ?></h2>
			<?php
				if ( ! empty( $this->menu_options['desc'] ) ) {
					?><p class='description'><?php echo $this->menu_options['desc'] ?></p><?php
				}
				$this->render_tabs( $tab );
			?>
			<form method="POST" action="">
				<div class="postbox">
					<div class="inside">
						<table class="form-table">
							<?php $this->render_fields( $tab ); ?>
						</table>
						<?php $this->save_button(); ?>
					</div>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the registered tabs
	 * @param  string $active_tab the viewed tab
	 * @return void
	 */
	public function render_tabs( $active_tab = 'general' ) {
		if( count( $this->tabs ) > 1 ) {
			echo '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">';
				foreach ($this->tabs as $key => $value) {
					echo '<a href="' . admin_url('admin.php?page=' . $this->menu_options['slug'] . '&tab=' . $key ) . '" class="nav-tab ' .  ( ( $key == $active_tab ) ? 'nav-tab-active' : '' ) . ' ">' . $value . '</a>';
				}
			echo '</h2>';
			echo '<br/>';
		}
	}
	/**
	 * Render the save button
	 * @return void
	 */
	protected function save_button() {
		?>
		<button type="submit" name="<?php echo $this->settings_id; ?>_save" class="button button-primary">
			<?php _e( 'Save', 'textdomain' ); ?>
		</button>
		<?php
	}
	/**
	 * Save if the button for this menu is submitted
	 * @return void
	 */
	protected function save_if_submit() {
		if( isset( $_POST[ $this->settings_id . '_save' ] ) ) {
			do_action( 'wordpressmenu_page_save_' . $this->settings_id );
		}
	}

	//...


}



class WordPressSubMenu extends WordPressMenu {
	function __construct( $options, WordPressMenu $parent ){
		parent::__construct( $options );
		$this->parent_id = $parent->settings_id;
	}
}


class WordPressMenuTab {
	public $slug;
	public $title;
	public $menu;
	function __construct( $options, WordPressMenu $menu ){
		$this->slug = $options['slug'];
		$this->title = $options['title'];
		$this->menu = $menu;
		$this->menu->add_tab( $options );
	}
	/**
	 * Add field to this tab
	 * @param [type] $array [description]
	 */
	public function add_field( $array ){
		$this->menu->add_field( $array, $this->slug );
	}
}


abstract class WordPressSettings {
	/**
	 * ID of the settings
	 * @var string
	 */
	public $settings_id = '';
	/**
	 * Tabs for the settings page
	 * @var array
	 */
	public $tabs = array(
		'general' => 'General' );
	/**
	 * Settings from database
	 * @var array
	 */
	protected $settings = array();
	/**
	 * Array of fields for the general tab
	 * array(
	 * 	'tab_slug' => array(
	 * 		'field_name' => array(),
	 * 		),
	 * 	)
	 * @var array
	 */
	protected $fields = array();
	/**
	 * Data gotten from POST
	 * @var array
	 */
	protected $posted_data = array();

	/**
	 * Get the settings from the database
	 * @return void
	 */
	public function init_settings() {
		$this->settings = (array) get_option( $this->settings_id );
		foreach ( $this->fields as $tab_key => $tab ) {

			foreach ( $tab as $name => $field ) {

				if( isset( $this->settings[ $name ] ) ) {
					$this->fields[ $tab_key ][ $name ]['default'] = $this->settings[ $name ];
				}

			}
		}
	}
	/**
	 * Save settings from POST
	 * @return [type] [description]
	 */
	public function save_settings(){

	 	$this->posted_data = $_POST;
	 	if( empty( $this->settings ) ) {
	 		$this->init_settings();
	 	}
	 	foreach ($this->fields as $tab => $tab_data ) {
	 		foreach ($tab_data as $name => $field) {

	 			$this->settings[ $name ] = $this->{ 'validate_' . $field['type'] }( $name );

	 		}
	 	}
	 	update_option( $this->settings_id, $this->settings );
	}
	/**
	 * Gets and option from the settings API, using defaults if necessary to prevent undefined notices.
	 *
	 * @param  string $key
	 * @param  mixed  $empty_value
	 * @return mixed  The value specified for the option or a default value for the option.
	 */
	public function get_option( $key, $empty_value = null ) {
		if ( empty( $this->settings ) ) {
			$this->init_settings();
		}
		// Get option default if unset.
		if ( ! isset( $this->settings[ $key ] ) ) {
			$form_fields = $this->fields;
			foreach ( $this->tabs as $tab_key => $tab_title ) {
				if( isset( $form_fields[ $tab_key ][ $key ] ) ) {
					$this->settings[ $key ] = isset( $form_fields[ $tab_key ][ $key ]['default'] ) ? $form_fields[ $tab_key ][ $key ]['default'] : '';

				}
			}

		}
		if ( ! is_null( $empty_value ) && empty( $this->settings[ $key ] ) && '' === $this->settings[ $key ] ) {
			$this->settings[ $key ] = $empty_value;
		}
		return $this->settings[ $key ];
	}

  /**
	 * Validate text field
	 * @param  string $key name of the field
	 * @return string
	 */
	public function validate_text( $key ){
		$text  = $this->get_option( $key );
		if ( isset( $this->posted_data[ $key ] ) ) {
			$text = wp_kses_post( trim( stripslashes( $this->posted_data[ $key ] ) ) );
		}
		return $text;
	}
	/**
	 * Validate textarea field
	 * @param  string $key name of the field
	 * @return string
	 */
	public function validate_textarea( $key ){
		$text  = $this->get_option( $key );

		if ( isset( $this->posted_data[ $key ] ) ) {
			$text = wp_kses( trim( stripslashes( $this->posted_data[ $key ] ) ),
				array_merge(
					array(
						'iframe' => array( 'src' => true, 'style' => true, 'id' => true, 'class' => true )
					),
					wp_kses_allowed_html( 'post' )
				)
			);
		}
		return $text;
	}
	/**
	 * Validate WPEditor field
	 * @param  string $key name of the field
	 * @return string
	 */
	public function validate_wpeditor( $key ){
		$text  = $this->get_option( $key );

		if ( isset( $this->posted_data[ $key ] ) ) {
			$text = wp_kses( trim( stripslashes( $this->posted_data[ $key ] ) ),
				array_merge(
					array(
						'iframe' => array( 'src' => true, 'style' => true, 'id' => true, 'class' => true )
					),
					wp_kses_allowed_html( 'post' )
				)
			);
		}
		return $text;
	}
	/**
	 * Validate select field
	 * @param  string $key name of the field
	 * @return string
	 */
	public function validate_select( $key ) {
		$value = $this->get_option( $key );
		if ( isset( $this->posted_data[ $key ] ) ) {
			$value = stripslashes( $this->posted_data[ $key ] );
		}
		return $value;
	}
	/**
	 * Validate radio
	 * @param  string $key name of the field
	 * @return string
	 */
	public function validate_radio( $key ) {
		$value = $this->get_option( $key );
		if ( isset( $this->posted_data[ $key ] ) ) {
			$value = stripslashes( $this->posted_data[ $key ] );
		}
		return $value;
	}
	/**
	 * Validate checkbox field
	 * @param  string $key name of the field
	 * @return string
	 */
	public function validate_checkbox( $key ) {
		$status = '';
		if ( isset( $this->posted_data[ $key ] ) && ( 1 == $this->posted_data[ $key ] ) ) {
			$status = '1';
		}
		return $status;
	}

  /**
	 * Adding fields
	 * @param array $array options for the field to add
	 * @param string $tab tab for which the field is
	 */
	public function add_field( $array, $tab = 'general' ) {
		$allowed_field_types = array(
			'text',
			'textarea',
			'wpeditor',
			'select',
			'radio',
			'checkbox' );
		// If a type is set that is now allowed, don't add the field
		if( isset( $array['type'] ) &&$array['type'] != '' && ! in_array( $array['type'], $allowed_field_types ) ){
			return;
		}
		$defaults = array(
			'name' => '',
			'title' => '',
			'default' => '',
			'placeholder' => '',
			'type' => 'text',
			'options' => array(),
			'default' => '',
			'desc' => '',
			);
		$array = array_merge( $defaults, $array );
		if( $array['name'] == '' ) {
			return;
		}
		foreach ( $this->fields as $tabs ) {
			if( isset( $tabs[ $array['name'] ] ) ) {
				trigger_error( 'There is alreay a field with name ' . $array['name'] );
				return;
			}
		}
		// If there are options set, then use the first option as a default value
		if( ! empty( $array['options'] ) && $array['default'] == '' ) {
			$array_keys = array_keys( $array['options'] );
			$array['default'] = $array_keys[0];
		}
		if( ! isset( $this->fields[ $tab ] ) ) {
			$this->fields[ $tab ] = array();
		}
		$this->fields[ $tab ][ $array['name'] ] = $array;
	}

	/**
	 * Adding tab
	 * @param array $array options
	 */
	public function add_tab( $array ) {
		$defaults = array(
			'slug' => '',
			'title' => '' );
		$array = array_merge( $defaults, $array );
		if( $array['slug'] == '' || $array['title'] == '' ){
			return;
		}
		$this->tabs[ $array['slug'] ] = $array['title'];
	}

  /**
	 * Rendering fields
	 * @param  string $tab slug of tab
	 * @return void
	 */
	public function render_fields( $tab ) {
		if( ! isset( $this->fields[ $tab ] ) ) {
			echo '<p>' . __( 'There are no settings on these page.', 'textdomain' ) . '</p>';
			return;
		}
		foreach ( $this->fields[ $tab ] as $name => $field ) {

			$this->{ 'render_' . $field['type'] }( $field );
		}
	}

  /**
	 * Render text field
	 * @param  string $field options
	 * @return void
	 */
	public function render_text( $field ){
		extract( $field );
		?>

		<tr>
			<th>
				<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
			</th>
			<td>
				<input type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $default; ?>" placeholder="<?php echo $placeholder; ?>" />
				<?php if( $desc != '' ) {
					echo '<p class="description">' . $desc . '</p>';
				}?>
			</td>
		</tr>

		<?php
	}
	/**
	 * Render textarea field
	 * @param  string $field options
	 * @return void
	 */
	public function render_textarea( $field ){
		extract( $field );
		?>

		<tr>
			<th>
				<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
			</th>
			<td>
				  <!-- <textarea name="message" rows="10" cols="30">The cat was playing in the garden.</textarea> -->

				<textarea name="<?php echo $name; ?>" id="<?php echo $name; ?>" rows="<?php echo $rows; ?>" cols="<?php echo $cols; ?>" placeholder="<?php echo $placeholder; ?>" ><?php echo $default; ?></textarea>
				<?php if( $desc != '' ) {
					echo '<p class="description">' . $desc . '</p>';
				}?>
			</td>
		</tr>

		<?php
	}
	/**
	 * Render WPEditor field
	 * @param  string $field  options
	 * @return void
	 */
	public function render_wpeditor( $field ){

		extract( $field );
		?>

		<tr>
			<th>
				<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
			</th>
			<td>
				<?php wp_editor( $default, $name, array('wpautop' => false) ); ?>
				<?php if( $desc != '' ) {
					echo '<p class="description">' . $desc . '</p>';
				}?>
			</td>
		</tr>

		<?php
	}
	/**
	 * Render select field
	 * @param  string $field options
	 * @return void
	 */
	public function render_select( $field ) {
		extract( $field );
		?>

		<tr>
			<th>
				<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
			</th>
			<td>
				<select name="<?php echo $name; ?>" id="<?php echo $name; ?>" >
					<?php
						foreach ($options as $value => $text) {
							echo '<option ' . selected( $default, $value, false ) . ' value="' . $value . '">' . $text . '</option>';
						}
					?>
				</select>
				<?php if( $desc != '' ) {
					echo '<p class="description">' . $desc . '</p>';
				}?>
			</td>
		</tr>

		<?php
	}
	/**
	 * Render radio
	 * @param  string $field options
	 * @return void
	 */
	public function render_radio( $field ) {
		extract( $field );
		?>

		<tr>
			<th>
				<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
			</th>
			<td>
				<?php
					foreach ($options as $value => $text) {
						echo '<input name="' . $name . '" id="' . $name . '" type="'.  $type . '" ' . checked( $default, $value, false ) . ' value="' . $value . '">' . $text . '</option><br/>';
					}
				?>
				<?php if( $desc != '' ) {
					echo '<p class="description">' . $desc . '</p>';
				}?>
			</td>
		</tr>

		<?php
	}

	/**
	 * Render checkbox field
	 * @param  string $field options
	 * @return void
	 */
	public function render_checkbox( $field ) {
		extract( $field );
		?>


		<tr>
			<th>
				<label for="<?php echo $name; ?>"><?php echo $title; ?></label>
			</th>
			<td>
				<input <?php checked( $default, '1', true ); ?> type="<?php echo $type; ?>" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="1" placeholder="<?php echo $placeholder; ?>" />
				<?php echo $desc; ?>
			</td>
		</tr>


		<?php
	}

  //..

}