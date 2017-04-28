<?php

// =============================================================================
// VIEWS/INTEGRITY/WP-HEADER.PHP
// -----------------------------------------------------------------------------
// Header output for Integrity.
// =============================================================================

?>

<?php x_get_child_view( 'global', 'header' ); ?>

  <?php x_get_view( 'global', '_slider-revolution-above' ); ?>

  <header class="<?php x_masthead_class(); ?>" role="banner">
    <?php x_get_child_view( 'global', 'topbar' ); ?>
    <?php x_get_child_view( 'global', 'navbar' ); ?>
    <?php x_get_view( 'integrity', '_breadcrumbs' ); ?>
  </header>

  <?php x_get_view( 'global', '_slider-revolution-below' ); ?>
  <?php x_get_view( 'integrity', '_landmark-header' ); ?>