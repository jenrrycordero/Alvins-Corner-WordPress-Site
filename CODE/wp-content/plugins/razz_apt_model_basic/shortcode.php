<?php


function foobar_func( $atts ){
    return "foo and bar";
}
add_shortcode( 'foobar', 'foobar_func' );


add_shortcode( 'razz_show_models', 'razz_show_models_shortcode' );


function razz_show_models_shortcode($atts, $content = null)
{
    extract( shortcode_atts(
                            array(
                                'all' => 1,
                                'filters' => 1,
                                'orderby' => 'name',
                                'order' => 'asc',
                                'limit' => -1
                            ), $atts
            )
    );

    $html = "";

    //first we evaluate if filters is active,  to get all the the options for the labels.

    $list_of_tags = array();
    if ( $filters )
    {
        $list_of_tags = razz_apt_models_get_filters();

        $html .= '<div id="razz-model-filter-wrapper">
        <div class="dropdown-toggle">
        <button class="mobile-filter-list">
                        Filters
                        <i class="fa fa-angle-down"></i>
                    </button>
                        <div class="inner filter-list">

                            <a href="#" data-filter="*" class="active-filter">All</a>';
        foreach ( $list_of_tags as $tag_class => $tag )
            $html .= "<a href='#' data-filter='." . $tag_class . "'>$tag</a>";

        $html .= "</div></div></div>";
    }



    //Second we retrieve the models based on the options of the sortcode.
    /*   
    $query_args = array(
        'post_type' => 'razz-apt-model',
        'posts_per_page' => $limit,
        'orderby' => $orderby,
        'order' => $order
    );
    */

    $query_args = array(
        'post_type' => 'razz-apt-model',
        'posts_per_page' => $limit,
        'meta_key' => 'bed',
        'orderby' => 'meta_value_num',
        'order' => $order
    );
    
    $query = new WP_Query( $query_args );

    if ( $query->have_posts() )
    {

        $html .= "<div id='razz-model-wrapper-container' class='razz-apartment-model-basic'>
                    <div id='razz-model-wrapper'>";

        $url_base_path =  plugins_url( 'razz_apt_models/ajax_request.php', dirname(__FILE__) );

        while ( $query->have_posts() )
        {
            $query->the_post();

            //meta info about the model. ACF.
            $beds = get_field('bed');
            $baths = get_field('bath');
            $sqft = get_field('sqft');

            $model_price_override = get_field('specific_price_override');
            $price = get_field('price');

            $labels = get_field('filters');
            $model_image = get_field('image');

            $beds_baths = $beds ? "$beds Bed | " : "";
            $beds_baths .= $baths ? "$baths Bath" : "";

            $model_area = "";
            $model_price = "";


            //settings from the options page.ACF
            $model_title_class = "";
            $display_sqft = get_field('show_sq_ft', "option");
            //$display_price = get_field('show_price', "option");  //this is not the correct name of the field
            $display_price = get_field('display_price', "option");
            //$display_price_label = get_field('price_override_label_model_list', "option");  //this is not the correct name of the field
            $display_price_label = get_field('price_override_label', "option");

            $sqft_class = '';
            $info_class = '';

            if ( $display_sqft && $sqft)
                $model_area = $sqft . '<span class="nowrap"> SQ FT</span>';

            if ( $display_price )
            {

                if ( $display_price_label != "" )
                {
                    $model_price = $display_price_label;
                    $sqft_class = 'short-space';
                    $info_class = 'no-price';
                }
                else if ( $model_price_override != '' )
                {
                    $model_price = $model_price_override;
                }
                else
                {
                    $model_price = ($price != "") ? "$ ".$price : "";
                }
            }

            $model_class = '';

            $terms = wp_get_post_terms( $query->post->ID, 'razz-apt-model-tag', array("fields" => "names") );
            foreach ( $terms as $filter )
            {
                $model_class .= str_replace(" ", "-", $filter) . " ";
            }
//            var_dump($model_class);

            $sanitized_model_title = sanitize_title(get_the_title()) . "_" . get_the_ID();

            $html .= "
            <div class='item $model_class'>
                <div id='$sanitized_model_title' class='item-contents goto-model'
                    data-model='$sanitized_model_title' data-id='" . $query->post->ID . "'>
                    <div class='details-header'>
                        <h3 class='$model_title_class'>" . get_the_title() . "</h3>
                        <span>$beds_baths</span>
                    </div>
                    <div class='img-razz-model-wrapper'>
                        <img src='{$model_image['sizes']['medium']}' data-full-img='{$model_image['url']}'
                            class='img-responsive valign' alt='{$model_image['alt']}' />
                    </div>";

            if ( $model_price || $model_area )
            {
                $html .= "<div class='details-footer'>";
                $html .= $model_area ? "<div class='sqft-info ".$sqft_class."'>$model_area</div>" : "";
                $html .= $model_price ? "<div class='price-info ".$info_class."'>$model_price</div>" : "";
                $html .= "</div>";
            }

            $html .= "
                </div>
            </div>";
        }

        $html .= "
        <div id='response-overlay' class='close-razz-model-wrapper'>
            <div id='response-wrapper-content' class='response-razz-model-wrapper'>
                <div class='close'></div>
                <div id='response-content'></div>
            </div>
        </div>";
		
		
		$custom_css = get_field('custom_css', 'option');
		$html .= "<style>$custom_css</style></div></div>";
    }

    wp_reset_postdata();

    return $html;
}
