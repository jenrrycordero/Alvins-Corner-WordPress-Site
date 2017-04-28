<?php

// =============================================================================
// FUNCTIONS/ENQUEUE/STYLES.PHP
// -----------------------------------------------------------------------------
// Plugin styles.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Enqueue Site Styles
// =============================================================================

// Enqueue Site Styles
// =============================================================================

function x_shortcodes_enqueue_site_styles() {

  $stack  = ( x_get_option( 'x_stack' )            ) ? x_get_option( 'x_stack' )            : 'integrity';
  $design = ( x_get_option( 'x_integrity_design' ) ) ? x_get_option( 'x_integrity_design' ) : 'light';

  if ( $stack == 'integrity' && $design == 'light' ) {
    $ext = '-light';
  } elseif ( $stack == 'integrity' && $design == 'dark' ) {
    $ext = '-dark';
  } else {
    $ext = '';
  }

  wp_enqueue_style( 'x-shortcodes', X_SHORTCODES_URL . '/css/' . $stack . $ext . '.css', NULL, X_SHORTCODES_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'x_shortcodes_enqueue_site_styles' );