<?php
/**
 * Created by PhpStorm.
 * User: alian
 * Date: 04-29-2015
 * Time: 12:59
 */



//add_action('wp_ajax_load_filters', 'razz_load_current_filters' );
//add_action( 'wp_ajax_nopriv_load_filters', 'razz_load_current_filters' );
//function razz_load_current_filters()
//{
//    echo json_encode( array_flip(razz_get_apartment_model_filters() ) );
//    wp_die();
//}
//
//
//add_action('wp_ajax_delete_filters', 'razz_delete_filters' );
//add_action( 'wp_ajax_nopriv_delete_filters', 'razz_delete_filters' );
//function razz_delete_filters()
//{
//    global $wpdb;
//    $meta_value_list = array();
//
//    $filters = json_decode($_REQUEST['filters'], 1);
//
//    foreach( $filters as $filter => $valid)
//    {
//        if ( $valid == "true" )
//        {
//            $meta_value_list[] = sanitize_text_field( $filter );
//        }
//    }
//
//    if ( !empty($meta_value_list) )
//    {
//        $query = "DELETE FROM {$wpdb->prefix}postmeta WHERE
//                    meta_key = ";
//    }
//    else
//    {
//        echo 0;
//    }
//
//    wp_die();
//}



add_action( 'wp_ajax_nopriv_get_model_view', 'razz_get_model_view' );
add_action( 'wp_ajax_get_model_view', 'razz_get_model_view' );

function razz_get_model_view()
{
    $url_base_path =  plugins_url( 'razz_apt_models/', dirname(__FILE__) );
    $model_id = $_GET['model'];

    $query_args = array(
        'post_type' => 'razz-apt-model',
        'p' => $model_id
    );
    $query = new WP_Query( $query_args );

    if ( !$query->have_posts() )
    {
        ob_clean();
        _e("There is no model with that id.", 'razz-apt-model');
        wp_die();
    }
    else
    {
        $query->the_post();

        $html = "";

        //Model info. ACF
        $beds = get_field('bed');
        $baths = get_field('bath');
        $sqft = get_field('sqft');

        $model_image = get_field('image');

        $model_price_override = get_field('specific_price_override');
        $price = get_field('price');
        $model_price = "";
        $price_dolar = false;
        $description = get_field('description');


        //Options info. ACF
        $option_description = get_field('description', 'option');

        $option_display_price = get_field('display_price', "option");
        $option_price_label = get_field('price_override_label', "option");

        $option_display_apply = get_field('enable_apply_now_link', "option");
        $option_apply_label = get_field('apply_now_label', "option");
        $option_apply_url = get_field('apply_now_url', "option");

        $option_display_pdf = get_field('pdf_button', "option");
        $option_pdf_label = get_field('pdf_label', "option");

        $option_email_apply = get_field('email_a_friend_link', "option");
        $option_email_label = get_field('email_a_friend_label', "option");
        $option_email_subject = get_field('email_subject', "option");
        $option_email_body = get_field('email_message', "option");

        $option_contact_us = get_field('show_contact_us_link', "option");
        $option_contact_us_label = get_field('contact_us_label', "option");


        if ( $option_display_price )
        {
            if ( $option_price_label != "" )
            {
                $model_price = $option_price_label;
            }
            else if ( $model_price_override != '' )
            {
                $model_price = $model_price_override;
            }
            else
            {
                $model_price = ($price != "") ? $price :  "";
                $price_dolar = true;
            }
        }
        if ( $option_display_apply && $option_apply_url)
        {
            $apply_now_link = $option_apply_url ;
            $apply_now_label = $option_apply_label ;
        }
        if ( $option_display_pdf )
        {
            $print_pdf_link = $url_base_path . "razz_model_pdf.php/$model_id";
            $print_pdf_label = $option_pdf_label;
        }

        if ( $option_email_apply )
        {
            $email_friend_subject = $option_email_subject;
            $email_friend_text = $option_email_body . "\n Visit it here: " . $_GET['current-url'];
            $email_friend_label = $option_email_label;
        }

        if ( $option_contact_us )
        {
            $contact_link = site_url( '/contact' );
            $contact_label = $option_contact_us_label;
        }

        $extra_description = $option_description;

        $title = get_the_title();

        $html ="
    <div id='left-column'>
        <h3 class='text-uppercase'>MODEL $title</h3>
        <div id='model-information-wrapper'><h4>CONTACT US FOR MORE DETAILS</h4>";

        if ( $model_price )
        {
            if ( $price_dolar  )
                $html .= "<div id='model-price'><i class='fa fa-usd'></i>Price: <span>$model_price</span></div>";
            else
                $html .= "<div id='model-price'>$model_price</div>";
        }




        $html .= "</div>
        <div id='model-links-wrapper'>";

        //this was moved from the above by Jenrry (client required)
        if ( $apply_now_link )
            $html .= "<div class='link-razz-model-wrapper'><a class='button' target='_blank' href='$apply_now_link'>$apply_now_label</a></div>";

        if ( $contact_label )
            $html .= "<div class='link-razz-model-wrapper'><a class='button' href='$contact_link'>$contact_label</a></div>";

        if ( $print_pdf_label )
            $html .= "<div class='link-razz-model-wrapper'><a class='button generate-pdf' target='_blank' href='#' data-model-id='$model_id'>$print_pdf_label</a></div>";

        if ( $email_friend_label )
            $html .= "<div class='link-razz-model-wrapper'>
                    <a class='button' href='mailto:?subject=$email_friend_subject&amp;body=$email_friend_text'>$email_friend_label</a>
                  </div>";

        if ( $description )
        {
            $html .= "<section id='model-desc'>" . $description . "</section>";
        }

        if ( $extra_description )
        {
            $html .= "<div class='model-description'>" . wpautop($extra_description, false) . "</div>";
        }


        $html .= "
        </div>
    </div>
    </div>
    <div id='content-column'>
         <div class='col-md-6'><span>";

        if ( $beds)
            $html .= "$beds BD";

        if ( $baths)
            $html .= " / $baths BA";

       $html .= "</span></div>
         <div class='col-md-6'><span>";

       if($sqft)
           $html .=   "$sqft sqft";

         $html .= "</span></div>
         <div class='col-md-6'>CALL US FOR PRICING</div>
        <img src='{$model_image['url']}' class='img-responsive valign' alt='{$model_image['alt']}' />
            <div id='controls-razz-model-wrapper'>
        <div id='pre-nav' class='nav pre'><a href='#'><i class='fa fa-4x fa-angle-left'></i></a></div>
        <div id='next-nav' class='nav next'><a href='#'><i class='fa fa-4x fa-angle-right'></i></a></div>
    </div>
    </div>

    <div class='clearfix'></div>
    ";

        ob_clean();
        echo $html;
        wp_die();
    }
}
