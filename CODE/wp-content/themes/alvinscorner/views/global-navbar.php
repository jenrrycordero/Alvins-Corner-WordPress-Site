<?php

// =============================================================================
// VIEWS/GLOBAL/_NAVBAR.PHP
// -----------------------------------------------------------------------------
// Outputs the navbar.
// =============================================================================

$navbar_position = x_get_navbar_positioning();
$logo_nav_layout = x_get_logo_navigation_layout();
$is_one_page_nav = x_is_one_page_navigation();

global $wp_theme_options;
?>

<?php if ( ( $navbar_position == 'static-top' || $navbar_position == 'fixed-top' || $is_one_page_nav ) && $logo_nav_layout == 'stacked' ) : ?>

  <div class="x-logobar">
    <div class="x-logobar-inner">
      <div class="x-container-fluid max width">
        <?php x_get_view( 'global', '_brand' ); ?>
      </div>
    </div> <!-- end .x-logobar-inner -->
  </div> <!-- end .x-logobar -->

    <div class="x-logobar-2">
        <div class="x-logobar-inner">
            <div class="x-container-fluid max width">
                <?php x_get_view( 'global', '_brand' ); ?>
            </div>
        </div> <!-- end .x-logobar-inner -->
    </div> <!-- end .x-logobar -->

  <div class="x-navbar-wrap">
    <div class="<?php x_navbar_class(); ?>"><?php 
//      var_dump($wp_theme_options); ?>
      <div class="x-navbar-inner">
        <div class="x-container-fluid max width">
          <?php x_get_view( 'global', '_nav', 'primary' ); ?>
          <?php x_social_global(); ?>
            <div style="width: 100%; height: 4px; position: relative;">
                <div style="width: 52px; background: none repeat scroll 0% 0% #63666a; height: 4px; position: absolute; left: 794.967px;" id="head_menu_top"></div>
            </div>
        </div>
      </div> <!-- end .x-navbar-inner -->
    </div> <!-- end .x-navbar -->
  </div> <!-- end .x-navbar-wrap -->

<?php else : ?>

  <div class="x-navbar-wrap">
    <div class="<?php x_navbar_class(); ?>"<?php
      if (!empty($wp_theme_options['headerbg_url'])) {
        echo ' style="background: url(&#39;' . $wp_theme_options['headerbg_url'] . '&#39;)"';
        }
    ?>>
      <div class="x-navbar-inner">
        <div class="x-container-fluid max width">
          <?php
            if (!empty($wp_theme_options['logo_url'])) { echo '<h1 class="site-logo"><a href="'. home_url().'"><img src="'.$wp_theme_options['logo_url'].'" /></a></h1>'; }
            else { x_get_view( 'global', '_brand' ); }
          ?> 
          <div class="topbar-container">
            <?php if(!empty($wp_theme_options['contact_number']) || !empty($wp_theme_options['applynow_url'])) {
              echo '<div class="top-content">';
            } ?>
            <?php 
              if (!empty($wp_theme_options['contact_number'])) { echo '<span class="contact-number">Call '.$wp_theme_options['contact_number'].'</span>'; }
              if (!empty($wp_theme_options['applynow_url'])) { echo '<a class="applynow-button" href="'.esc_attr($wp_theme_options["applynow_url"]).'" target="_blank">Apply Now</a>'; }
            ?>
            <?php if(!empty($wp_theme_options['contact_number']) || !empty($wp_theme_options['applynow_url'])) {
              echo '</div>';
            } ?>
            <?php if(!empty($wp_theme_options['social_facebook_url']) || !empty($wp_theme_options['social_twitter_url']) || !empty($wp_theme_options['social_pinterest_url'])) {
              echo '<div class="top-social">';
            } ?>
             <?php 
              if (!empty($wp_theme_options['social_facebook_url'])) { echo '<a class="social-icons facebook" href="'.esc_attr($wp_theme_options['social_facebook_url']).'" target="_blank"><i class="fa fa-facebook"></i></a>'; }
              if (!empty($wp_theme_options['social_twitter_url'])) { echo '<a class="social-icons twitter" href="'.esc_attr($wp_theme_options['social_twitter_url']).'" target="_blank"><i class="fa fa-twitter"></i></a>'; }
              if (!empty($wp_theme_options['social_pinterest_url'])) { echo '<a class="social-icons pinterest" href="'.esc_attr($wp_theme_options['social_pinterest_url']).'" target="_blank"><i class="fa fa-pinterest"></i></a>'; }
            ?>
            <?php if(!empty($wp_theme_options['social_facebook_url']) || !empty($wp_theme_options['social_twitter_url']) || !empty($wp_theme_options['social_pinterest_url'])) {
              echo '</div>';
            } ?>
            <?php  x_get_view( 'global', '_nav', 'primary' ); ?>
          </div>
        </div>
      </div> <!-- end .x-navbar-inner -->
    </div> <!-- end .x-navbar -->
  </div> <!-- end .x-navbar-wrap -->

<?php endif; ?>