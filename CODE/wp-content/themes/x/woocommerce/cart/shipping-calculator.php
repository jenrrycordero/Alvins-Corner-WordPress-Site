<?php

// =============================================================================
// WOOCOMMERCE/CART/SHIPPING-CALCULATOR.PHP
// -----------------------------------------------------------------------------
// @version 2.0.8
// =============================================================================

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

if ( get_option( 'woocommerce_enable_shipping_calc' ) === 'no' || ! WC()->cart->needs_shipping() )
  return;
?>

<?php do_action( 'woocommerce_before_shipping_calculator' ); ?>

<form class="shipping_calculator" action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">

  <h2><?php _e( 'Calculate Shipping', '__x__' ); ?></h2>

  <section class="shipping-calculator-fields">

    <p class="form-row form-row-wide">
      <select name="calc_shipping_country" id="calc_shipping_country" class="country_to_state" rel="calc_shipping_state">
        <option value=""><?php _e( 'Select a country&hellip;', '__x__' ); ?></option>
        <?php
          foreach( WC()->countries->get_shipping_countries() as $key => $value )
            echo '<option value="' . esc_attr( $key ) . '"' . selected( WC()->customer->get_shipping_country(), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
        ?>
      </select>
    </p>

    <p class="form-row form-row-wide">
      <?php
        $current_cc = WC()->customer->get_shipping_country();
        $current_r  = WC()->customer->get_shipping_state();
        $states     = WC()->countries->get_states( $current_cc );

        // Hidden Input
        if ( is_array( $states ) && empty( $states ) ) {

          ?><input type="hidden" name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php _e( 'State / county', '__x__' ); ?>" /><?php

        // Dropdown Input
        } elseif ( is_array( $states ) ) {

          ?><span>
            <select name="calc_shipping_state" id="calc_shipping_state" placeholder="<?php _e( 'State / county', '__x__' ); ?>">
              <option value=""><?php _e( 'Select a state&hellip;', '__x__' ); ?></option>
              <?php
                foreach ( $states as $ckey => $cvalue )
                  echo '<option value="' . esc_attr( $ckey ) . '" ' . selected( $current_r, $ckey, false ) . '>' . __( esc_html( $cvalue ), '__x__' ) .'</option>';
              ?>
            </select>
          </span><?php

        // Standard Input
        } else {

          ?><input type="text" class="input-text" value="<?php echo esc_attr( $current_r ); ?>" placeholder="<?php _e( 'State / county', '__x__' ); ?>" name="calc_shipping_state" id="calc_shipping_state" /><?php

        }
      ?>
    </p>

    <?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_city', false ) ) : ?>
      <p class="form-row form-row-wide">
        <input type="text" class="input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_city() ); ?>" placeholder="<?php _e( 'City', '__x__' ); ?>" name="calc_shipping_city" id="calc_shipping_city" />
      </p>
    <?php endif; ?>

    <?php if ( apply_filters( 'woocommerce_shipping_calculator_enable_postcode', true ) ) : ?>
      <p class="form-row form-row-wide">
        <input type="text" class="input-text" value="<?php echo esc_attr( WC()->customer->get_shipping_postcode() ); ?>" placeholder="<?php _e( 'Postcode / Zip', '__x__' ); ?>" name="calc_shipping_postcode" id="calc_shipping_postcode" />
      </p>
    <?php endif; ?>

    <p><button type="submit" name="calc_shipping" value="1" class="button"><?php _e( 'Update Totals', '__x__' ); ?></button></p>

    <?php wp_nonce_field( 'woocommerce-cart' ); ?>

  </section>
</form>

<?php do_action( 'woocommerce_after_shipping_calculator' ); ?>