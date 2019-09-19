<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://anurag-singh.github.io/
 * @since      1.0.0
 *
 * @package    Wordpress_Admin_Customizer
 * @subpackage Wordpress_Admin_Customizer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wordpress_Admin_Customizer
 * @subpackage Wordpress_Admin_Customizer/public
 * @author     Anurag Singh <developer.anuragsingh@outlook.com>
 */
class As_Wp_Admin_Customization_Public {

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
	private $slider_enabled;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $as_wp_admin_customization       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $as_wp_admin_customization, $version ) {

		$this->as_wp_admin_customization = $as_wp_admin_customization;
		$this->version = $version;

		$admin_customization = $this->slider_enabled = get_option( 'admin-customization');
		$isSliderEnable = $admin_customization['slider-enabled'];

		if($isSliderEnable == TRUE) {
			// echo '<pre>................';
			// print_r($isSliderEnable);
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_for_bx_slider' ), 10 );
			add_action('wp_footer', array( $this, 'define_bx_slider_properties'));
		}

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in As_Wp_Admin_Customization_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The As_Wp_Admin_Customization_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->as_wp_admin_customization, plugin_dir_url( __FILE__ ) . 'css/as-wp-admin-customization-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in As_Wp_Admin_Customization_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The As_Wp_Admin_Customization_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->as_wp_admin_customization, plugin_dir_url( __FILE__ ) . 'js/as-wp-admin-customization-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the BX Slider's Stylesheets & JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_bx_slider_style_scripts() {

		wp_enqueue_style( $this->as_wp_admin_customization, plugin_dir_url( __FILE__ ) . 'css/as-wp-admin-customization-public.css', array(), $this->version, 'all' );

		wp_enqueue_script( $this->as_wp_admin_customization, plugin_dir_url( __FILE__ ) . 'js/as-wp-admin-customization-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Display content through short code
	 *
	 * @since    1.0.0
	 */
	public function render_shortcode() {
		global $post;
		print_r($post);
	}

	// Display HTML SiteMap with shortcode "html_sitemap"
	function as_display_html_sitemap( $pageArgArr ) {
		if ( class_exists( 'WooCommerce' ) ) { // If woocommerce is activated, remove these pages
			$pageToBeExclude = array('Cart', 'Checkout', 'Shipping Addresses', 'My Account', 'Payment', 'Thank you', 'Sitemap');

			foreach ($pageToBeExclude as $page) {
				$pageObj = get_page_by_title( $page );

				$pageIdsTobeExclude[] = $pageObj->ID;
			}

			// Convert array into string
			$pageIdsTobeExclude = implode(',', $pageIdsTobeExclude);
		} else {
			$pageIdsTobeExclude = '';
		}

	    $pageArgArr = shortcode_atts(array(
            'child_of' => '0',
            'authors' => '',
            'date_format' => '',
            'depth' => '',
            'echo' => 'true',
            'exclude' => $pageIdsTobeExclude,
            'include' => '',
            'link_after' => '',
            'link_after' => '',
            'post_type' => 'page',
            'post_status' => 'publish',
            'show_date' => '',
            'sort_column' => '',
            'title_li' => '<h2>' . __( 'Pages', 'textdomain' ) . '</h2>',
            'item_spacing' => '',
            'walker' => ''
	            ), $pageArgArr, 'html_sitemap');

	    // Display site's pages
	    echo "<div class='html-sitemap-links pages'><ul class='html-sitemap-links'>";
	    wp_list_pages( $pageArgArr );
	    echo "</div></ul>";

	    // Display site's blog categories
	    echo "<div class='html-sitemap-links categories'><ul>";
	    wp_list_categories( array(
	        'orderby' => 'name',
	        'exclude' => 1,
	        //'include' => array( 3, 5, 9, 16 )
	        'title_li' => '<h2>' . __( 'Blog\'s Categories', 'textdomain' ) . '</h2>'
	    ) );
	    echo "</div></ul>";

	    // if Woocommerce Activated
	    if ( class_exists( 'WooCommerce' ) ) {
	    	// Display Woocommerce categories,
		  	echo "<div class='html-sitemap-links product-categories'><ul>";
			    wp_list_categories( array(
			    	'taxonomy' => 'product_cat',
			        'orderby' => 'name',
			        'exclude' => 1,
			        //'include' => array( 3, 5, 9, 16 )
			        'title_li' => '<h2>' . __( 'Product\'s Categories', 'textdomain' ) . '</h2>'
			    ) );
		   	echo "</div></ul>";

		   	// Display Woocommerce categories,
		  	echo "<div class='html-sitemap-links products'><ul>";
		  	echo '<li><h2>Products</h2>';
			    $args = array(
			        'post_type'      => 'product',
			        'posts_per_page' => -1,
			    );

			    $products = new WP_Query( $args );
			    echo '<ul>';
			    while ( $products->have_posts() ) : $products->the_post();
			        global $product;
			        echo '<li><a href="'.get_permalink().'">'.get_the_title().'</a></li>';
			    endwhile;

			    wp_reset_query();
		   	echo "</ul></li>";
		   	echo "</div></ul>";
		}

	}

	// Display HTML SiteMap with shortcode "html_sitemap"

}