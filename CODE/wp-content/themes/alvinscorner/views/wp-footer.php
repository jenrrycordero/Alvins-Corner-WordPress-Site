<?php

// =============================================================================
// VIEWS/INTEGRITY/WP-FOOTER.PHP
// -----------------------------------------------------------------------------
// Footer output for Integrity.
// =============================================================================

?>

  <?php x_get_view( 'global', '_header', 'widget-areas' ); ?>
  <?php x_get_view( 'global', '_footer', 'scroll-top' ); ?>
  <?php x_get_view( 'global', '_footer', 'widget-areas' ); ?>


<?php /* if ( x_get_option( 'x_footer_bottom_display' ) == 1 ) : */  /*I dont know why this if isnt working properly */ ?>

  <footer class="x-colophon bottom" role="contentinfo">
    <div class="x-container-fluid max width">
      <div class="footer-left">
        <p><?php echo date("Y"); ?> <span class='copyright-symbol'>&copy;</span> Alvin's Corner at Penny Lane LLC | Powered by <a href='http://www.razzinteractive.com' target='_blank'>Razz Interactive</a> and <a href='http://www.gotomyapartment.com/' target='_blank'>GoToMyApartment</a></p>
      </div>
      <div class="footer-right">
        <p><img src='/wp-content/uploads/2017/03/Alvins-Corner-footer-iconsOK.png'></p>
        <?php /* echo wp_nav_menu( array( 'fallback_cb' => '', 'menu' => 'footer-menu','echo' => false,'menu_class' => 'pet-friendly-link'  ) ); */?>

      </div>

    </div>
  </footer>

<?php /* endif; */?>


  <?php if ( x_get_option( 'x_footer_bottom_display' ) == 1 ) : ?>

    <footer class="x-colophon bottom" role="contentinfo">
      <div class="x-container-fluid max width">
        <div class="footer-left">
          <p class="copyright">
            <?php the_time('Y'); ?>
          </p>


        </div>
        <div class="footer-right">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/footer-logos.png" />
        </div>

      </div>
    </footer>

  <?php endif; ?>

<?php x_get_view( 'global', '_footer' ); ?>