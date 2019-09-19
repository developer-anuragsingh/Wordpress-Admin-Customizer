<?php

/**
 * The login screen specific functionality of the plugin.
 *
 * @link       http://anuragsingh.me
 * @since      1.0.0
 *
 * @package    Wordpress_Admin_Customizer
 * @subpackage Wordpress_Admin_Customizer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wordpress_Admin_Customizer
 * @subpackage Wordpress_Admin_Customizer/admin
 * @author     Anurag Singh <developer.anuragsingh@outlook.com>
 */
class As_Wp_Admin_Customization_Admin_Login_Screen {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $as_wp_admin_customization    The ID of this plugin.
	 */
	private $as_wp_admin_customization;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 *  Prefix for plugin settings.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $option_name
	 */
	private $option_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $as_wp_admin_customization       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $as_wp_admin_customization, $version ) {

		$this->as_wp_admin_customization = $as_wp_admin_customization;
		$this->version = $version;
		$this->option_name = get_site_option('admin-customization');




	}

	/**
	 * Register the stylesheets for the admin login screen.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_login_screen_styles() {

		wp_enqueue_style( $this->as_wp_admin_customization.'-login-screen', plugin_dir_url( dirname(__FILE__) ) . '/css/as-wp-admin-customization-login-screen.css', array(), $this->version, 'all' );

	}


	/*
	 * Change logo of WP login Screen
	*/
	function login_screen_bg() {
		$bgFileNo = rand(1,12); // Randomly change login screen bg image
		?>
		<style type="text/css">
			.login {
				font-size: 16px;
				letter-spacing: 1px;
				text-shadow: -2px 2px 2px rgba(0,0,0, 0.3);
				background-position: center center;
				background-image: url(<?php echo plugin_dir_url( dirname(__FILE__) . '/../..' );?>/images/<?php echo $bgFileNo; ?>.gif);
			}
		</style>
	<?php
	}

	/*
	 * Change logo of WP login Screen logo
	*/
	function login_screen_logo() {
		$devLogoId = get_site_option('developer_logo_image');
		if(empty($devLogoId)) {
			$devLogoUrl = plugin_dir_url(dirname(__FILE__) . '/../..') . '/images/developer-logo.png';
		} else {
			$devLogoUrl = array_shift(wp_get_attachment_image_src($devLogoId, 'full' ));
		}
		?>
	    <style type="text/css">
	        #login h1 a, .login h1 a {
	        	/*height:150px;*/
	        	width:auto;
	            background-size:auto;
	            background-position: center center;
	            background-image: url(<?php echo $devLogoUrl ?>);
	            background-color: #000;
	            border: 1px solid #fff;
	            box-shadow: 0 -8px 6px -6px black;
	        }
	    </style>
	<?php }


	 /*
	 * Change logo URL on WP login Screen
	 */
	public function login_screen_logo_url() {
		$logoUrl = $this->option_name['logo-url'];

		if (empty($logoUrl)) {
			$logoUrl = get_bloginfo('url');
		}

		return esc_html($logoUrl);
	}

	/*
	 * Change logo URL on WP login Title
	 */
	public function login_screen_logo_title() {
		$logoTitle = $this->option_name['logo-title'];

		if (empty($logoTitle)) {
			$logoTitle = get_bloginfo('name');
		}

		return $logoTitle;
	}

	/*
	 * Make 'Remember me' option always checked
	 */
	public function always_check_remember_me() {
		$rememberMe = $this->option_name['check-remember-me-always'];

		if (!empty($rememberMe) && $rememberMe == TRUE) {
			add_filter( 'login_footer', array($this, 'remember_me_checked') );
		}

	}

	/*
	 *	keep 'Remember me' option checked
	 */
	public function remember_me_checked() {
		echo "<script>document.getElementById('rememberme').checked = true;</script>";
	}


	/*
	 * Redirect user to public view if they are not "Administrator"
	 */
	public function loginRedirect( $redirect_to, $request, $user ) {
		if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		    if( in_array('administrator', $user->roles)) {
		      	return admin_url();
		    } else {
		      	return site_url();
		    }
		  } else {
		      	return site_url();
		}
	}


	/*
	 * Add another link to header of login screen
	*/
	public function login_form_header() { ?>
	    <div class="wp-login-header-wrapper">
		    <h2>
		    	<!-- <a href="#">Anurag Singh</a> -->
		    </h2>

	    </div>
	<?php }


	/*
	 * Add another link to footer of login screen
	*/
	public function login_form_footer() { ?>
	    <div class="wp-login-footer-wrapper">
	    	<?php
	    		$devContactNo = $this->option_name['contact-no'];
	    		if(empty($devContactNo)){
	    			$devContactNo = "0000-000-000";
	    		}
	    		$devContactNoHref = trim(str_replace(array('-', ' '), '', $devContactNo)); // Sanitize contact no to href

	    		$devEmail = $this->option_name['email'];
	    		if(empty($devEmail)){
	    			$devEmail = get_option('email');
	    		}

	    		$devWebsite = $this->option_name['website'];
	    		if(empty($devWebsite)){
	    			$devWebsite = get_bloginfo('url');
	    		}
	    	?>
		    <ul class="wp-login-footer">
		    	<li id="phone">Phone : <a href="tel:<?php echo $devContactNoHref; ?>"><?php echo $devContactNo ?></a></li>
		    	<li id="email">Email : <a href="mailto:<?php echo $devEmail; ?>"><?php echo $devEmail; ?></a></li>
		    	<li id="website">Website : <a href="<?php echo $devWebsite; ?>"><?php echo $devWebsite; ?></a></li>
		    </ul>
	    </div>
	<?php }


	/*
	 * Remove WP logo from admin area
	 */
	public function remove_wp_logo_from_admin_screen( $wp_admin_bar ) {
		$removeWpLogo = $this->option_name['remove-wordpress-logo'];

		if(!empty($removeWpLogo) && $removeWpLogo == TRUE) {
			$wp_admin_bar->remove_node( 'wp-logo' );
		}
	}

	/*
	 * Change "Howdy" Text from admin area
	 */
	public function change_howdy_text_from_admin_area($translated, $text, $domain) {
		$replaceHowdyText = $this->option_name['replace-howdy-text'];

		if(!empty($replaceHowdyText)){
		    if (!is_admin() || 'default' != $domain)
		        return $translated;

		    if (false !== strpos($translated, 'Howdy'))
		        return str_replace('Howdy', $replaceHowdyText, $translated);

		    return $translated;
	    }
	}

	/*
	 * Remove widgets from user dashboard
	 */
	public function remove_dashboard_widgets () {
		$removeDashboardWidgets = $this->option_name['remove-dashboard-widget'];

		if (!empty($removeDashboardWidgets) && $removeDashboardWidgets == TRUE) {
			remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
	        remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );
	        remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	        remove_meta_box( 'dashboard_secondary', 'dashboard', 'normal' );
	        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	        remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
	        remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
	        remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
	        remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');//since 3.8
		}

	}


	/*
	 * Trigger a function to add a field in user's profile
	 */
	public function enable_additional_fields_in_user_profile( ) {
		if(	isset($this->option_name['users-contact-no']) && !empty($this->option_name['users-contact-no']) ){

			// Add filter to add field
			add_filter('user_contactmethods', array($this, 'add_contact_no_fields_in_user_profile'));

		}

		if(	isset($this->option_name['users-facebook-link']) && !empty($this->option_name['users-facebook-link']) ){

			// Add filter to add field
			add_filter('user_contactmethods', array($this, 'add_facebook_fields_in_user_profile'));

		}

		if(	isset($this->option_name['users-twitter-link']) && !empty($this->option_name['users-twitter-link']) ){

			// Add filter to add field
			add_filter('user_contactmethods', array($this, 'add_twitter_fields_in_user_profile'));

		}

		if(	isset($this->option_name['users-linkedin-link']) && !empty($this->option_name['users-linkedin-link']) ){

			// Add filter to add field
			add_filter('user_contactmethods', array($this, 'add_linkedin_fields_in_user_profile'));

		}

	}

	/*
	 *  Add additional fields in WP User's profile
	 */
	public function add_contact_no_fields_in_user_profile( $user_contact ) {
			// Add additional link
			$user_contact['contact_no'] = 'Contact No.';

			return $user_contact;
	}

	/*
	 *  Add additional fields in WP User's profile
	 */
	public function add_facebook_fields_in_user_profile( $user_contact ) {

			$user_contact['facebook'] = 'Facebook';
			// Add additional link
			return $user_contact;
	}

	/*
	 *  Add additional fields in WP User's profile
	 */
	public function add_twitter_fields_in_user_profile( $user_contact ) {
			// Add additional link
			$user_contact['twitter'] = 'Twitter';

			return $user_contact;
	}

	/*
	 *  Add additional fields in WP User's profile
	 */
	public function add_linkedin_fields_in_user_profile( $user_contact ) {
			// Add additional link
			$user_contact['linkedin'] = 'LinkedIn';

			return $user_contact;
	}


	/*
	 * Trigger a function to add a field in user's profile
	 */
	public function enable_reordering_of_admin_menu_items( ) {
		if(	isset($this->option_name['reorder-admin-menu-items']) && !empty($this->option_name['reorder-admin-menu-items']) ){

			add_filter( 'custom_menu_order', array($this, 'reorder_admin_menu' ));
			add_filter( 'menu_order', array($this, 'reorder_admin_menu' ));

		}

	}


	/*
	 * Re-arrange in wp admin menu
	 */
	function reorder_admin_menu( $__return_true ) {
	    return array(
	         'edit.php?post_type=page', // Pages
	         'edit.php', // Posts
	         'upload.php', // Media
	         'separator1', // --Space--
	         'index.php', // Dashboard
	         'themes.php', // Appearance
	         'edit-comments.php', // Comments
	         'separator2', // --Space--
	         'plugins.php', // Plugins
	         //'separator3', // --Space--
	         'tools.php', // Tools
	         'users.php', // Users
	         'options-general.php', // Settings
	   );
	}


	/*
	 * Disable Comments
	 */
	public function disable_comments( ) {
		if(	isset($this->option_name['disable-comments']) && !empty($this->option_name['disable-comments']) ){

			// add_filter( 'custom_menu_order', array($this, 'reorder_admin_menu' ));
			add_action('admin_init', array($this, 'disable_comments_post_types_support'), 10, 3);

			$post_types = $this->get_all_selected_posts();

			if(empty($post_types)) {
				$post_types = array();
			}

			if(is_array($post_types)) {
				if(in_array('all', $post_types)){
					add_filter('comments_open', array($this, 'disable_comments_status'), 20, 2);
					add_filter('pings_open', array($this, 'disable_comments_status'), 20, 2);
					add_filter('comments_array', array($this, 'disable_comments_hide_existing_comments'), 10, 2);
					add_action('admin_menu', array($this, 'disable_comments_admin_menu'));
					add_action('admin_init', array($this, 'disable_comments_admin_menu_redirect'));
					add_action('admin_init', array($this, 'disable_comments_dashboard'));
					add_action('init', array($this, 'disable_comments_admin_bar'));
					add_action( 'wp_before_admin_bar_render', array($this, 'admin_bar_render' ));
				}
			}

		}

	}



	public function get_all_selected_posts () {

		// $post_types = $this->option_name['disable-comments'];
		$post_types = array('all');		// Manually disable for all post types

		return $post_types;
	}


	// Disable support for comments and trackbacks in post types
	function disable_comments_post_types_support() {
		//$post_types = get_post_types();
		$post_types = $this->get_all_selected_posts();
		if(empty($post_types)) {
			return;
		}

		foreach ($post_types as $post_type) {
			if(post_type_supports($post_type, 'comments')) {
				remove_post_type_support($post_type, 'comments');
				remove_post_type_support($post_type, 'trackbacks');
			}
		}

	}
	// Close comments on the front-end
	function disable_comments_status() {
		return false;
	}
	// Hide existing comments
	function disable_comments_hide_existing_comments($comments) {
		$comments = array();
		return $comments;
	}
	// Remove comments page in menu
	function disable_comments_admin_menu() {
		remove_menu_page('edit-comments.php');
	}
	// Redirect any user trying to access comments page
	function disable_comments_admin_menu_redirect() {
		global $pagenow;
		if ($pagenow === 'edit-comments.php') {
			wp_redirect(admin_url()); exit;
		}
	}
	// Remove comments metabox from dashboard
	function disable_comments_dashboard() {
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
	}
	// Remove comments links from admin bar
	function disable_comments_admin_bar() {
			if (is_admin_bar_showing()) {
					remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
			}
	}
	function admin_bar_render() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('comments');
	}
	/* Disable Comments


	/* Disable Updates Notification */
	function disable_updates() {
		if(	isset($this->option_name['disable-updates']) && !empty($this->option_name['disable-updates']) && $this->option_name['disable-updates'] == TRUE){

			$disableUpdates = $this->option_name['disable-updates'];

			remove_action('load-update-core.php', 'wp_update_plugins');
			add_filter('pre_site_transient_update_plugins', '__return_null');
			remove_action( 'admin_notices', 'update_nag', 3 );

			add_filter('pre_site_transient_update_core', array($this, 'remove_core_updates'));
			add_filter('pre_site_transient_update_plugins', array($this, 'remove_core_updates'));
			add_filter('pre_site_transient_update_themes', array($this, 'remove_core_updates'));

		}
	}

	function remove_core_updates(){
		global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
	}


	/* Site favicon */
	function add_favicon_on_site() {
		if(	isset($this->option_name['favicon']) && !empty($this->option_name['favicon']) ){

			add_action('wp_head', array($this, 'blog_favicon'));
			add_action( 'admin_head', array($this, 'blog_favicon' ));
			add_action('login_head', array($this, 'blog_favicon'));

		}
	}

	/* Add Favicon */
	function blog_favicon() {

		$faviconImgId = $this->option_name['favicon'];

		if (!empty($faviconImgId)) {
			$faviconArr = wp_get_attachment_image_src($faviconImgId);
			$faviconUrl = array_shift($faviconArr);
			echo '<link rel="shortcut icon"  href="' . $faviconUrl .'" />';
		}
	}
	/* Add Favicon */


	// PHP Mailer
	function enable_php_mailer() {
		if(	isset($this->option_name['smtp-enabled']) && !empty($this->option_name['smtp-enabled']) && $this->option_name['smtp-enabled'] == TRUE){

				if(!empty($this->option_name['smtp-host'])) {
					$smtpHost 		= 	$this->option_name['smtp-host'];
				}

				if(!empty($this->option_name['smtp-port'])) {
					$smtpPort 		= 	$this->option_name['smtp-port'];
				}

				if(!empty($this->option_name['smtp-username'])) {
					$smtpUserName 	= 	$this->option_name['smtp-username'];
				}

				if(!empty($this->option_name['smtp-password'])) {
					$smtpPassword 	= 	$this->option_name['smtp-password'];
				}

				if(!empty($this->option_name['smtp-secure'])) {
					$smtpSecure 	= 	$this->option_name['smtp-secure'];
				}

				if(!empty($this->option_name['smtp-email-id'])) {
					$senderEmailId 	= 	$this->option_name['smtp-email-id'];
				}

				if(!empty($this->option_name['smtp-email-name'])) {
					$senderName 	= 	$this->option_name['smtp-email-name'];
				}

				add_action( 'phpmailer_init', array($this, 'use_phpmailer') );
		}
	}


	function use_phpmailer( $phpmailer ) {
	    $phpmailer->isSMTP();
	    $phpmailer->Host = $smtpHost;
	    $phpmailer->SMTPAuth = true; // Force it to use Username and Password to authenticate
	    $phpmailer->Port = $smtpPort;
	    $phpmailer->Username = $smtpUserName;
	    $phpmailer->Password = $smtpPassword;

	    // Additional settings…
	    //$phpmailer->SMTPSecure = "tls"; // Choose SSL or TLS, if necessary for your server
	    $phpmailer->SMTPSecure = $smtpSecure; // Choose SSL or TLS, if necessary for your server
	    $phpmailer->From = $senderEmailId;
	    $phpmailer->FromName = $senderName;
	}


}