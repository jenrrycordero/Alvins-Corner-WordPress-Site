<?php
/**
 * Created by PhpStorm.
 * User: Alian
 * Date: 6/15/2015
 * Time: 17:53
 */

if( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array (
        'key' => 'group_557eeacd3275c',
        'title' => 'Options',
        'fields' => array (
            array (
                'key' => 'field_557eeae1471db',
                'label' => 'Light-box options',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
            ),
            array (
                'key' => 'field_557eeb6b471dd',
                'label' => 'Info',
                'name' => '',
                'type' => 'message',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => 'This tab define the options for the light-box, what to show or hide and for some elements the information to override the default values.',
                'esc_html' => 0,
            ),
            array (
                'key' => 'field_557ef982e10fd',
                'label' => 'Description',
                'name' => 'description',
                'type' => 'wysiwyg',
                'instructions' => 'Write here any information do you want to display on the light-box',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'tabs' => 'all',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ),
            array (
                'key' => 'field_557eeb38471dc',
                'label' => 'Display Price?',
                'name' => 'display_price',
                'type' => 'true_false',
                'instructions' => 'Check to display the price.',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 1,
            ),
            array (
                'key' => 'field_557eeb9d471de',
                'label' => 'Price override label',
                'name' => 'price_override_label',
                'type' => 'text',
                'instructions' => 'Text to display instead of the price. This will affect all the models and take precedence above each model options.',
                'required' => 0,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'field_557eeb38471dc',
                            'operator' => '==',
                            'value' => '1',
                        ),
                    ),
                ),
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
                'key' => 'field_557eec07471df',
                'label' => 'Enable Apply now link?',
                'name' => 'enable_apply_now_link',
                'type' => 'true_false',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => 'separator-above',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 1,
            ),
            array (
                'key' => 'field_557eec2c471e0',
                'label' => 'Apply now URL',
                'name' => 'apply_now_url',
                'type' => 'url',
                'instructions' => 'Indicate the url for the apply now. Please use the full url ',
                'required' => 0,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'field_557eec07471df',
                            'operator' => '==',
                            'value' => '1',
                        ),
                    ),
                ),
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => 'http://www....',
            ),
            array (
                'key' => 'field_557eec63471e1',
                'label' => 'Apply now Label',
                'name' => 'apply_now_label',
                'type' => 'text',
                'instructions' => 'set and specific name for the link.',
                'required' => 0,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'field_557eec07471df',
                            'operator' => '==',
                            'value' => '1',
                        ),
                    ),
                ),
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 'Apply Now',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'field_557eeca1471e2',
                'label' => 'PDF button?',
                'name' => 'pdf_button',
                'type' => 'true_false',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => 'separator-above',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 1,
            ),
            array (
                'key' => 'field_557eed17471e3',
                'label' => 'PDF label',
                'name' => 'pdf_label',
                'type' => 'text',
                'instructions' => 'Indicate the name of the Print PDF button',
                'required' => 0,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'field_557eeca1471e2',
                            'operator' => '==',
                            'value' => '1',
                        ),
                    ),
                ),
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 'Print pdf',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'field_557eed3b471e4',
                'label' => 'Email a Friend link?',
                'name' => 'email_a_friend_link',
                'type' => 'true_false',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => 'separator-above',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 0,
            ),
            array (
                'key' => 'field_557eed66471e5',
                'label' => 'Email a Friend label',
                'name' => 'email_a_friend_label',
                'type' => 'text',
                'instructions' => 'Name to display for the "Email a Friend" button',
                'required' => 0,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'field_557eed3b471e4',
                            'operator' => '==',
                            'value' => '1',
                        ),
                    ),
                ),
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 'Email a Friend',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'field_557eed9d471e6',
                'label' => 'Email subject',
                'name' => 'email_subject',
                'type' => 'text',
                'instructions' => 'Indicate the subject for the email.',
                'required' => 0,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'field_557eed3b471e4',
                            'operator' => '==',
                            'value' => '1',
                        ),
                    ),
                ),
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 'Check this amassing property I found!',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'field_557eedf0471e7',
                'label' => 'Email Message',
                'name' => 'email_message',
                'type' => 'textarea',
                'instructions' => 'Write an small text to include in the body of the email.',
                'required' => 0,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'field_557eed3b471e4',
                            'operator' => '==',
                            'value' => '1',
                        ),
                    ),
                ),
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'maxlength' => '',
                'rows' => '',
                'new_lines' => 'wpautop',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'field_557eee2e471e8',
                'label' => 'Show Contact Us link?',
                'name' => 'show_contact_us_link',
                'type' => 'true_false',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => 'separator-above',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 1,
            ),
            array (
                'key' => 'field_557eee3e471e9',
                'label' => 'Contact Us label',
                'name' => 'contact_us_label',
                'type' => 'text',
                'instructions' => 'Write the name of the Contact Us button',
                'required' => 0,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'field_557eee2e471e8',
                            'operator' => '==',
                            'value' => '1',
                        ),
                    ),
                ),
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => 'Contact Us',
                'placeholder' => '',
                'prepend' => '',
                'append' => '',
                'maxlength' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
            array (
                'key' => 'field_557eee5d471ea',
                'label' => 'Model List section',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
            ),
            array (
                'key' => 'field_557eee69471eb',
                'label' => 'Shortcode',
                'name' => '',
                'type' => 'message',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '<b>[razz_show_models]</b>',
                'esc_html' => 0,
            ),
            array (
                'key' => 'field_557eeed2471ec',
                'label' => 'Show SQ ft?',
                'name' => 'show_sq_ft',
                'type' => 'true_false',
                'instructions' => 'Check to display the square feet information.',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 1,
            ),
            array (
                'key' => 'field_557eeefd471ed',
                'label' => 'Show Price?',
                'name' => 'show_price',
                'type' => 'true_false',
                'instructions' => 'Check to display the price information.',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'message' => '',
                'default_value' => 0,
            ),
            array (
                'key' => 'field_557eef46471ee',
                'label' => 'Price override label',
                'name' => 'price_override_label_model_list',
                'type' => 'text',
                'instructions' => 'Write here the override text for <b>ALL</b> the models when on the list view. This take precedence above each model section and <b>only</b> applies when on the list.',
                'required' => 0,
                'conditional_logic' => array (
                    array (
                        array (
                            'field' => 'field_557eeefd471ed',
                            'operator' => '==',
                            'value' => '1',
                        ),
                    ),
                ),
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
                'key' => 'field_557eef8e471ef',
                'label' => 'Customization',
                'name' => '',
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
            ),
            array (
                'key' => 'field_557eef97471f0',
                'label' => 'Custom CSS',
                'name' => 'custom_css',
                'type' => 'textarea',
                'instructions' => 'Use this block with care. Here you can set the custom css rules for this implementation.',
                'required' => 0,
                'conditional_logic' => 0,
                'razz-model-wrapper' => array (
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'default_value' => '',
                'placeholder' => '',
                'maxlength' => '',
                'rows' => '',
                'new_lines' => '',
                'readonly' => 0,
                'disabled' => 0,
            ),
        ),
        'location' => array (
            array (
                array (
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'razz_apartment_models',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'left',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
    ));

endif;