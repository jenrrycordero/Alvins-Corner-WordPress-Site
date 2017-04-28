<?php
/**
 * Created by PhpStorm.
 * User: alian
 * Date: 04-27-2015
 * Time: 16:28
 */

/*
Plugin Name: Razz: Apartment Models Basic
Plugin URI: http://razzinteractive.com
Description: Plugin to interact with models for the apartments
Version: 2.1.2
Author: Razz interactive
Author URI: http://razzinteractive.com
License: GPLv2 or later
*/



// 1. customize ACF path
add_filter('advanced-custom-fields-pro/settings/path', 'my_acf_settings_path');
function my_acf_settings_path( $path ) {
    $path = get_stylesheet_directory() . '/advanced-custom-fields-pro/';
    return $path;
}

// 2. customize ACF dir
add_filter('advanced-custom-fields-pro/settings/dir', 'my_acf_settings_dir');
function my_acf_settings_dir( $dir ) {
    $dir = get_stylesheet_directory_uri() . '/advanced-custom-fields-pro/';
    return $dir;
}

// 3. Hide ACF field group menu item. This has to be acf
//add_filter('acf/settings/show_admin', '__return_false');

include_once 'advanced-custom-fields-pro/acf.php';


include_once 'settings.php';

include_once 'post_type.php';

include_once 'shortcode.php';

include_once 'ajax_request.php';

include_once 'razz_model_pdf.php';

include_once 'plugin_options.php';



add_action( 'admin_enqueue_scripts', 'razz_apt_model_enqueue_script' );
if ( !function_exists('razz_apt_model_enqueue_script'))
{
    function razz_apt_model_enqueue_script($hook) {
        global $typenow;
        if( $typenow == 'razz-apt-model' ) {


            wp_enqueue_style("razz-apt-models-style", plugin_dir_url( __FILE__ ) . 'css/style.css' );


            wp_enqueue_media();
            /**
             * Loads the image management javascript
             */
            // Registers and enqueues the required javascript.
            wp_register_script( 'razz-apt-models-script-admin', plugin_dir_url( __FILE__ ) . 'js/razz_apt_models_script_admin.js', array( 'jquery' ) );

            wp_localize_script( 'razz-apt-models-script-admin', 'meta_image',
                array(
                    'title' => __( 'Choose or Upload an Image', 'razz-apartment-models' ),
                    'button' => __( 'Use this image', 'razz-apartment-models' ),
                )
            );
            wp_enqueue_script( 'razz-apt-models-script-admin' );


            wp_enqueue_script( 'jquery-ui-autocomplete' );
            wp_register_style( 'jquery-ui-styles','http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
            wp_enqueue_style( 'jquery-ui-styles' );

            wp_localize_script( 'razz-apt-models-script-admin', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

        }


        if ( $hook = "toplevel_page_razz_apartment_models")
        {
            wp_register_script( 'razz-apt-models-script-admin', plugin_dir_url( __FILE__ ) . 'js/razz_apt_models_script_admin.js', array( 'jquery' ) );
            wp_enqueue_script( 'razz-apt-models-script-admin' );
            wp_localize_script( 'razz-apt-models-script-admin', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

            wp_enqueue_style("razz-apt-models-admin-style", plugin_dir_url( __FILE__ ) . 'css/style-admin.css' );
        }

        if( $hook == 'toplevel_page_razz_apartment_models' )
        {
            wp_enqueue_style("razz-apt-models-admin-style", plugin_dir_url( __FILE__ ) . 'css/style-admin.css' );
        }
    }
}


add_action( 'wp_enqueue_scripts', 'razz_apt_model_frontend_scripts' );
function razz_apt_model_frontend_scripts() {
	
	global $post;
    if( ( is_a( $post, 'WP_Post' ) || is_page() ) ) {
        
		wp_enqueue_style( 'razz-apt-models-style', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), '1.2.0' );
		
		wp_enqueue_style( 'font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css' );


		wp_enqueue_script( 'isotope', "http://isotope.metafizzy.co/isotope.pkgd.min.js", array( 'jquery' ), '2.1.1', true );

		wp_enqueue_script( 'razz-apt-models-valign', plugin_dir_url( __FILE__ )  . '/js/razz_valign_snipset.js', array('jquery'), '1.1.0', true );

		wp_enqueue_script( 'razz-apt-models-script', plugin_dir_url( __FILE__ )  . '/js/razz_apt_models_script.js', array('jquery'), '1.12.0', true );

		wp_localize_script( 'razz-apt-models-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    }
	
}



if ( !function_exists("razz_get_meta_values") )
{
    function razz_get_meta_values($key, $post_type = "post", $status = "publish", $diferent_only = true)
    {
        global $wpdb;

        $extra = "";
        if ( $diferent_only ) $extra = " DISTINCT ";

        $r = $wpdb->get_col( $wpdb->prepare( "SELECT $extra pm.meta_value FROM {$wpdb->postmeta} pm
                                                LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                                                WHERE pm.meta_key = '%s'
                                                AND p.post_type = '%s'
                                                AND p.post_status = '%s'
                                            ", $key, $post_type, $status ) );
        return $r;
    }
}



if ( !function_exists('razz_get_apartment_model_filters') )
{
    function razz_get_apartment_model_filters()
    {
        $filters = array("Studio" => "Studio", "1 Bedroom" => "1 Bedroom", "2 Bedroom" => "2 Bedroom",
                         "3 Bedroom" => "3 Bedroom", "4 Bedroom" => "4 Bedroom", "5 Bedroom" => "5 Bedroom");

        $current_filters = razz_get_meta_values("razz_apt_model_info_labels", 'razz-apt-model');

        foreach( $current_filters as $i => $tag)
        {

            $t = razz_get_info_from_tags($tag, 0);

            //the merge will keep the different keys and override the same key.
            $filters = array_merge($filters, $t);
        }

        foreach ( $filters as $visible_filter => $v)
        {
            $filters[$visible_filter] = str_replace(" ", "-", $visible_filter);
        }

        return $filters;
    }
}

if ( !function_exists('razz_get_info_from_tags') )
{
    function razz_get_info_from_tags($tag, $return_filtered_array = true)
    {
        $tag = trim( preg_replace('/\\s*,\\s*/', ",", $tag, -1 ) );

        $filters = array_flip( explode(",", $tag) );

        if ( !$return_filtered_array ) return $filters;

        foreach ( $filters as $visible_filter => $v)
        {
            $filters[$visible_filter] = str_replace(" ", "-", $visible_filter);
        }

        return $filters;
    }
}



if ( !function_exists('razz_apt_models_get_filters') )
{
    function razz_apt_models_get_filters()
    {
        $term_array = array();

        $terms = get_terms('razz-apt-model-tag');
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) )
        {
            foreach ( $terms as $term ) {
                $term_array[ str_replace(" ", "-", $term->name) ] = $term->name;
            }
        }

        return $term_array;
    }
}