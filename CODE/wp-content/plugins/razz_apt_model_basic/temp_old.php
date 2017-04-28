<?php





add_action( 'add_meta_boxes', 'razz_apt_model_metabox' );
if ( !function_exists("razz_apt_model_metabox") )
{
    function razz_apt_model_metabox()
    {
        add_meta_box('razz_apt_model_info', 'Model information', 'razz_apt_model_metabox_display', 'razz-apt-model', 'normal', 'high');
    }
}

if ( !function_exists('razz_apt_model_metabox_display') )
{
    function razz_apt_model_metabox_display($post)
    {
        wp_nonce_field( 'razz_apt_model_info', 'razz_apt_model_info_nonce' );

        $value = get_post_meta( $post->ID, 'razz_apt_model_info_values', 1 );

//        echo "<pre>";
//        var_dump($value);
//        echo "</pre>";
        ?>

        <div class="metabox-field-group">
            <div class="title"><?php _e( 'General info about the model', 'razz-apartment-models' )?></div>
    <span class="inline-input">
        <label>Area:</label>
        <input type='text' name='razz_apt_model_info_values[sqft]' class="small-input"
               value='<?php echo isset($value['sqft']) ? $value['sqft'] : ""; ?>'><sub>sq ft</sub>
    </span>
    <span class="inline-input">
        <label>Price:</label>
        <input type='text' name='razz_apt_model_info_values[price]' class="small-input"
               value='<?php echo isset($value['price']) ? $value['price'] : ""; ?>'>$
    </span>
    <span class="inline-input">
        <label>Beds:</label>
        <input type='text' name='razz_apt_model_info_values[bed]' class="small-input"
               value='<?php echo isset($value['bed']) ? $value['bed'] : ""; ?>'>
    </span>
    <span class="inline-input">
        <label>Baths:</label>
        <input type='text' name='razz_apt_model_info_values[bath]' class="small-input"
               value='<?php echo isset($value['bath']) ? $value['bath'] : ""; ?>'>
    </span>
        </div>

        <div class="metabox-field-group">
            <div class="title"><?php _e( 'Override price input', 'razz-apartment-models' )?></div>
            <input type='text' name='razz_apt_model_info_values[specific_price_override]' class="medium-input" placeholder=""
                   value='<?php echo isset($value['specific_price_override']) ? $value['specific_price_override'] : ""; ?>'>
            <span class="dashicons dashicons-editor-help" title="If you fill this input the text will be used instead of the price."></span>
        </div>

        <div class="metabox-field-group">
            <div class="title"><?php _e( 'Labels for the model', 'razz-apartment-models' )?></div>
            <input id=autocomplete type='text' name='razz_apt_model_info_values[labels]' class="medium-input" placeholder=""
                   value='<?php echo isset($value['labels']) ? $value['labels'] : ""; ?>'>
            <span class="dashicons dashicons-editor-help" title="This labels are used to populate the filters on the front view."></span>
            <div style="display:none">
                <script>
                    autocompleteSource = <?php echo json_encode( array_flip(razz_get_apartment_model_filters()) );?>;
                </script>
            </div>
        </div>

        <div class="metabox-field-group">
            <div class="title"><?php _e( 'Image for the Model', 'razz-apartment-models' )?></div>

            <?php
            $extra_class = "";
            $img_path = "";
            $img_id = "";

            if ( isset ( $value['img'] ) && $value['img'] != "" ) :
                $extra_class = "hidden";
                $img_path = $value['img'];
                $img_id = $value['img-id'];
                ?>
                <div class="position-relative display-inline-block">
                    <?php echo wp_get_attachment_image($img_id)?>
                    <span id='remove-icon' class="dashicons dashicons-no-alt tooltip" title="<?php _e('Delete image', 'razz-apartment-models'); ?>"></span>
                </div>
            <?php endif; ?>

            <div id='meta-image-razz-model-wrapper' class="<?php echo $extra_class; ?>">
                <input type="text" name="razz_apt_model_info_values[img]" id="meta-image" value="<?php echo $img_path; ?>" />
                <input type="hidden" name="razz_apt_model_info_values[img-id]" id="meta-image-id" value="<?php echo $img_id; ?>" />
                <button id="meta-image-button" class="button"><?php _e( 'Choose or Upload an Image', 'razz-apartment-models'); ?></button>
            </div>
        </div>

    <?php
    }
}



add_action( 'save_post', 'razz_apt_model_metabox_save' );
if ( !function_exists('razz_apt_model_metabox_save') )
{
    function razz_apt_model_metabox_save($post_id)
    {
        // Check if our nonce is set Verify that the nonce is valid.
        if ( !isset( $_POST['razz_apt_model_info_nonce'] ) ||
             !wp_verify_nonce( $_POST['razz_apt_model_info_nonce'], 'razz_apt_model_info' ) )
            return;

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( isset( $_POST['razz-apt-model'] ) && 'page' == $_POST['razz-apt-model'] )
        {
            if ( ! current_user_can( 'edit_page', $post_id ) )
                return;
        }
        else
        {
            if ( ! current_user_can( 'edit_post', $post_id ) )
                return;
        }

        // Make sure that it is set.
        if ( ! isset( $_POST['razz_apt_model_info_values'] ) ) {
            return;
        }

        /* OK, it's safe for us to save the data now. */


        // Sanitize user input.
        $values['sqft'] = sanitize_text_field( $_POST['razz_apt_model_info_values']['sqft'] );
        $values['bed'] = sanitize_text_field( $_POST['razz_apt_model_info_values']['bed'] );
        $values['bath'] = sanitize_text_field( $_POST['razz_apt_model_info_values']['bath'] );
        $values['price'] = sanitize_text_field( $_POST['razz_apt_model_info_values']['price'] );
        $values['labels'] = trim( sanitize_text_field( $_POST['razz_apt_model_info_values']['labels'] ), ", ");
        $values['specific_price_override'] = ( sanitize_text_field( $_POST['razz_apt_model_info_values']['specific_price_override'] ) );

        $values['img'] = sanitize_text_field( $_POST['razz_apt_model_info_values']['img'] );
        $values['img-id'] = sanitize_text_field( $_POST['razz_apt_model_info_values']['img-id'] );

        // Update the meta field in the database.
        update_post_meta( $post_id, 'razz_apt_model_info_values', $values );

        //we save the labels again to facilitate the retrieve for the display.
        update_post_meta( $post_id, 'razz_apt_model_info_labels', sanitize_text_field( $_POST['razz_apt_model_info_values']['labels'] ) );

//        update_post_meta( $post_id, 'razz_apt_model_info_values[sqft]', $values['sqft'] );
//        update_post_meta( $post_id, 'razz_apt_model_info_values[bed]', $values['bed'] );
//        update_post_meta( $post_id, 'razz_apt_model_info_values[bath]', $values['bath'] );
    }
}







/**********************************************************************************************************************
 ***                      OLD SETTINGS FILE
 */

add_action( 'admin_menu', 'razz_add_admin_menu' );
add_action( 'admin_init', 'razz_settings_init' );

if ( !function_exists('razz_add_admin_menu') )
{
    function razz_add_admin_menu(  )
    {
//        add_menu_page( 'Razz Apartment Models', 'R.A.M. Options', 'manage_options', 'razz_apartment_models', 'razz_apartment_models_options_page' );
    }
}

if ( !function_exists("razz_settings_init") )
{
    function razz_settings_init(  )
    {
        register_setting( 'pluginPage-razz_settings', 'razz_settings' );

        add_settings_section(
            'razz_settings-section',
            __( 'Model lightbox section', 'razz-apartment-models' ),
            'razz_settings_section_1_callback',
            'pluginPage-razz_settings'
        );


        add_settings_field( 'lb-price', __('Show price range?', 'razz-apartment-models' ), 'razz_settings_checkbox_field',
            'pluginPage-razz_settings', 'razz_settings-section', array('lb-price'));
        add_settings_field( 'lb-price-label', __('Price range override', 'razz-apartment-models' ), 'razz_settings_text_field',
            'pluginPage-razz_settings', 'razz_settings-section', array('lb-price-label'));

        add_settings_field( 'lb-apply', __('Enable Apply now link?', 'razz-apartment-models' ), 'razz_settings_checkbox_field',
            'pluginPage-razz_settings', 'razz_settings-section', array('lb-apply'));
        add_settings_field( 'lb-apply-url', __('Apply now link', 'razz-apartment-models' ), 'razz_settings_text_field',
            'pluginPage-razz_settings', 'razz_settings-section', array('lb-apply-url'));
        add_settings_field( 'lb-apply-label', __('Apply Now text override', 'razz-apartment-models' ), 'razz_settings_text_field',
            'pluginPage-razz_settings', 'razz_settings-section', array('lb-apply-label'));

        add_settings_field( 'lb-print-pdf', __('Enable Print PDF link?', 'razz-apartment-models' ), 'razz_settings_checkbox_field',
            'pluginPage-razz_settings','razz_settings-section', array('lb-print-pdf') );
        add_settings_field( 'lb-print-pdf-label', __('Print PDF text override', 'razz-apartment-models' ), 'razz_settings_text_field',
            'pluginPage-razz_settings','razz_settings-section', array('lb-print-pdf-label') );

        add_settings_field( 'lb-email', __('Enable Email a friend link?', 'razz-apartment-models' ), 'razz_settings_checkbox_field',
            'pluginPage-razz_settings', 'razz_settings-section', array('lb-email'));
        add_settings_field( 'lb-email-subject', __('Email subject', 'razz-apartment-models' ), 'razz_settings_text_field',
            'pluginPage-razz_settings', 'razz_settings-section', array('lb-email-subject'));
        add_settings_field( 'lb-email-text', __('Email Text', 'razz-apartment-models' ), 'razz_settings_textarea_field',
            'pluginPage-razz_settings', 'razz_settings-section', array('lb-email-text'));
        add_settings_field( 'lb-email-label', __('Email a friend text override', 'razz-apartment-models' ), 'razz_settings_text_field',
            'pluginPage-razz_settings', 'razz_settings-section', array('lb-email-label'));

        add_settings_field( 'lb-contact', __('Enable Contact us link?', 'razz-apartment-models' ), 'razz_settings_checkbox_field',
            'pluginPage-razz_settings', 'razz_settings-section', array('lb-contact'));
        add_settings_field( 'lb-contact-label', __('Contact us text override', 'razz-apartment-models' ), 'razz_settings_text_field',
            'pluginPage-razz_settings', 'razz_settings-section', array('lb-contact-label'));

        add_settings_field( 'lb-description-text', __('Description text', 'razz-apartment-models' ), 'razz_settings_textarea_field',
            'pluginPage-razz_settings', 'razz_settings-section', array('lb-description-text') );


        add_settings_section(
            'razz_settings-section-2',
            __( 'Model List section', 'razz-apartment-models' ),
            'razz_settings_section_2_callback',
            'pluginPage-razz_settings'
        );


        add_settings_field( '', __('Shortcode information', 'razz-apartment-models' ), 'razz_settings_msg_field',
            'pluginPage-razz_settings', 'razz_settings-section-2', array('msg' => "<b>[razz_show_models]</b>"));

        add_settings_field( 'page-sq-ft', __('Show SQ ft?', 'razz-apartment-models' ), 'razz_settings_checkbox_field',
            'pluginPage-razz_settings', 'razz_settings-section-2', array('page-sq-ft'));

        add_settings_field( 'page-price', __('Show Price?', 'razz-apartment-models' ), 'razz_settings_checkbox_field',
            'pluginPage-razz_settings', 'razz_settings-section-2', array('page-price'));
        add_settings_field( 'page-price-label', __('Price range override', 'razz-apartment-models' ), 'razz_settings_text_field',
            'pluginPage-razz_settings', 'razz_settings-section-2', array('page-price-label'));


        add_settings_section(
            'razz_settings-section-3',
            __( 'Style block', 'razz-apartment-models' ),
            'razz_settings_section_3_callback',
            'pluginPage-razz_settings'
        );

        add_settings_field( 'style-css', __('Add Custom CSS', 'razz-apartment-models' ), 'razz_settings_textarea_field',
            'pluginPage-razz_settings', 'razz_settings-section-3', array('style-css', 'class' => 'css'));

    }
}


if ( !function_exists('razz_settings_section_1_callback') )
{
    function razz_settings_section_1_callback( ) {
        echo __( 'This information is used on the lightbox, here you can mark to show some elements or override text information',
            'razz-apartment-models' );
    }
}

if ( !function_exists('razz_settings_section_2_callback') )
{
    function razz_settings_section_2_callback( ) {
        echo __( 'This block control the elements to show on the list of models',
            'razz-apartment-models' );
    }
}

if ( !function_exists('razz_settings_section_3_callback') )
{
    function razz_settings_section_3_callback( ) {
        echo __( 'Use this part with caution. Here you can paste your specific css rules.',
            'razz-apartment-models' );
    }
}


if ( !function_exists("razz_settings_msg_field") )
{
    function razz_settings_msg_field($args)
    {
        echo "<span class='msg'>" . $args['msg'] . "</span>";
    }
}

if ( !function_exists('razz_settings_text_field') )
{
    function razz_settings_text_field($args)
    {
        $setting_name = $args[0];
        $options = get_option('razz_settings');
        $option_value = isset( $options[$setting_name] ) ? $options[$setting_name] : "";

        echo "<input type='text' name='razz_settings[$setting_name]' value='$option_value'>";
    }
}

if ( !function_exists('razz_settings_checkbox_field') )
{
    function razz_settings_checkbox_field($args)
    {
        $setting_name = $args[0];
        $options = get_option('razz_settings');

        echo "<input type='checkbox' name='razz_settings[$setting_name]' value='1'";
        checked( $options[$setting_name], 1 );
        echo ">";
    }
}

if ( !function_exists('razz_settings_textarea_field') )
{
    function razz_settings_textarea_field($args)
    {
        $setting_name = $args[0];
        $options = get_option('razz_settings');
        $option_value = isset( $options[$setting_name] ) ? $options[$setting_name] : "";
        $class = $args['class'] ?: "";

        echo "<textarea name='razz_settings[$setting_name]' class='$class'>$option_value</textarea>";
    }
}



if ( !function_exists("razz_apartment_models_options_page") )
{
    function razz_apartment_models_options_page()
    {
        ?>
        <form action='options.php' method='post'>
            <h2>Razz Apartment Models</h2>

            <?php
            settings_fields( 'pluginPage-razz_settings' );
            do_settings_sections( 'pluginPage-razz_settings' );
            submit_button();
            ?>

        </form>
    <?php
    }
}