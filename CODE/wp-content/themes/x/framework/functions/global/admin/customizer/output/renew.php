<?php
 
// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/CUSTOMIZER/OUTPUT/RENEW.PHP
// -----------------------------------------------------------------------------
// Renew CSS ouptut.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Site Link Color Accents
//   02. Container Sizing
//   03. Layout Sizing
//   04. Masthead
//   05. Navbar
//   06. Navbar - Positioning
//   07. Navbar - Dropdowns
//   08. Colophon
//   09. Custom Fonts
//   10. Custom Fonts - Colors
//   11. Responsive Styling
// =============================================================================

$x_renew_entry_icon_color               = x_get_option( 'x_renew_entry_icon_color' );
$x_renew_topbar_text_color              = x_get_option( 'x_renew_topbar_text_color' );
$x_renew_topbar_link_color_hover        = x_get_option( 'x_renew_topbar_link_color_hover' );
$x_renew_topbar_background              = x_get_option( 'x_renew_topbar_background' );
$x_renew_logobar_background             = x_get_option( 'x_renew_logobar_background' );
$x_renew_navbar_button_color            = x_get_option( 'x_renew_navbar_button_color' );
$x_renew_navbar_background              = x_get_option( 'x_renew_navbar_background' );
$x_renew_navbar_button_background_hover = x_get_option( 'x_renew_navbar_button_background_hover' );
$x_renew_navbar_button_background       = x_get_option( 'x_renew_navbar_button_background' );
$x_renew_footer_background              = x_get_option( 'x_renew_footer_background' );
$x_renew_footer_text_color              = x_get_option( 'x_renew_footer_text_color' );
$x_renew_entry_icon_position            = x_get_option( 'x_renew_entry_icon_position' );
$x_renew_entry_icon_position_vertical   = x_get_option( 'x_renew_entry_icon_position_vertical' );
$x_renew_entry_icon_position_horizontal = x_get_option( 'x_renew_entry_icon_position_horizontal' );

?>

/* Site Link Color Accents
// ========================================================================== */

/*
// Color.
*/

a,
h1 a:hover,
h2 a:hover,
h3 a:hover,
h4 a:hover,
h5 a:hover,
h6 a:hover,
.x-comment-time:hover,
#reply-title small a,
.comment-reply-link:hover,
.x-comment-author a:hover {
  color: <?php echo $x_site_link_color; ?>;
}

a:hover,
#reply-title small a:hover,
.x-recent-posts a:hover .h-recent-posts {
  color: <?php echo $x_site_link_color_hover; ?>;
}

.entry-title:before {
  color: <?php echo $x_renew_entry_icon_color; ?>;
}

<?php if ( X_WOOCOMMERCE_IS_ACTIVE ) : ?>

  .woocommerce .price > .amount,
  .woocommerce .price > ins > .amount,
  .woocommerce-page .price > .amount,
  .woocommerce-page .price > ins > .amount,
  .woocommerce li.product .entry-header h3 a:hover,
  .woocommerce-page li.product .entry-header h3 a:hover,
  .woocommerce .star-rating:before,
  .woocommerce-page .star-rating:before,
  .woocommerce .star-rating span:before,
  .woocommerce-page .star-rating span:before {
    color: <?php echo $x_site_link_color; ?>;
  }

<?php endif; ?>


/*
// Border color.
*/

a.x-img-thumbnail:hover,
li.bypostauthor > article.comment {
  border-color: <?php echo $x_site_link_color; ?>;
}

<?php if ( X_WOOCOMMERCE_IS_ACTIVE ) : ?>

  .woocommerce div.product .woocommerce-tabs .x-comments-area li.comment.bypostauthor .x-comment-header .star-rating-container,
  .woocommerce-page div.product .woocommerce-tabs .x-comments-area li.comment.bypostauthor .x-comment-header .star-rating-container {
    border-color: <?php echo $x_site_link_color; ?>;
  }

<?php endif; ?>


/*
// Background color.
*/

.flex-direction-nav a,
.flex-control-nav a:hover,
.flex-control-nav a.flex-active,
.x-dropcap,
.x-skill-bar .bar,
.x-pricing-column.featured h2,
.h-comments-title small,
.pagination a:hover,
.x-entry-share .x-share:hover,
.entry-thumb,
.widget_tag_cloud .tagcloud a:hover,
.widget_product_tag_cloud .tagcloud a:hover,
.x-highlight,
.x-recent-posts .x-recent-posts-img,
.x-recent-posts .x-recent-posts-img:before,
.x-portfolio-filters {
  background-color: <?php echo $x_site_link_color; ?>;
}

.x-recent-posts a:hover .x-recent-posts-img,
.x-portfolio-filters:hover {
  background-color: <?php echo $x_site_link_color_hover; ?>;
}

<?php if ( X_WOOCOMMERCE_IS_ACTIVE ) : ?>

  .woocommerce .onsale,
  .woocommerce-page .onsale,
  .widget_price_filter .ui-slider .ui-slider-range,
  .woocommerce div.product .woocommerce-tabs .x-comments-area li.comment.bypostauthor article.comment:before,
  .woocommerce-page div.product .woocommerce-tabs .x-comments-area li.comment.bypostauthor article.comment:before {
    background-color: <?php echo $x_site_link_color; ?>;
  }

<?php endif; ?>



/* Container Sizing
// ========================================================================== */

.x-container-fluid.width {
  width: <?php echo $x_layout_site_width . '%'; ?>;
}

.x-container-fluid.max {
  max-width: <?php echo $x_layout_site_max_width . 'px'; ?>;
}

<?php if ( $x_layout_site == 'boxed' ) :; ?>

  .site,
  .x-navbar.x-navbar-fixed-top.x-container-fluid.max.width {
    width: <?php echo $x_layout_site_width . '%'; ?>;
    max-width: <?php echo $x_layout_site_max_width . 'px'; ?>;
  }

<?php endif; ?>



/* Layout Sizing
// ========================================================================== */

.x-main {
  width: <?php echo $x_layout_content_width - 3.20197 . '%'; ?>;
}

.x-sidebar {
  width: <?php echo 100 - 3.20197 - $x_layout_content_width . '%'; ?>;
}



/* Masthead
// ========================================================================== */

.x-topbar .p-info,
.x-topbar .p-info a,
.x-topbar .x-social-global a {
  color: <?php echo $x_renew_topbar_text_color; ?>;
}

.x-topbar .p-info a:hover {
  color: <?php echo $x_renew_topbar_link_color_hover; ?>;
}

.x-topbar {
  background-color: <?php echo $x_renew_topbar_background; ?>;
}

<?php if ( $x_logo_navigation_layout == 'stacked' ) : ?>

  .x-logobar {
    background-color: <?php echo $x_renew_logobar_background; ?>;
  }

<?php endif; ?>



/* Navbar
// ========================================================================== */

.x-navbar .desktop .x-nav > li:before {
  padding-top: <?php echo $x_navbar_adjust_links_top . 'px'; ?>;
}


/*
// Color.
*/

.x-brand,
.x-brand:hover,
.x-navbar .desktop .x-nav > li > a,
.x-navbar .desktop .sub-menu li > a,
.x-navbar .mobile .x-nav li a {
  color: <?php echo $x_navbar_link_color; ?>;
}

.x-navbar .desktop .x-nav > li > a:hover,
.x-navbar .desktop .x-nav > .x-active > a,
.x-navbar .desktop .x-nav > .current-menu-item > a,
.x-navbar .desktop .sub-menu li > a:hover,
.x-navbar .desktop .sub-menu li.x-active > a,
.x-navbar .desktop .sub-menu li.current-menu-item > a,
.x-navbar .desktop .x-nav .x-megamenu > .sub-menu > li > a,
.x-navbar .mobile .x-nav li > a:hover,
.x-navbar .mobile .x-nav li.x-active > a,
.x-navbar .mobile .x-nav li.current-menu-item > a {
  color: <?php echo $x_navbar_link_color_hover; ?>;
}

.x-btn-navbar,
.x-btn-navbar:hover {
  color: <?php echo $x_renew_navbar_button_color; ?>;
}


/*
// Background color.
*/

.x-navbar .desktop .sub-menu li:before,
.x-navbar .desktop .sub-menu li:after {
  background-color: <?php echo $x_navbar_link_color; ?>;
}

.x-navbar,
.x-navbar .sub-menu {
  background-color: <?php echo $x_renew_navbar_background; ?> !important;
}

.x-btn-navbar,
.x-btn-navbar.collapsed:hover {
  background-color: <?php echo $x_renew_navbar_button_background_hover; ?>;
}

.x-btn-navbar.collapsed {
  background-color: <?php echo $x_renew_navbar_button_background; ?>;
}


/*
// Box shadow.
*/

.x-navbar .desktop .x-nav > li > a:hover > span,
.x-navbar .desktop .x-nav > li.x-active > a > span,
.x-navbar .desktop .x-nav > li.current-menu-item > a > span {
  box-shadow: 0 2px 0 0 <?php echo $x_navbar_link_color_hover; ?>;
}



/* Navbar - Positioning
// ========================================================================== */

<?php if ( $x_navbar_positioning == 'static-top' || $x_navbar_positioning == 'fixed-top' ) : ?>

  .x-navbar .desktop .x-nav > li > a {
    height: <?php echo $x_navbar_height . 'px'; ?>;
    padding-top: <?php echo $x_navbar_adjust_links_top . 'px'; ?>;
  }

<?php endif; ?>

<?php if ( $x_navbar_positioning == 'fixed-left' || $x_navbar_positioning == 'fixed-right' ) : ?>

  .x-navbar .desktop .x-nav > li > a {
    padding-top: <?php echo round( ( $x_navbar_adjust_links_side - $x_navbar_font_size ) / 2 ) . 'px'; ?>;
    padding-bottom: <?php echo round( ( $x_navbar_adjust_links_side - $x_navbar_font_size ) / 2 ) . 'px'; ?>;
    padding-left: 10%;
    padding-right: 10%;
  }

  .desktop .x-megamenu > .sub-menu {
    width: <?php echo 879 - $x_navbar_width . 'px'; ?>
  }

<?php endif; ?>

<?php if ( $x_navbar_positioning == 'fixed-left' ) : ?>

  .x-widgetbar {
    left: <?php echo $x_navbar_width . 'px'; ?>;
  }

<?php endif; ?>

<?php if ( $x_navbar_positioning == 'fixed-right' ) : ?>

  .x-widgetbar {
    right: <?php echo $x_navbar_width . 'px'; ?>;
  }

<?php endif; ?>



/* Navbar - Dropdowns
// ========================================================================== */

.x-navbar .desktop .x-nav > li ul {
  top: <?php echo $x_navbar_height . 'px'; ?>;
}



/* Colophon
// ========================================================================== */

.x-colophon.bottom {
  background-color: <?php echo $x_renew_footer_background; ?>;
}

.x-colophon.bottom,
.x-colophon.bottom a,
.x-colophon.bottom .x-social-global a {
  color: <?php echo $x_renew_footer_text_color; ?>;
}



/* Custom Fonts
// ========================================================================== */

.h-landmark {
  font-weight: <?php echo $x_body_font_weight; ?>;
  <?php if ( x_is_font_italic( $x_body_font_weight_and_style ) ) : ?>
    font-style: italic;
  <?php endif; ?>
}



/* Custom Fonts - Colors
// ========================================================================== */

/*
// Brand.
*/

<?php if ( $x_logo_font_color_enable == '1' ) : ?>

  .x-brand,
  .x-brand:hover {
    color: <?php echo $x_logo_font_color; ?>;
  }

<?php endif; ?>


/*
// Body.
*/

<?php if ( $x_body_font_color_enable == '1' ) : ?>

  .x-comment-author a {
    color: <?php echo $x_body_font_color; ?>;
  }

  <?php if ( X_WOOCOMMERCE_IS_ACTIVE ) : ?>

    .woocommerce .price > .from,
    .woocommerce .price > del,
    .woocommerce p.stars span a:after,
    .woocommerce-page .price > .from,
    .woocommerce-page .price > del,
    .woocommerce-page p.stars span a:after,
    .widget_price_filter .price_slider_amount .button,
    .widget_shopping_cart .buttons .button {
      color: <?php echo $x_body_font_color; ?>;
    }

  <?php endif; ?>

<?php endif; ?>


/*
// Headings.
*/

<?php if ( $x_headings_font_color_enable == '1' ) : ?>

  .x-comment-author a,
  .comment-form-author label,
  .comment-form-email label,
  .comment-form-url label,
  .comment-form-rating label,
  .comment-form-comment label,
  .widget_calendar #wp-calendar caption,
  .widget_calendar #wp-calendar th,
  .x-accordion-heading .x-accordion-toggle,
  .x-nav-tabs > li > a:hover,
  .x-nav-tabs > .active > a,
  .x-nav-tabs > .active > a:hover {
    color: <?php echo $x_headings_font_color; ?>;
  }

  .widget_calendar #wp-calendar th {
    border-bottom-color: <?php echo $x_headings_font_color; ?>;
  }

  .pagination span.current,
  .x-portfolio-filters-menu,
  .widget_tag_cloud .tagcloud a,
  .h-feature-headline span i,
  .widget_price_filter .ui-slider .ui-slider-handle {
    background-color: <?php echo $x_headings_font_color; ?>;
  }

<?php endif; ?>



/* Responsive Styling
// ========================================================================== */

@media (max-width: 979px) {

  <?php if ( $x_navbar_positioning == 'fixed-top' && $x_layout_site == 'boxed' ) : ?>

    .x-navbar.x-navbar-fixed-top.x-container-fluid.max.width {
      left: 0;
      right: 0;
      width: 100%;
    }

  <?php endif; ?>

  <?php if ( $x_navbar_positioning == 'fixed-left' || $x_navbar_positioning == 'fixed-right' ) : ?>

    .x-navbar .x-navbar-inner > .x-container-fluid.width {
      width: <?php echo $x_layout_site_width . '%'; ?>;
    }

  <?php endif; ?>

  .x-widgetbar {
    left: 0;
    right: 0;
  }
}


<?php if ( is_home() && $x_renew_entry_icon_position == 'creative' && x_get_option( 'x_blog_style' ) == 'standard'  ) : ?>

  @media (min-width: 980px) {
    .x-full-width-active .entry-title:before,
    .x-content-sidebar-active .entry-title:before {
      position: absolute;
      width: 70px;
      height: 70px;
      margin-top: -<?php echo $x_renew_entry_icon_position_vertical . 'px'; ?>;
      margin-left: -<?php echo $x_renew_entry_icon_position_horizontal . '%'; ?>;
      font-size: 32px;
      font-size: 3.2rem;
      line-height: 70px;
      border-radius: 100em;
    }
  }

<?php endif; ?>