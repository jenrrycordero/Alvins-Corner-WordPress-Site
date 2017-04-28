<?php
// =============================================================================
// X Alta West Commerce
// -----------------------------------------------------------------------------
// Custom Functions
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   00. Theme Includes
//   01. Set Child Theme Stack Styles
//   02. Enqueue Child Scripts
//   03. Get Child View
//   04. Social Widget Shortcode
//   05. Artist Gallery - Carousel (Shortcode)
//   06. Artist Lightbox
// =============================================================================



// Theme Includes
// =============================================================================
	// include_once('includes/cpt-artist.php');		// Artist Custom post type
	// include_once('includes/meta-artist.php');		// Artist metaboxes
	// include_once('includes/aq_resizer.php');		// Aqua Resizer
	// include_once('includes/custom-gallery.php');	// Custom Wordpress Gallery (Carousel)


// Set Child Theme Stack Styles
// =============================================================================
if ( ! function_exists( 'x_enqueue_stack_styles' ) ) :
  	function x_enqueue_stack_styles() {
		$stack  = x_get_stack();
	    $design = x_get_option( 'x_integrity_design', 'light' );

	    if ( $stack == 'integrity' && $design == 'light' ) {
	      	$ext = '-light';
	    } elseif ( $stack == 'integrity' && $design == 'dark' ) {
	      	$ext = '-dark';
	    } else {
	      	$ext = '';
	    }

		if ( is_child_theme() ) {
     		wp_enqueue_style( 'x-stack-parent', get_template_directory_uri() . '/framework/css/site/stacks/' . $stack . $ext . '.css', NULL, NULL, 'all' );
     		wp_enqueue_style( 'x-magnificpopup', get_stylesheet_directory_uri() . '/css/magnific-popup.css', NULL, NULL, 'all' );
     		wp_enqueue_style( 'x-fontawesome', get_stylesheet_directory_uri() . '/css/font-awesome.min.css', NULL, NULL, 'all' );
    	}

     	// wp_enqueue_style( 'altawestcommerce-opensans', 'http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,700,600,400' . $stack . $ext . '.css', NULL, NULL, 'all' );
     	// wp_enqueue_style( 'altawestcommerce-styles', get_stylesheet_directory_uri() . '/altawestcommerce.css', NULL, NULL, 'all' );


  	}
	add_action( 'wp_enqueue_scripts', 'x_enqueue_stack_styles');
endif;




//Enqueue child style as the last css file.
if ( !function_exists('x_enqueue_last_child_theme') )
{
	function x_enqueue_last_child_theme()
	{
		wp_enqueue_style( 'owl-carousel-0', get_stylesheet_directory_uri() . '/js/owl-carousel/owl.carousel.css', NULL, NULL, 'all' );
		wp_enqueue_style( 'owl-carousel-1', get_stylesheet_directory_uri() . '/js/owl-carousel/owl.theme.css', NULL, NULL, 'all' );
		wp_enqueue_style( 'owl-carousel-2', get_stylesheet_directory_uri() . '/js/owl-carousel/owl.transitions.css', NULL, NULL, 'all' );
		wp_enqueue_style( 'x-theme-style', get_stylesheet_directory_uri() . '/theme-style.css', NULL, NULL, 'all' );


		wp_register_script('owl-carousel', get_stylesheet_directory_uri() . "/js/owl-carousel/owl.carousel.min.js", array('x-site-body'), null, true);
		wp_register_script('x-main', get_stylesheet_directory_uri() . "/js/main.js", array('x-site-body'), null, true);

		wp_enqueue_script('owl-carousel');
		wp_enqueue_script('x-main');

	}
	add_action( 'wp_enqueue_scripts', 'x_enqueue_last_child_theme', 99);
}



// Enqueue Child Scripts
// =============================================================================
if ( ! function_exists( 'x_enqueue_child_scripts' ) ) :
  	function x_enqueue_child_scripts() {

  		// register scripts here
  		wp_register_script('x-magnificpopup-js', get_stylesheet_directory_uri() . "/js/magnific-popup.js", array('x-site-body'), null, true);
		wp_register_script('x-ddslick', get_stylesheet_directory_uri() . "/js/jquery.ddslick.min.js", array('x-site-body'), null, true);
  		// wp_register_script('x-altawestcommerce-plugins', get_stylesheet_directory_uri() . "/js/plugins.js", array('x-site-body'), null, true);



		// enqueue scripts here
		wp_enqueue_script('x-magnificpopup-js');
		wp_enqueue_script('x-ddslick');
		// wp_enqueue_script('x-altawestcommerce-plugins');
  	}
  	add_action( 'wp_enqueue_scripts', 'x_enqueue_child_scripts' );
endif;


// Get Child View
// =============================================================================

if ( ! function_exists( 'x_get_child_view' ) ) :
  function x_get_child_view( $base, $extension = '' ) {

    $file = 'altawestcommerce_' . $base . ( ( empty( $extension ) ) ? '' : '-' . $extension );

    do_action( 'x_before_view_' . $file );

    get_template_part( 'views/' . $base, $extension );

    do_action( 'x_after_view_' . $file );

  }
endif;

include_once ("includes/theme-options.php");

wp_theme_options();