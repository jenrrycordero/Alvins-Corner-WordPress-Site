<?php

// =============================================================================
// FUNCTIONS/GLOBAL/ADMIN/ADDONS/DEMO/STACK-XCS.PHP
// -----------------------------------------------------------------------------
// Stack specific Customizer setting updates.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Integrity 3
//   02. Integrity 5
//   03. Integrity 6
//   04. Integrity 8
//   05. Integrity 9
//   06. Integrity 10
//   07. Renew 1
//   08. Renew 3
//   09. Renew 5
//   10. Renew 6
//   11. Renew 7
//   12. Renew 9
//   13. Icon 5
//   14. Icon 7
//   15. Icon 8
//   16. Ethos 1
//   17. Ethos 2
// =============================================================================

// Integrity 3
// =============================================================================

if ( $demo == 'integrity-3' ) {

  update_option( 'x_design_bg_image_full', 'http://placehold.it/1500x1000' );

}



// Integrity 5
// =============================================================================

if ( $demo == 'integrity-5' ) {

  update_option( 'x_design_bg_image_pattern', 'http://placehold.it/50x50' );
  update_option( 'x_logo', 'http://placehold.it/115x55' );

}



// Integrity 6
// =============================================================================

if ( $demo == 'integrity-6' ) {

  update_option( 'x_design_bg_image_full', 'http://placehold.it/1500x1000' );

}



// Integrity 8
// =============================================================================

if ( $demo == 'integrity-8' ) {

  update_option( 'x_design_bg_image_full', 'http://placehold.it/1500x1000' );

}



// Integrity 9
// =============================================================================

if ( $demo == 'integrity-9' ) {

  update_option( 'x_design_bg_image_pattern', 'http://placehold.it/50x50' );

}



// Integrity 10
// =============================================================================

if ( $demo == 'integrity-10' ) {

  update_option( 'x_design_bg_image_pattern', 'http://placehold.it/50x50' );
  update_option( 'x_design_bg_image_full', 'http://placehold.it/1500x1000' );

}



// Renew 1
// =============================================================================

if ( $demo == 'renew-1' ) {

  update_option( 'x_design_bg_image_full', 'http://placehold.it/1500x1000' );

}



// Renew 3
// =============================================================================

if ( $demo == 'renew-3' ) {

  update_option( 'x_design_bg_image_full', 'http://placehold.it/1500x1000' );

}



// Renew 5
// =============================================================================

if ( $demo == 'renew-5' ) {

  update_option( 'x_logo', 'http://placehold.it/300x200' );
  update_option( 'x_logo_width', 300 );

}



// Renew 6
// =============================================================================

if ( $demo == 'renew-6' ) {

  update_option( 'x_logo', 'http://placehold.it/450x150' );
  update_option( 'x_logo_width', 450 );

}



// Renew 7
// =============================================================================

if ( $demo == 'renew-7' ) {

  update_option( 'x_design_bg_image_full', 'http://placehold.it/1500x1000' );

}



// Renew 9
// =============================================================================

if ( $demo == 'renew-9' ) {

  update_option( 'x_design_bg_image_pattern', 'http://placehold.it/50x50' );

}



// Icon 5
// =============================================================================

if ( $demo == 'icon-5' ) {

  update_option( 'x_design_bg_image_full', 'http://placehold.it/1500x1000' );

}



// Icon 7
// =============================================================================

if ( $demo == 'icon-7' ) {

  update_option( 'x_design_bg_image_pattern', 'http://placehold.it/50x50' );

}



// Icon 8
// =============================================================================

if ( $demo == 'icon-8' ) {

  update_option( 'x_design_bg_image_full', 'http://placehold.it/1500x1000' );

}



// Ethos 1
// =============================================================================

if ( $demo == 'ethos-1' ) {

  $term_1 = term_exists( 'Standard', 'category' );
  $term_2 = term_exists( 'Audio', 'category' );
  $term_3 = term_exists( 'Video', 'category' );

  $term_1_exists = $term_1 != 0 && $term_1 != NULL;
  $term_2_exists = $term_2 != 0 && $term_2 != NULL;
  $term_3_exists = $term_3 != 0 && $term_3 != NULL;

  if ( $term_1_exists && $term_2_exists && $term_2_exists ) {

    $input = $term_1['term_id'] . ', ' . $term_2['term_id'] . ', ' . $term_3['term_id'];

    update_option( 'x_ethos_filterable_index_categories', $input );

  } else {

    update_option( 'x_ethos_filterable_index_enable', '0' );

  }

  update_option( 'x_design_bg_image_pattern', 'http://placehold.it/50x50' );
  update_option( 'x_ethos_post_carousel_display', 'random' );
  update_option( 'x_ethos_post_slider_blog_display', 'random' );

}



// Ethos 2
// =============================================================================

if ( $demo == 'ethos-2' ) {

  update_option( 'x_design_bg_image_pattern', 'http://placehold.it/50x50' );

}