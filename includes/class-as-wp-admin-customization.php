<?php
# ref - https://www.ibenic.com/creating-wordpress-menu-pages-oop/

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://anuragsingh.me
 * @since      1.0.0
 *
 * @package    Wordpress_Admin_Customizer
 * @subpackage Wordpress_Admin_Customizer/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wordpress_Admin_Customizer
 * @subpackage Wordpress_Admin_Customizer/includes
 * @author     Anurag Singh <developer.anuragsingh@outlook.com>
 */
class Wordpress_Admin_Customizer {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      As_Wp_Admin_Customization_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $as_wp_admin_customization    The string used to uniquely identify this plugin.
	 */
	protected $as_wp_admin_customization;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->as_wp_admin_customization = 'as-wp-admin-customization';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_post_type_hooks();
		$this->define_taxonomy_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - As_Wp_Admin_Customization_Loader. Orchestrates the hooks of the plugin.
	 * - As_Wp_Admin_Customization_i18n. Defines internationalization functionality.
	 * - Wordpress_Admin_Customizer_Admin. Defines all hooks for the admin area.
	 * - As_Wp_Admin_Customization_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-wp-admin-customization-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-as-wp-admin-customization-i18n.php';

		/**
		 * The class responsible for creating custom 'post type' of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/inc/class-as-wp-admin-customization-post-type.php';

		/**
		 * The class responsible for creating custom 'taxonomy' of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/inc/class-as-wp-admin-customization-taxonomy.php';


		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-as-wp-admin-customization-admin.php';


		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/inc/class-as-wp-admin-customization-menu.php';

		/**
		 * The class responsible for defining all actions that occur in the admin login screen.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/inc/class-as-wp-admin-customization-admin-login-screen.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-as-wp-admin-customization-public.php';



		$this->loader = new As_Wp_Admin_Customization_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the As_Wp_Admin_Customization_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new As_Wp_Admin_Customization_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wordpress_Admin_Customizer_Admin( $this->get_as_wp_admin_customization(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );


		$plugin_admin_login_screen = new As_Wp_Admin_Customization_Admin_Login_Screen( $this->get_as_wp_admin_customization(), $this->get_version() );
		// $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin_login_screen, 'enqueue_login_screen_styles' );

		// Add stylesheet for login screen
		$this->loader->add_action( 'login_enqueue_scripts', $plugin_admin_login_screen, 'enqueue_login_screen_styles');

		// Change Background of WP login screen
		$this->loader->add_action('login_head', $plugin_admin_login_screen, 'login_screen_bg');

		// Change WP login screen logo
		$this->loader->add_action( 'login_enqueue_scripts', $plugin_admin_login_screen, 'login_screen_logo' );

		// Change WP admin screen logo URL
		$this->loader->add_filter( 'login_headerurl', $plugin_admin_login_screen, 'login_screen_logo_url' );

		// Change WP admin screen logo Title
		$this->loader->add_filter( 'login_headertitle', $plugin_admin_login_screen, 'login_screen_logo_title' );

		// Keep 'remember me' option always checked
		$this->loader->add_filter( 'init', $plugin_admin_login_screen, 'always_check_remember_me' );

		// Redirect general user to public view
		$this->loader->add_filter('login_redirect', $plugin_admin_login_screen, 'loginRedirect', 10, 3);

		// Add extra content in header of wp login screen
		$this->loader->add_action('login_header', $plugin_admin_login_screen, 'login_form_header');

		// Add extra content in footer of wp login screen
		$this->loader->add_action('login_footer', $plugin_admin_login_screen, 'login_form_footer');


		// Remove WP logo from wp user's area
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin_login_screen, 'remove_wp_logo_from_admin_screen', 999 );

		// Change "Howdy" Text from admin area
//		$this->loader->add_filter( 'gettext', $plugin_admin_login_screen, 'change_howdy_text_from_admin_area', 10, 3 );

		// Remove widgets from wp user's dashboard
		$this->loader->add_action( 'wp_dashboard_setup', $plugin_admin_login_screen, 'remove_dashboard_widgets');

		// enable and add additional fields in WP User's profile
		$this->loader->add_filter('init', $plugin_admin_login_screen, 'enable_additional_fields_in_user_profile');

		// Enable reordering of left admin menu
		$this->loader->add_filter( 'init', $plugin_admin_login_screen, 'enable_reordering_of_admin_menu_items' );

		// Disable comments
		$this->loader->add_filter( 'init', $plugin_admin_login_screen, 'disable_comments' );

		// Disable WP Updates Notifications
		$this->loader->add_filter( 'init', $plugin_admin_login_screen, 'disable_updates' );

		// Add favicon
		$this->loader->add_filter( 'init', $plugin_admin_login_screen, 'add_favicon_on_site' );

		// Enable PHP SMTP Mailer
		$this->loader->add_filter( 'init', $plugin_admin_login_screen, 'enable_php_mailer' );

		// Enable PHP SMTP Mailer
		$this->loader->add_filter( 'init', $plugin_admin_login_screen, 'enable_php_mailer' );





	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new As_Wp_Admin_Customization_Public( $this->get_as_wp_admin_customization(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_shortcode( 'shortcode', $plugin_public, 'render_shortcode' );
		$this->loader->add_shortcode( 'html_sitemap', $plugin_public, 'as_display_html_sitemap' );

	}

	/**
	 * Create custom 'post type' for admin & public facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_post_type_hooks() {
		$post_type_arr = array('anu rag');	// Define 'Custom Post Types'

	    foreach ($post_type_arr as $post_type) {

			$labels = $this->get_sanitized_labels($post_type);		// Get sanitized text through a function

			$name = $labels['name'];
			$single = $labels['single'];
			$plural = $labels['plural'];

			# Create post type for plugin
			$plugin_post_type[] = new As_Wp_Admin_Customization_Post_Type(
											sanitize_title( $name ),
											$plural,
											$single,
											$description = '',
											$options = array()
										);

	    }

	}

	/**
	 * Create custom 'taxonomy' for admin & public facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_taxonomy_hooks() {
		$taxonomy_post_type_asso_array = array(
							'sin gh' => 'anu rag',
						);	// Define 'Custom Post Types'

	    foreach ($taxonomy_post_type_asso_array as $taxonomy => $post_type) {
	    	$post_type_labels = $this->get_sanitized_labels($post_type);	// Get sanitized text through a function
			$taxo_labels = $this->get_sanitized_labels($taxonomy);			// Get sanitized text through a function

			$post_type_slug = $post_type_labels['slug'];
			$taxo_slug = $taxo_labels['slug'];
			$taxo_name = $taxo_labels['name'];
			$taxo_single = $taxo_labels['single'];
			$taxo_plural = $taxo_labels['plural'];

			# Create taxonomy for plugin
			$plugin_taxonomy[] = new As_Wp_Admin_Customization_Taxonomy(
										$taxo_slug,
										$taxo_plural,
										$taxo_single,
										$post_types = array($post_type_slug),
										$tax_args = array()
									);
	    }

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_as_wp_admin_customization() {
		return $this->as_wp_admin_customization;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    As_Wp_Admin_Customization_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Convert text into sanitize text.
	 * Replace all black space (' ') with '-'
	 * lowercase all characters
	 * @since     1.0.0
	 * @return    string
	 */
	private function get_sanitized_labels($text) {
		$name = ucwords(strtolower(preg_replace('/\s+/', ' ', $text) ));
		$slug = strtolower(sanitize_title( $text ) );

		$single = ucfirst($name);

		$last_character = substr($single, -1);

		if ($last_character === 'y') {
			$plural = substr_replace($single, 'ies', -1);
		}
		else {
			$plural = $single.'s'; // add 's' to convert singular name to plural
		}

		$response = array(
						'name'		=>	$name,
						'single' 	=> 	$single,
						'plural' 	=> 	$plural,
						'slug'		=> 	$slug
					);

		return $response;
	}

}
