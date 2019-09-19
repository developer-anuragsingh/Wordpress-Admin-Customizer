<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://anurag-singh.github.io/
 * @since      1.0.0
 *
 * @package    Wordpress-Admin-Customizer
 * @subpackage Wordpress-Admin-Customizer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wordpress-Admin-Customizer
 * @subpackage Wordpress-Admin-Customizer/admin
 * @author     Anurag Singh <developer.anuragsingh@outlook.com>
 */
class Wordpress_Admin_Customizer_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $Wordpress_Admin_Customizer    The ID of this plugin.
	 */
	private $Wordpress_Admin_Customizer;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $Wordpress_Admin_Customizer       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $Wordpress_Admin_Customizer, $version ) {

		$Wordpress_Admin_Customizer = $Wordpress_Admin_Customizer;
		$this->version = $version;

		// add menu, sub-menu & tabs
		$parentMenu = $this->setup_parent_menu();
		$this->setup_fields_for_parent_menu($parentMenu);

		$loginScreenTabParentMenu = $this->setup_login_screen_tab($parentMenu);
		$this->setup_fields_for_login_screen ($loginScreenTabParentMenu);

		$userProfileTabParentMenu = $this->setup_users_profile_tab($parentMenu);
		$this->setup_fields_for_users_profile_tab ($userProfileTabParentMenu);

		$phpMailerTabParentMenu = $this->setup_php_mailer_tab($parentMenu);
		$this->setup_fields_for_php_mailer_tab ($phpMailerTabParentMenu);

		$phpMailerTabParentMenu = $this->setup_slider_tab($parentMenu);
		$this->setup_fields_for_slider_tab ($phpMailerTabParentMenu);

		$googleServicesTabParentMenu = $this->setup_google_services_tab($parentMenu);
		$this->setup_fields_for_google_services_tab ($googleServicesTabParentMenu);

		$htmlSitemapTabParentMenu = $this->setup_html_sitemap_tab($parentMenu);
		$this->setup_fields_for_html_sitemap_tab ($htmlSitemapTabParentMenu);

		$subMenu = $this->setup_submenu($parentMenu);
		$this->setup_tabs_for_sub_menu($subMenu);

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress-Admin-Customizer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress-Admin-Customizer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $Wordpress_Admin_Customizer, plugin_dir_url( __FILE__ ) . 'css/as-wp-admin-customization-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordpress-Admin-Customizer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordpress-Admin-Customizer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $Wordpress_Admin_Customizer, plugin_dir_url( __FILE__ ) . 'js/as-wp-admin-customization-admin.js', array( 'jquery' ), $this->version, false );

	}

	// Sanitize text - replace underscore (_) with space ( ) into a string
	public function sanitize_text($str) {
		return ucwords(str_replace("_", " ", $str));	// Uppercase and replace '_' with space(' ')
	}

	/**
	 * Create custom  menu, sub-menu and tabs for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function setup_parent_menu() {
		$parent_menu_title 	= 'Admin UI';

		// Custom WordPress Parent Menu
		$parentMenu = new WordPressMenu( array(
			'title' => $parent_menu_title,
			'slug' 	=> sanitize_title( $parent_menu_title ),
			'desc' 	=> 'Settings for admin area.',
			'icon' 	=> 'dashicons-welcome-widgets-menus',
			'position' => 99,
		));

		return $parentMenu;
	}

	/**
	 * Create input fields parent menu (Default tab).
	 *
	 * @since    1.0.0
	 */
	public function setup_fields_for_parent_menu($parentMenu) {
		$site_favicon				=	'Favicon';
		$remove_wp_logo				= 	'Remove Wordpress Logo';
		$replace_howdy_text			= 	'Replace Howdy Text';
		$remove_dashboard_widget	= 	'Remove Dashboard Widget';
		$reorder_menu_items			= 	'Reorder Admin Menu Items';
		$disable_comments			= 	'Disable Comments';
		$disable_updates			= 	'Disable Updates';


		// Get all the post types of website
		$postTypeArr = get_post_types();
		$postTypeArr = array_merge($postTypeArr, array('all'=>'all')); 		// add 'all' as an option
		$postTypeOptions = array_map(array($this, 'sanitize_text'), $postTypeArr);

		// echo '<pre>';
		// print_r($postTypeOptions);
		// Get all the post types of website


		//Add a field
		$parentMenu->add_field(array(
			'title' => $site_favicon,
			'name' => sanitize_title( $site_favicon ),
			'desc' => "Favicon of site.",
			// 'type' => 'radio',
			// 'options' => array(
			// 	TRUE => 'Yes',
			// 	FALSE => 'No' )
		));

		//Add a field
		$parentMenu->add_field(array(
			'title' => $remove_wp_logo,
			'name' => sanitize_title( $remove_wp_logo ),
			'desc' => "Remove wordpress logo from TLS.",
			'type' => 'radio',
			'options' => array(
				TRUE => 'Yes',
				FALSE => 'No' )
		));

		// Add a field
		$parentMenu->add_field(array(
			'title' => $replace_howdy_text,
			'name' => sanitize_title( $replace_howdy_text ),
			'desc' => 'Change "Howdy" Text from Top Right Section of admin area',
		));

		//Add a field
		$parentMenu->add_field(array(
			'title' => $remove_dashboard_widget,
			'name' => sanitize_title( $remove_dashboard_widget ),
			'desc' => "Remove dashboard widgets",
			'type' => 'radio',
			'options' => array(
				FALSE => 'No',
				TRUE => 'Yes',
			)
		));

		// Add a field
		$parentMenu->add_field(array(
			'title' => $reorder_menu_items,
			'name' => sanitize_title( $reorder_menu_items ),
			'desc' => 'Re-arrange menu item order of admin\'s menu',
			'type' => 'radio',
			'options' => array(
				FALSE => 'No',
				TRUE => 'Yes',
			)
		));

		// Add a field
		$parentMenu->add_field(array(
			'title' => $disable_comments,
			'name' => sanitize_title( $disable_comments ),
			'desc' => 'Disable Comments',
			'type' => 'radio',
			'options' => array(
				FALSE => 'No',
				TRUE => 'Yes',
			)
		));

		// Add a field
		$parentMenu->add_field(array(
			'title' => $disable_updates,
			'name' => sanitize_title( $disable_updates ),
			'desc' => 'Disable Updates',
			'type' => 'radio',
			'options' => array(
				FALSE => 'No',
				TRUE => 'Yes',
			)
		));
	}

	/**
	 * Create custom tab for parent menu.
	 *
	 * @since    1.0.0
	 */
	function setup_login_screen_tab ($parentMenu) {
		$custom_tab_title 	= 'Login Screen';

		// Creating tab with our custom wordpress menu
		$loginScreenTab = new WordPressMenuTab(
			array(
				'title' => $custom_tab_title,
				'slug'	=> sanitize_title( $custom_tab_title ),
				 ),
			$parentMenu
		);

		return $loginScreenTab;
	}

	/**
	 * Create fields for custom tab in parent menu.
	 *
	 * @since    1.0.0
	 */
	function setup_fields_for_login_screen ($loginScreenTab) {
		$developer_logo_url			= 'Logo Url';
		$developer_logo_title		= 'Logo Title';
		$checked_remember_me_always	= 'Check Remember Me Always';
		$developer_contact_no		= 'Contact No';
		$developer_email			= 'Email';
		$developer_website			= 'Website';

		//Add a field
		$loginScreenTab->add_field(array(
			'title' => $developer_logo_url,
			'name' => sanitize_title( $developer_logo_url ),
			'desc' => 'User will redirect to this page once they click on logo.',
		));

		//Add a field
		$loginScreenTab->add_field(array(
			'title' => $developer_logo_title,
			'name' => sanitize_title( $developer_logo_title ),
			'desc' => 'Text will display once user hover mouse on logo.',
		));

		//Add a field
		$loginScreenTab->add_field(array(
			'title' => $checked_remember_me_always,
			'name' => sanitize_title( $checked_remember_me_always ),
			'desc' => "'Remember me' option always be checked. ",
			'type' => 'radio',
			'options' => array(
				TRUE => 'Yes',
				FALSE => 'No' )
		));

		//Add a field
		$loginScreenTab->add_field(array(
			'title' => $developer_contact_no,
			'name' => sanitize_title( $developer_contact_no ),
			'desc' => 'Text will display once user hover mouse on logo.',
		));

		//Add a field
		$loginScreenTab->add_field(array(
			'title' => $developer_email,
			'name' => sanitize_title( $developer_email ),
			'desc' => 'Text will display once user hover mouse on logo.',
		));

		//Add a field
		$loginScreenTab->add_field(array(
			'title' => $developer_website,
			'name' => sanitize_title( $developer_website ),
			'desc' => 'Text will display once user hover mouse on logo.',
		));
	}

	/**
	 * Create custom tab for parent menu.
	 *
	 * @since    1.0.0
	 */
	function setup_users_profile_tab ($parentMenu) {
		$custom_tab_title 	= 'User\'s Profile';

		// Creating tab with our custom wordpress menu
		$userProfileTab = new WordPressMenuTab(
			array(
				'title' => $custom_tab_title,
				'slug'	=> sanitize_title( $custom_tab_title ),
				 ),
			$parentMenu
		);

		return $userProfileTab;
	}

	/**
	 * Create fields for custom tab in parent menu.
	 *
	 * @since    1.0.0
	 */
	function setup_fields_for_users_profile_tab ($userProfileTab) {

		$user_contact_no			= 'User\'s Contact No';
		$user_fb_link				= 'User\'s Facebook Link';
		$user_tw_link				= 'User\'s Twitter Link';
		$user_li_link				= 'User\'s Linkedin Link';


		// $checked_remember_me_always	= 'Check Remember Me Always';

		// $developer_contact_no		= 'Contact No';
		// $developer_email			= 'Email';
		// $developer_website			= 'Website';
		// $field_title_2				= 'Field 2';
		// $field_title_3				= 'Field 3';
		// $field_title_4				= 'Field 4';
		// $field_title_5				= 'Field 5';
		// $field_title_6				= 'Field 6';



		// //Add a field
		// $userProfileTab->add_field(array(
		// 	'title' => $developer_logo_title,
		// 	'name' => sanitize_title( $developer_logo_title ),
		// 	'desc' => 'Text will display once user hover mouse on logo.',
		// ));



		// Add a field
		$userProfileTab->add_field(array(
			'title' => $user_contact_no,
			'name' => sanitize_title( $user_contact_no ),
			'desc' => 'Add Contact No. field in user\'s profile',
			'type' => 'radio',
			'options' => array(
				FALSE => 'No',
				TRUE => 'Yes',
			)
		));

		// Add a field
		$userProfileTab->add_field(array(
			'title' => $user_fb_link,
			'name' => sanitize_title( $user_fb_link ),
			'desc' => 'Add Facebook profile link in user\'s profile',
			'type' => 'radio',
			'options' => array(
				FALSE => 'No',
				TRUE => 'Yes',
			)
		));

		// Add a field
		$userProfileTab->add_field(array(
			'title' => $user_tw_link,
			'name' => sanitize_title( $user_tw_link ),
			'desc' => 'Add Twitter profile link in user\'s profile',
			'type' => 'radio',
			'options' => array(
				FALSE => 'No',
				TRUE => 'Yes',
			)
		));

		// Add a field
		$userProfileTab->add_field(array(
			'title' => $user_li_link,
			'name' => sanitize_title( $user_li_link ),
			'desc' => 'Add Linkedin profile link in user\'s profile',
			'type' => 'radio',
			'options' => array(
				FALSE => 'No',
				TRUE => 'Yes',
			)
		));




		//Add a field
		// $userProfileTab->add_field(array(
		// 	'title' => $developer_contact_no,
		// 	'name' => sanitize_title( $developer_contact_no ),
		// 	'desc' => 'Text will display once user hover mouse on logo.',
		// ));


		// //Add a field
		// $userProfileTab->add_field(array(
		// 	'title' => $developer_email,
		// 	'name' => sanitize_title( $developer_email ),
		// 	'desc' => 'Text will display once user hover mouse on logo.',
		// ));

		// //Add a field
		// $userProfileTab->add_field(array(
		// 	'title' => $developer_website,
		// 	'name' => sanitize_title( $developer_website ),
		// 	'desc' => 'Text will display once user hover mouse on logo.',
		// ));




		//Add a field
		// $userProfileTab->add_field(array(
		// 	'title' => $field_title_2,
		// 	'name' => sanitize_title( $field_title_2 ),
		// 	'desc' => 'Input Description',
		// 	'type' => 'textarea'
		// ));

		// //Add a field
		// $userProfileTab->add_field(array(
		// 	'title' => $field_title_3,
		// 	'name' => sanitize_title( $field_title_3 ),
		// 	'desc' => 'Input Description',
		// 	'type' => 'wpeditor'
		// ));

		// //Add a field
		// $userProfileTab->add_field(array(
		// 	'title' => $field_title_4,
		// 	'name' => sanitize_title( $field_title_4 ),
		// 	'desc' => 'Check it to wake it',
		// 	'type' => 'checkbox'
		// ));

		// //Add a field
		// $userProfileTab->add_field(array(
		// 	'title' => $field_title_5,
		// 	'name' => sanitize_title( $field_title_5 ),
		// 	'desc' => 'Check it to wake it',
		// 	'type' => 'radio',
		// 	'options' => array(
		// 		'one' => 'Option one',
		// 		'two' => 'Option two' )
		// ));

		// //Add a field
		// $userProfileTab->add_field(array(
		// 	'title' => $field_title_6,
		// 	'name' => sanitize_title( $field_title_6 ),
		// 	'type' => 'select',
		// 	'options' => array(
		// 		'one' => 'Option one',
		// 		'two' => 'Option two' ) ) );
		// Custom WordPress Parent Menu
	}

	/**
	 * Create custom tab for parent menu.
	 *
	 * @since    1.0.0
	 */
	function setup_php_mailer_tab ($parentMenu) {
		$custom_tab_title 	= 'PHP Mailer';

		// Creating tab with our custom wordpress menu
		$phpMailerTab = new WordPressMenuTab(
			array(
				'title' => $custom_tab_title,
				'slug'	=> sanitize_title( $custom_tab_title ),
				 ),
			$parentMenu
		);

		return $phpMailerTab;
	}



	/**
	 * Create fields for custom tab in parent menu.
	 *
	 * @since    1.0.0
	 */
	function setup_fields_for_php_mailer_tab ($phpMailerTab) {

		$smtp_enable				= 'Smtp Enabled';
		$smtp_host					= 'Smtp Host';
		$smtp_port					= 'Smtp Port';
		$smtp_username				= 'Smtp Username';
		$smtp_password				= 'Smtp Password';
		$smtp_secure				= 'Smtp Secure';
		$from_email_id				= 'Smtp Email Id';
		$from_email_name			= 'Smtp Email Name';


		// Add a field
		$phpMailerTab->add_field(array(
			'title' => $smtp_enable,
			'name' => sanitize_title( $smtp_enable ),
			'desc' => 'Enable or disable SMTP support.',
			'type' => 'radio',
			'options' => array(
				FALSE => 'No',
				TRUE => 'Yes',
			)
		));


		// Add a field
		$phpMailerTab->add_field(array(
			'title' => $smtp_host,
			'name' => sanitize_title( $smtp_host ),
			'desc' => 'domainname.com',
		));

		// Add a field
		$phpMailerTab->add_field(array(
			'title' => $smtp_port,
			'name' => sanitize_title( $smtp_port ),
			'desc' => '25',
		));

		// Add a field
		$phpMailerTab->add_field(array(
			'title' => $smtp_username,
			'name' => sanitize_title( $smtp_username ),
			'desc' => 'username@yourdomain.com',
		));

		// Add a field
		$phpMailerTab->add_field(array(
			'title' => $smtp_password,
			'name' => sanitize_title( $smtp_password ),
			'desc' => '*******',
		));

		// Add a field
		$phpMailerTab->add_field(array(
			'title' => $smtp_secure,
			'name' => sanitize_title( $smtp_secure ),
			'desc' => 'Choose SSL or TLS, if necessary for your server, Themes & Plugins.',
		));

		// Add a field
		$phpMailerTab->add_field(array(
			'title' => $from_email_id,
			'name' => sanitize_title( $from_email_id ),
			'desc' => 'no-reply@domainname.com',
		));

		// Add a field
		$phpMailerTab->add_field(array(
			'title' => $from_email_name,
			'name' => sanitize_title( $from_email_name ),
			'desc' => 'Your Name',
		));
	}

	/**
	 * Create custom tab for parent menu.
	 *
	 * @since    1.0.0
	 */
	function setup_slider_tab ($parentMenu) {
		$custom_tab_title 	= 'Slider';

		// Creating tab with our custom wordpress menu
		$loginScreenTab = new WordPressMenuTab(
			array(
				'title' => $custom_tab_title,
				'slug'	=> sanitize_title( $custom_tab_title ),
				 ),
			$parentMenu
		);

		return $loginScreenTab;
	}


	/**
	 * Create fields for custom tab in parent menu.
	 *
	 * @since    1.0.0
	 */
	function setup_fields_for_slider_tab ($sliderTab) {

		$enable_slider					= 'Slider Enabled';
		$mode_of_slider					= 'Slider Mode';
		$autoplay_slider				= 'Slider Autoplay';
		$display_image_captions			= 'Slider Captions';
		$display_pagination				= 'Slider Pagination';
		$display_controls_slider		= 'Slider Controls';
		$slider_speed					= 'Slider Speed';


		// Add a field
		$sliderTab->add_field(array(
			'title' => $enable_slider,
			'name' => sanitize_title( $enable_slider ),
			'desc' => 'Enable or disable image slider.',
			'type' => 'radio',
			'options' => array(
				FALSE => 'No',
				TRUE => 'Yes',
			)
		));

		// //Add a field
		$sliderTab->add_field(array(
			'title' => $mode_of_slider,
			'name' => sanitize_title( $mode_of_slider ),
			'type' => 'select',
			'options' => array( 'horizontal' => 'Horizontal', 'vertical' => 'Vertical', 'fade' => 'Fade'),
		));

		$sliderTab->add_field(array(
			'title' => $autoplay_slider,
			'name' => sanitize_title( $autoplay_slider ),
			'desc' => 'Autoplay slider images',
			'type' => 'radio',
			'options' => array(	FALSE => 'No', TRUE => 'Yes' )
		));

		$sliderTab->add_field(array(
			'title' => $display_image_captions,
			'name' => sanitize_title( $display_image_captions ),
			'desc' => 'Display caption on slider images',
			'type' => 'radio',
			'options' => array(	FALSE => 'No', TRUE => 'Yes' )
		));

		$sliderTab->add_field(array(
			'title' => $display_pagination,
			'name' => sanitize_title( $display_pagination ),
			'desc' => 'Display pagination on slider images',
			'type' => 'radio',
			'options' => array(	FALSE => 'No', TRUE => 'Yes' )
		));

		$sliderTab->add_field(array(
			'title' => $display_controls_slider,
			'name' => sanitize_title( $display_controls_slider ),
			'desc' => 'Display controls on slider images',
			'type' => 'radio',
			'options' => array(	FALSE => 'No', TRUE => 'Yes' )
		));

		$sliderTab->add_field(array(
			'title' => $slider_speed,
			'name' => sanitize_title( $slider_speed ),
			'desc' => 'Slider Speed <b>(1 Sec = 100)</b>',
		));

	}

	/**
	 * Create custom tab for parent menu.
	 *
	 * @since    1.0.0
	 */
	function setup_google_services_tab ($parentMenu) {
		$custom_tab_title 	= 'Google Services';

		// Creating tab with our custom wordpress menu
		$googleServicesTab = new WordPressMenuTab(
			array(
				'title' => $custom_tab_title,
				'slug'	=> sanitize_title( $custom_tab_title ),
				 ),
			$parentMenu
		);

		return $googleServicesTab;
	}


	/**
	 * Create fields for custom tab in parent menu.
	 *
	 * @since    1.0.0
	 */
	function setup_fields_for_google_services_tab ($googleServicesTab) {
		$google_webmaster				= 'Google Webmaster';
		$google_analytics				= 'Google Analytics';
		$google_recaptcha_site_key		= 'reCAPTCHA - (Site key)';
		$google_recaptcha_secret_key	= 'reCAPTCHA - (Secret key)';


		// Add a field
		$googleServicesTab->add_field(array(
			'title' => $google_webmaster,
			'name' => sanitize_title( $google_webmaster ),
			'desc' => 'xxxxxxxxxxxxxxxx',
		));

		// Add a field
		$googleServicesTab->add_field(array(
			'title' => $google_analytics,
			'name' => sanitize_title( $google_analytics ),
			'desc' => 'UA-60XXXXX3-X',
		));

		// Add a field
		$googleServicesTab->add_field(array(
			'title' => $google_recaptcha_site_key,
			'name' => sanitize_title( $google_recaptcha_site_key ),
			'desc' => 'xxxxxxxxxxxxxxxxxx',
		));

		// Add a field
		$googleServicesTab->add_field(array(
			'title' => $google_recaptcha_secret_key,
			'name' => sanitize_title( $google_recaptcha_secret_key ),
			'desc' => ' xxxxxxxxxxxxxxxxxx',
		));
	}

	/**
	 * Create custom tab for parent menu.
	 *
	 * @since    1.0.0
	 */
	function setup_html_sitemap_tab ($parentMenu) {
		$custom_tab_title 	= 'HTML Sitemap';

		// Creating tab with our custom wordpress menu
		$loginScreenTab = new WordPressMenuTab(
			array(
				'title' => $custom_tab_title,
				'slug'	=> sanitize_title( $custom_tab_title ),
				 ),
			$parentMenu
		);

		return $loginScreenTab;
	}

	/**
	 * Create fields for custom tab in parent menu.
	 *
	 * @since    1.0.0
	 */
	function setup_fields_for_html_sitemap_tab ($htmlSitemapTab) {

		$html_sitemap_enabled			= 'Html Sitemap Enabled';
		$pages_to_exclude				= 'Html Sitemap Pages To Exclude';

		//Add a field
		$htmlSitemapTab->add_field(array(
			'title' => $html_sitemap_enabled,
			'name' => sanitize_title( $html_sitemap_enabled ),
			'desc' => "Enable HTML Sitemap for website. Use shortcode - <b>[html_sitemap]</b>.",
			'type' => 'radio',
			'options' => array(
				TRUE => 'Yes',
				FALSE => 'No' )
		));

		//Add a field
		$htmlSitemapTab->add_field(array(
			'title' => $pages_to_exclude,
			'name' => sanitize_title( $pages_to_exclude ),
			'desc' => 'Page Title, which you want to exclude from sitemap. Write each name in seperated by new line.',
			'type' => 'textarea',
			'rows' => "10",
			'cols' => "30"
		));
	}


	/**
	 * Create custom sub menu and tabs for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function setup_submenu($parentMenu) {
		$child_menu_title 	= 	'Options';

		// Custom WordPress Sub Menu
		$subMenu = new WordPressSubMenu( array(
			'title' => $child_menu_title,
			'slug' => sanitize_title( $child_menu_title ),
			'desc' => 'Settings for custom WordPress SubMenu',
		), $parentMenu);
		// Custom WordPress Sub Menu

		return $subMenu;
	}

	/**
	 * Create custom sub menu and tabs for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function setup_tabs_for_sub_menu($subMenu) {
		$field_title_1		=	'Field';

		// Add field
		$subMenu->add_field(array(
			'title' => $field_title_1,
			'name' => sanitize_title( $field_title_1 ),
			'desc' => 'Check it to wake it',
			'type' => 'checkbox'
		));
	}
}
