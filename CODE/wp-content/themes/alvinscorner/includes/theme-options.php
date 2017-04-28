<?php 
// Theme Options
// =============================================================================
function wp_theme_get_default_options() {
    $options = array(
        'logo_url' => '',
        'headerbg_url' => '',
        'applynow_url' => '',
        'contact_number' => '',
        'social_facebook_url' => '',
        'social_twitter_url' => '',
        'social_pinterest_url' => ''
    );
    return $options;
}

function wp_theme_options_setup() {
    global $pagenow;
 
    if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
        // Now we'll replace the 'Insert into Post Button' inside Thickbox
        add_filter( 'gettext', 'replace_thickbox_text'  , 1, 3 );
    }
}
add_action( 'admin_init', 'wp_theme_options_setup' );
 
function replace_thickbox_text($translated_text, $text, $domain) {
    if ('Insert into Post' == $text) {
        $referer = strpos( wp_get_referer(), 'theme-options' );
        $rquery = parse_url(wp_get_referer(), PHP_URL_QUERY);
        $rquery = parse_str($rquery,$rarray);

        if ( $referer != '' ) {
            if ($rarray['spec'] == 'logo') {
                return __('Set this as my site logo', 'wp-theme' );    
            }
            elseif ($rarray['spec'] == 'headerbg') {
                return __('Set this as my header background', 'wp-theme' );    
            }
            else {
                return __('Upload this image', 'wp-theme' );
            }
        }
    }
    return $translated_text;
}

function wp_theme_options_init() {
    $wp_theme_options = get_option( 'theme_wp_theme_options' );
 
    // Are our options saved in the DB?
    if ( false === $wp_theme_options ) {
        // If not, we'll save our default options
        $wp_theme_options = wp_theme_get_default_options();
        add_option( 'theme_wp_theme_options', $wp_theme_options );
    }
 
    // In other case we don't need to update the DB
}
// Initialize Theme options
add_action( 'after_setup_theme', 'wp_theme_options_init' );


// Add "wp_theme Options" link to the "Appearance" menu
function wp_theme_menu_options() {
    // add_theme_page( $page_title, $menu_title, $capability, $menu_slug, $function);
    // add_theme_page('Theme Options', 'Theme Options', 'edit_theme_options', 'wp_theme-settings', 'wp_theme_admin_options_page');
    add_menu_page('X Theme Options', 'X Theme Options', 'manage_options', 
        'theme-options', 'wp_theme_admin_options_page', NULL, 50);
}
// Load the Admin Options page
add_action('admin_menu', 'wp_theme_menu_options');
 
function wp_theme_admin_options_page() {
    ?>
        <!-- 'wrap','submit','icon32','button-primary' and 'button-secondary' are classes
        for a good WP Admin Panel viewing and are predefined by WP CSS -->
 
        <div class="wrap">
 
            <div id="icon-themes" class="icon32"><br /></div>
 
            <h2><?php _e( get_bloginfo('name').' - X Theme Options', 'wp_theme' ); ?></h2>
            <?php
            if (isset($_GET['settings-updated'])) { 
                if ($_GET['settings-updated'] === 'true') {
                ?>
                <div id="message" class="updated"><p>Settings saved.</p></div>
            <?php }
            } ?>
 
            <!-- If we have any error by submiting the form, they will appear here -->
            <?php settings_errors( 'wp_theme-settings-errors' ); ?>
 
            <form id="form-wp_theme-options" action="options.php" method="post" enctype="multipart/form-data">
 
                <?php
                    settings_fields('theme_wp_theme_options');
                    do_settings_sections('wp_theme');
                ?>
                <p class="submit">
                    <input name="theme_wp_theme_options[submit]" id="submit_options_form" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'wp_theme'); ?>" />
                    <input name="theme_wp_theme_options[reset]" type="submit" class="button-secondary" value="<?php esc_attr_e('Reset Defaults', 'wp_theme'); ?>" />
                </p>
 
            </form>
 
        </div>
    <?php
}

function wp_theme_options_settings_init() {
    register_setting( 'theme_wp_theme_options', 'theme_wp_theme_options', 'wp_theme_options_validate' );
 
    // Header Section
    add_settings_section('wp_theme_settings_header_text', __( 'Header', 'wp_theme' ), '', 'wp_theme');
    add_settings_field('wp_theme_setting_logo',  __( 'Site Logo', 'wp_theme' ), 'wp_theme_setting_logo', 'wp_theme', 'wp_theme_settings_header_text');
    add_settings_field('wp_theme_setting_logo_preview',  __( '', 'wp_theme' ), 'wp_theme_setting_logo_preview', 'wp_theme', 'wp_theme_settings_header_text');
    add_settings_field('wp_theme_setting_headerbg',  __( 'Site Header Background', 'wp_theme' ), 'wp_theme_setting_headerbg', 'wp_theme', 'wp_theme_settings_header_text');
    add_settings_field('wp_theme_setting_headerbg_preview',  __( '', 'wp_theme' ), 'wp_theme_setting_headerbg_preview', 'wp_theme', 'wp_theme_settings_header_text');
    add_settings_field('wp_theme_setting_applynow',  __( 'Apply Now URL', 'wp_theme' ), 'wp_theme_setting_applynow', 'wp_theme', 'wp_theme_settings_header_text');
    add_settings_field('wp_theme_setting_contact_number',  __( 'Contact Number', 'wp_theme' ), 'wp_theme_setting_contact_number', 'wp_theme', 'wp_theme_settings_header_text');

// Social Links Section
    add_settings_section('wp_theme_settings_sociallinks_text', __( 'Social Links', 'wp_theme' ), '', 'wp_theme');
    add_settings_field('wp_theme_setting_social_facebook',  __( 'Facebook', 'wp_theme' ), 'wp_theme_setting_social_facebook', 'wp_theme', 'wp_theme_settings_sociallinks_text');
    add_settings_field('wp_theme_setting_social_twitter',  __( 'Twitter', 'wp_theme' ), 'wp_theme_setting_social_twitter', 'wp_theme', 'wp_theme_settings_sociallinks_text');
    add_settings_field('wp_theme_setting_social_pinterest',  __( 'Pinterest', 'wp_theme' ), 'wp_theme_setting_social_pinterest', 'wp_theme', 'wp_theme_settings_sociallinks_text');

    // Footer Section
    // add_settings_section('wp_theme_settings_footer_text', __( 'Footer', 'wp_theme' ), '', 'wp_theme');
    // add_settings_field('wp_theme_setting_footer_copyright',  __( 'Copyright Text', 'wp_theme' ), 'wp_theme_setting_footer_copyright', 'wp_theme', 'wp_theme_settings_footer_text');
    // add_settings_field('wp_theme_setting_footer_copyright',  __( 'Right Footer Content', 'wp_theme' ), 'wp_theme_setting_footer_copyright', 'wp_theme', 'wp_theme_settings_footer_text');
}
add_action( 'admin_init', 'wp_theme_options_settings_init' );
 
function wp_theme_options_validate( $input ) {
    $default_options = wp_theme_get_default_options();
    $valid_input = $default_options;
 
    $submit = ! empty($input['submit']) ? true : false;
    $reset = ! empty($input['reset']) ? true : false;
 
    if ( $submit ) {
        $valid_input['logo_url'] = $input['logo_url'];
        $valid_input['headerbg_url'] = $input['headerbg_url'];
        $valid_input['applynow_url'] = $input['applynow_url'];
        $valid_input['contact_number'] = $input['contact_number'];
        $valid_input['social_facebook_url'] = $input['social_facebook_url'];
        $valid_input['social_twitter_url'] = $input['social_twitter_url'];
        $valid_input['social_pinterest_url'] = $input['social_pinterest_url'];
    }        
    elseif ( $reset ) {
        $valid_input['logo_url'] = $default_options['logo_url'];
        $valid_input['headerbg_url'] = $default_options['headerbg_url'];
        $valid_input['applynow_url'] = $default_options['applynow_url'];
        $valid_input['contact_number'] = $default_options['contact_number'];
        $valid_input['social_facebook_url'] = $default_options['social_facebook_url'];
        $valid_input['social_twitter_url'] = $default_options['social_twitter_url'];
        $valid_input['social_pinterest_url'] = $default_options['social_pinterest_url'];
    }
    return $valid_input;
}

function wp_theme_setting_logo() {
    $wp_theme_options = get_option( 'theme_wp_theme_options' );
    ?>
        <input type="text" id="logo_url" name="theme_wp_theme_options[logo_url]" value="<?php echo esc_url( $wp_theme_options['logo_url'] ); ?>" size="30" />
        <input id="upload_logo_button" type="button" class="button" value="<?php _e( 'Upload', 'wp_theme' ); ?>" />
        <span class="description"><?php _e('Upload an image for the site logo.', 'wp_theme' ); ?></span>
    <?php
}

function wp_theme_setting_headerbg() {
    $wp_theme_options = get_option( 'theme_wp_theme_options' );
    ?>
        <input type="text" id="headerbg_url" name="theme_wp_theme_options[headerbg_url]" value="<?php echo esc_url( $wp_theme_options['headerbg_url'] ); ?>" size="30" />
        <input id="upload_headerbg_button" type="button" class="button" value="<?php _e( 'Upload', 'wp_theme' ); ?>" />
        <span class="description"><?php _e('Upload an image for the header background.', 'wp_theme' ); ?></span>
    <?php
}

function wp_theme_options_enqueue_scripts() {
    wp_register_script( 'wp_theme-upload', get_stylesheet_directory_uri() .'/js/wp-theme-upload.js', array('jquery','media-upload','thickbox') );
 
    if ( 'toplevel_page_theme-options' == get_current_screen() -> id ) {
        wp_enqueue_script('jquery');
 
        wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');
 
        wp_enqueue_script('media-upload');
        wp_enqueue_script('wp_theme-upload');
 
    }
}
add_action('admin_enqueue_scripts', 'wp_theme_options_enqueue_scripts');

function wp_theme_setting_logo_preview() {
    $wp_theme_options = get_option( 'theme_wp_theme_options' );  ?>
    <div id="upload_logo_preview" style="min-height: 100px; display: inline;">
        <img style="max-width:100%;" src="<?php echo esc_url( $wp_theme_options['logo_url'] ); ?>" />
    </div>
    <?php
}

function wp_theme_setting_headerbg_preview() {
    $wp_theme_options = get_option( 'theme_wp_theme_options' );  ?>
    <div id="upload_headerbg_preview" style="min-height: 100px; display: inline;">
        <img style="max-width:100%;" src="<?php echo esc_url( $wp_theme_options['headerbg_url'] ); ?>" />
    </div>
    <?php
}


function wp_theme_setting_applynow() {
    $wp_theme_options = get_option( 'theme_wp_theme_options' );
    ?>
        <input type="text" id="applynow_url" name="theme_wp_theme_options[applynow_url]" value="<?php echo $wp_theme_options['applynow_url']; ?>" size="45" />
        <span class="description"><?php _e('Paste your Apply Now URL here.', 'wp_theme' ); ?></span>
    <?php
}

function wp_theme_setting_contact_number() {
    $wp_theme_options = get_option( 'theme_wp_theme_options' );
    ?>
        <input type="text" id="contact_number" name="theme_wp_theme_options[contact_number]" value="<?php echo $wp_theme_options['contact_number']; ?>" size="45" />
        <span class="description"><?php _e('Place your contact number here.', 'wp_theme' ); ?></span>
    <?php
}

function wp_theme_setting_social_facebook() {
    $wp_theme_options = get_option( 'theme_wp_theme_options' );
    ?>
        <input type="text" id="social_facebook_url" name="theme_wp_theme_options[social_facebook_url]" value="<?php echo $wp_theme_options['social_facebook_url']; ?>" size="45" />
        <span class="description"><?php _e('Paste your Facebook profile URL here.', 'wp_theme' ); ?></span>
    <?php
}

function wp_theme_setting_social_twitter() {
    $wp_theme_options = get_option( 'theme_wp_theme_options' );
    ?>
        <input type="text" id="social_twitter_url" name="theme_wp_theme_options[social_twitter_url]" value="<?php echo $wp_theme_options['social_twitter_url']; ?>" size="45" />
        <span class="description"><?php _e('Paste your Twitter profile URL here.', 'wp_theme' ); ?></span>
    <?php
}

function wp_theme_setting_social_pinterest() {
    $wp_theme_options = get_option( 'theme_wp_theme_options' );
    ?>
        <input type="text" id="social_pinterest_url" name="theme_wp_theme_options[social_pinterest_url]" value="<?php echo $wp_theme_options['social_pinterest_url']; ?>" size="45" />
        <span class="description"><?php _e('Paste your Pinterest profile URL here.', 'wp_theme' ); ?></span>
    <?php
}

function wp_theme_options(){
    global $wp_theme_options;
    $wp_theme_options = get_option( 'theme_wp_theme_options' ); 
}   