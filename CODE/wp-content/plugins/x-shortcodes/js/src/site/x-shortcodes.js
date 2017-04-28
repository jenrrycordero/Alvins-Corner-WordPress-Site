// =============================================================================
// JS/X-SHORTCODES.JS
// -----------------------------------------------------------------------------
// Custom shortcode scripts for the X Shortcodes plugin.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Custom Shortcode Scripts
//   02. Data Attribute API
// =============================================================================

// Custom Shortcode Scripts
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Parallax effect.
  //

  var windowObj    = $(window);
  var windowHeight = windowObj.height();
  var thisObj      = $(this);

  windowObj.resize(function() {
    windowHeight = windowObj.height();
  });

  $.fn.parallaxContentBand = function(xAxis, parallaxSpeed) {
    var thisObj = $(this);
    var contentBandFirstTop;

    thisObj.each(function(){
      contentBandFirstTop = thisObj.offset().top;
    });

    windowObj.resize(function() {
      thisObj.each(function() {
        contentBandFirstTop = thisObj.offset().top;
      });
    });

    function windowUpdate() {
      var windowPosition = windowObj.scrollTop();

      thisObj.each(function() {
        var contentBandOffsetTop = thisObj.offset().top;
        var contentBandHeight    = thisObj.outerHeight();

        if (contentBandOffsetTop + contentBandHeight < windowPosition || contentBandOffsetTop > windowPosition + windowHeight) {
          return;
        }

        thisObj.css('background-position', xAxis + ' ' + Math.floor((contentBandFirstTop - windowPosition) * parallaxSpeed) + 'px');
      });
    }

    windowObj.bind('scroll', windowUpdate).resize(windowUpdate);
    windowUpdate();
  };


  //
  // Ensure accordion collapse class.
  //

  $('.x-accordion-toggle[data-parent]').click(function() {
    $(this).closest('.x-accordion').find('.x-accordion-toggle:not(.collapsed)').addClass('collapsed');
  });



});



// Data Attribute API
// =============================================================================

window.xData = window.xData || {};

(function( exports, $ ){
  var base, api;

  //
  // API to map a callback to an element.
  //

  api = {

    map: function( elementName, callback ) {
      base.mappedFunctions[elementName] = callback;
    },

    process: function() {
      $(base).trigger('ready');
    }

  };

  base = {

    mappedFunctions: [],

    init: function(){
      $(this).on('ready', this.processElements);
    },

    processElements: function(){

      // Find X Elements
      $elements = $('[data-x-element]').each(function(index, element){

        // Filter out anything already processed
        if( ! $(element).data('x-initialized') ) {

          // Grab the parameters passed along from PHP
          var params = $(element).data('x-params') || {};

          // Locate the mapped callback for this element
          callback = base.lookupCallback( $(element).data('x-element') );

          // Execute Callback
          callback( element, params);

          // Mark this element as initialized, preventing it from being processed in the future.
          $(element).data('x-initialized', true );

        }

      });

    },

    lookupCallback: function( elementName ){
      return this.mappedFunctions[elementName] || function() { console.log('X Element not found.'); };
    }
  };

  base.init();


  //
  // Process all elements on $.ready.
  //
  // Call to reprocess page: xData.api.process();
  //

  $(function($){
    api.process();
  });

  exports.base = base;
  exports.api  = api;
  exports.fn   = {};

})( xData, jQuery );