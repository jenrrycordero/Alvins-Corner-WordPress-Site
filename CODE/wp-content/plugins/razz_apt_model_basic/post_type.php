<?php




add_action( 'init', 'create_post_type' );
if ( !function_exists("create_post_type") )
{
    function create_post_type()
    {
        register_post_type( 'razz-apt-model',
            array(
                'labels' => array(
                    'name' => __( 'Apt. Model', 'razz-apartment-models' ),
                    'singular_name' => __( 'Apt. Model', 'razz-apartment-models' ),
                    'add_new_item' => __( 'Add new Model', 'razz-apartment-models' ),
                    'edit_item' => __( 'Edit Model', 'razz-apartment-models' ),
                    'new_item' => __( 'New Model', 'razz-apartment-models' ),
                    'view_item' => __( 'View Model', 'razz-apartment-models' ),
                    'search_items' => __( 'Search Models', 'razz-apartment-models' ),
                    'not_found' => __( 'No Model found', 'razz-apartment-models' ),
                    'not_found_in_trash' => __( 'No Model found in Trash', 'razz-apartment-models' ),
                ),
                'public' => true,
                'has_archive' => false,
                'menu_position' => 5,
                'supports' => array( 'title', 'editor', 'thumbnail', 'tags' ),
                'capability_type' => 'post',
                'taxonomies' => array('razz-apt-model-tag')
            )
        );

        register_taxonomy('razz-apt-model-tag', 'razz-apt-model',
            array(
                'labels' => array(
                    'name' => __( 'Filter', 'razz-apartment-models' ),
                    'singular_name' => __( 'Filter', 'razz-apartment-models' ),
                    'add_new_item' => __( 'Add new Filter', 'razz-apartment-models' ),
                    'edit_item' => __( 'Edit Filter', 'razz-apartment-models' ),
                    'new_item' => __( 'New Filter', 'razz-apartment-models' ),
                    'view_item' => __( 'View Filter', 'razz-apartment-models' ),
                    'search_items' => __( 'Search Filters', 'razz-apartment-models' ),
                    'not_found' => __( 'No Filter found', 'razz-apartment-models' ),
                    'not_found_in_trash' => __( 'No Filter found in Trash', 'razz-apartment-models' ),
                    'all_items' => __( 'Filters', 'razz-apartment-models' )
                ),
                'public' => true,
                'show_tagcloud' => false,
                'show_in_quick_edit' => true
            )
        );
    }
}




if( function_exists('acf_add_local_field_group')  ):

    acf_add_local_field_group(array (
        'key' => 'group_557b518bc1d35',
        'title' => 'Model information',
        'fields' => array (
            array (
                'key' => 'field_557b51c6c9046',
                'label' => 'Sqft',
                'name' => 'sqft',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => 'c-lg-3',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => 'sqft',
                'min' => '',
                'max' => '',
                'step' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'field_557b533ac9049',
                'label' => 'Beds',
                'name' => 'bed',
                'type' => 'number',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => 'c-lg-3',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'min' => '',
                'max' => '',
                'step' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'field_557b533ac9048',
                'label' => 'Baths',
                'name' => 'bath',
                'type' => 'number',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => 'c-lg-3',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'min' => '',
                'max' => '',
                'step' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'field_557b51edc9047',
                'label' => 'Price',
                'name' => 'price',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => 'c-lg-3',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '$',
                'min' => '',
                'max' => '',
                'step' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'razz-apt-model',
                ),
            ),
        ),
        'menu_order' => -1,
        'position' => 'acf_after_title',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => array (
            0 => 'permalink',
//            1 => 'the_content',
            2 => 'excerpt',
            3 => 'custom_fields',
            4 => 'discussion',
            5 => 'comments',
            6 => 'slug',
            7 => 'author',
            8 => 'format',
            9 => 'page_attributes',
            10 => 'featured_image',
            11 => 'send-trackbacks',
        ),
    ));


    acf_add_local_field_group(array (
        'key' => 'group_z557b518bc1d35',
        'title' => 'Filters and text override',
        'fields' => array (
            array (
                'key' => 'field_557b53ccc904c',
                'label' => 'Price override',
                'name' => 'specific_price_override',
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'field_557b53e4c904d',
                'label' => 'image',
                'name' => 'image',
                'type' => 'image',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
                'library' => 'all',
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
            ),
            array (
                'key' => 'field_557b533jgh3jd',
                'label' => 'Description',
                'name' => 'description',
                'type' => 'textarea',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'min_width' => '',
                'min_height' => '',
                'min_size' => '',
                'max_width' => '',
                'max_height' => '',
                'max_size' => '',
                'mime_types' => '',
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'razz-apt-model',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'acf_after_title',
        'style' => 'default',
        'label_placement' => 'left',
        'instruction_placement' => 'label',
        'hide_on_screen' => array (
            0 => 'permalink',
//            1 => 'the_content',
            2 => 'excerpt',
            3 => 'custom_fields',
            4 => 'discussion',
            5 => 'comments',
            6 => 'slug',
            7 => 'author',
            8 => 'format',
            9 => 'page_attributes',
            10 => 'featured_image',
            11 => 'send-trackbacks',
        ),
    ));
endif;