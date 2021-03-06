<?php

// =============================================================================
// VIEWS/INTEGRITY/WP-404.PHP
// -----------------------------------------------------------------------------
// Handles output for when a page does not exist.
// =============================================================================

?>

<?php get_header(); ?>

  <div class="x-container-fluid max width offset cf">
    <div class="x-main full" role="main">
      <div class="entry-wrap entry-404">

        <?php x_get_view( 'global', '_content-404' ); ?>

      </div>
    </div>
  </div>

<?php get_footer(); ?>