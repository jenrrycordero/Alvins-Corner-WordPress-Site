<?php

// =============================================================================
// VC_TEMPLATES/VC_COLUMN.PHP
// -----------------------------------------------------------------------------
// Make [vc_column] behave like the [column] shortcode.
// =============================================================================

?>

<?php

extract( shortcode_atts( array(
  'id'                    => '',
  'class'                 => '',
  'style'                 => '',
  'width'                 => '',
  'fade'                  => '',
  'fade_animation'        => '',
  'fade_animation_offset' => ''
), $atts ) );

$id    = ( $id    != '' ) ? 'id="' . esc_attr( $id ) . '"' : '';
$class = ( $class != '' ) ? 'x-column vc ' . esc_attr( $class ) : 'x-column vc';
$style = ( $style != '' ) ? $style : '';
switch ( $width ) {
  case '1/1' :
    $width = ' x-1-1';
    break;
  case '1/2' :
    $width = ' x-1-2';
    break;
  case '1/3' :
    $width = ' x-1-3';
    break;
  case '2/3' :
    $width = ' x-2-3';
    break;
  case '1/4' :
    $width = ' x-1-4';
    break;
  case '3/4' :
    $width = ' x-3-4';
    break;
  case '1/6' :
    $width = ' x-1-6';
    break;
  case '5/6' :
    $width = ' x-5-6';
    break;
}
$fade_animation = ( $fade_animation != '' ) ? $fade_animation : 'in';

if ( $fade == 'true' ) {
  $fade = 'data-fade="true" data-fade-animation="' . $fade_animation . '"';
  switch ( $fade_animation ) {
    case 'in' :
      $fade_animation_offset = '';
      break;
    case 'in-from-top' :
      $fade_animation_offset = ' top: -' . $fade_animation_offset . ';';
      break;
    case 'in-from-left' :
      $fade_animation_offset = ' left: -' . $fade_animation_offset . ';';
      break;
    case 'in-from-right' :
      $fade_animation_offset = ' right: -' . $fade_animation_offset . ';';
      break;
    case 'in-from-bottom' :
      $fade_animation_offset = ' bottom: -' . $fade_animation_offset . ';';
      break;
  }
} else {
  $fade                  = '';
  $fade_animation_offset = '';
}

$output = "<div {$id} class=\"{$class}{$width}\" style=\"{$style}{$fade_animation_offset}\" {$fade}>" . do_shortcode( $content ) . "</div>";

echo $output;

?>