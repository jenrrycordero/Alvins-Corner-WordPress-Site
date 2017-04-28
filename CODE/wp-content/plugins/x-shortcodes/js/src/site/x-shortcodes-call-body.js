// =============================================================================
// JS/X-SHORTCODES-CALL-BODY.JS
// -----------------------------------------------------------------------------
// Function calls.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Window Load
//   02. Element Mappings
//       a. Google Maps
//       b. Skill Bar
//       c. Popovers and Tooltips
//       d. Legacy Popovers and Tooltips
//       e. MEJS
//       f. Responsive Slider
//       g  Content Column
//       h. Recent Posts
//       i. Responsive Lightbox
//       j. Content Band
//       k. Responsive Text
//       l. Counter
// =============================================================================

// Window Load
// =============================================================================

jQuery(window).load(function() {

  jQuery('.x-flexslider-featured-gallery').flexslider({
    controlNav   : false,
    selector     : '.x-slides > li',
    prevText     : '<i class="x-icon-chevron-left"></i>',
    nextText     : '<i class="x-icon-chevron-right"></i>',
    animation    : 'fade',
    easing       : 'easeInOutExpo',
    smoothHeight : true,
    slideshow    : false
  });

  jQuery('.x-flexslider-flickr').flexslider({
    controlNav   : false,
    selector     : '.x-slides > li',
    prevText     : '<i class="x-icon-chevron-left"></i>',
    nextText     : '<i class="x-icon-chevron-right"></i>',
    animation    : 'fade',
    easing       : 'easeInOutExpo',
    smoothHeight : true,
    slideshow    : false
  });

});



// Element Mappings
// =============================================================================

(function( $ ){

  //
  // Google maps.
  //

  xData.api.map( 'google_map_marker', function( element, params ) {
    console.log( 'Map Marker Added: [LAT:' + params.lat + ',LNG:' + params.lng + ']' );
  });

  xData.fn.setMarkers = function(googleMap, googleMapID) {

    var googleMarkers     = [];
    var googleInfoWindows = [];
    var mapContainerID    = $( '#' + googleMapID ).parent().attr('id');

    $( '#' + mapContainerID + ' .x-google-map-marker' ).each(function(i,e) {

      var params = $(e).data('x-params');

      googleMarkers[i] = new google.maps.Marker({
        map             : googleMap,
        position        : new google.maps.LatLng( params.lat, params.lng ),
        infoWindowIndex : i,
        icon            : params.image
      });

      googleInfoWindows[i] = new google.maps.InfoWindow({
        content  : params.markerInfo,
        maxWidth : 200
      });

      google.maps.event.addListener(googleMarkers[i], 'click', function() {
        if ( params.markerInfo !== '' ) {
          googleInfoWindows[i].open(googleMap, this);
        }
      });

    });

  };

  xData.api.map('google_map', function(element, params) {

    var $map            = $(element).find('.x-google-map-inner');
    var $mapID          = $map.attr('id');
    var $mapLat         = params.lat;
    var $mapLng         = params.lng;
    var $mapCoordinates = new google.maps.LatLng($mapLat, $mapLng);
    var $mapDrag        = params.drag;
    var $mapZoom        = parseInt(params.zoom);
    var $mapZoomControl = params.zoomControl;
    var $mapHue         = params.hue;

    var mapStyles = [
      {
        featureType : 'all',
        elementType : 'all',
        stylers     : [
          { hue : ( $mapHue ) ? $mapHue : null }
        ]
      },
      {
        featureType : 'water',
        elementType : 'all',
        stylers     : [
          { hue : ( $mapHue ) ? $mapHue : null },
          { saturation : 0  },
          { lightness  : 50 }
        ]
      },
      {
        featureType : 'poi',
        elementType : 'all',
        stylers     : [
          { visibility : 'off' }
        ]
      }
    ];

    var mapOptions = {
      scrollwheel            : false,
      draggable              : ( $mapDrag === true ),
      zoomControl            : ( $mapZoomControl === true ),
      disableDoubleClickZoom : false,
      disableDefaultUI       : true,
      zoom                   : $mapZoom,
      center                 : $mapCoordinates,
      mapTypeId              : google.maps.MapTypeId.ROADMAP
    };

    var googleStyledMap = new google.maps.StyledMapType(mapStyles, {name : "Styled Map"});
    var googleMap       = new google.maps.Map(document.getElementById($mapID), mapOptions);
    googleMap.mapTypes.set('map_style', googleStyledMap);
    googleMap.setMapTypeId('map_style');

    if ( xData.fn.setMarkers ) {
      xData.fn.setMarkers( googleMap, $mapID );
    }

  });


  //
  // Skill bar.
  //

  xData.api.map('skill_bar', function(element, params) {

    $(element).waypoint(function() {
      $(this).find('.bar').animate({ 'width' : params.percent }, 750, 'easeInOutExpo');
    }, { offset : '95%', triggerOnce : true });

  });


  //
  // Popovers and tooltips
  //

  xData.api.map('extra', function(element, params) {

    if ( params.type == 'tooltip' ) {

      var options = {
        animation : true,
        html      : false,
        delay     : {
          show : 0,
          hide : 0
        },
        placement: params.placement,

        trigger: params.trigger,
      };

      if (params.title && params.title !== '') {
        options.title = params.title;
      }

      $(element).tooltip(options);

    } else {

      var options = {
        animation : true,
        html      : false,
        delay     : {
          show : 0,
          hide : 0
        },
        placement: params.placement,
        trigger: params.trigger,
        content: params.content
      };

      if ( params.title && params.title !== '' ) {
        options.title = params.title;
      }

      $(element).popover(options);
    }

  });


  //
  // Legacy popovers and tooltips.
  //

  jQuery(document).ready(function($) {

    $('[data-toggle="tooltip"]').tooltip({
      animation : true,
      html      : false,
      delay     : {
        show : 0,
        hide : 0
      }
    });

    $('[data-toggle="popover"]').popover({
      animation : true,
      html      : false,
      delay     : {
        show : 0,
        hide : 0
      }
    });

  });


  //
  // MEJS.
  //

  xData.api.map('x_mejs', function(element, params){

    //
    // Add Silverlight types (only once).
    //

    $.each([ 'video/x-ms-wmv', 'audio/x-ms-wma' ], function( i, type ) {
      if ( ! $.inArray(type, mejs.plugins.silverlight[0].types ) ) {
        mejs.plugins.silverlight[0].types.push(type);
      }
    });

    $(element).find('.x-mejs').mediaelementplayer({

      pluginPath         : _wpmejsSettings.pluginPath,
      startVolume        : 1,
      features           : ['playpause', 'progress'],
      audioWidth         : '100%',
      audioHeight        : '32',
      videoWidth         : '100%',
      videoHeight        : '100%',
      pauseOtherPlayers  : true,
      alwaysShowControls : true,

      success : function( mejs ) {

        var play        = true;
        var $container  = $(element).find('.mejs-container');
        var $controls   = $(element).find('.mejs-controls');
        var controlsOn  = function() { $controls.stop().animate({ opacity : 1 }, 150); };
        var controlsOff = function() { $controls.stop().animate({ opacity : 0 }, 150); };


        //
        // Autoplay, muting, and looping.
        //

        mejs.addEventListener( 'canplay', function() {
          if ( mejs.attributes.autoplay && play ) {
            mejs.play();
            play = false;
          }
          if ( mejs.attributes.muted ) {
            mejs.setMuted(true);
          }
        });

        mejs.addEventListener( 'ended', function() {
          if ( mejs.attributes.loop )
            mejs.play();
        });


        //
        // Video only.
        //

        if ( $container.hasClass('mejs-video') ) {
          mejs.addEventListener( 'playing', function() {
            $container.hover( controlsOn, controlsOff );
          });
          mejs.addEventListener( 'pause', function() {
            $container.off( 'mouseenter mouseleave' );
            controlsOn();
          });
        }

      },

      error : function() { console.log( 'MEJS media error.' ); }

    });

  });


  //
  // Responsive slider.
  //

  xData.api.map('slider', function(element, params) {

    jQuery(window).load(function() {
      jQuery(element).flexslider({
        selector       : '.x-slides > li',
        prevText       : '<i class=\"x-icon-chevron-left\"></i>',
        nextText       : '<i class=\"x-icon-chevron-right\"></i>',
        animation      : params.animation,
        controlNav     : params.controlNav,
        directionNav   : params.prevNextNav,
        slideshowSpeed : params.slideTime,
        animationSpeed : params.slideSpeed,
        slideshow      : params.slideshow,
        randomize      : params.random,
        pauseOnHover   : true,
        useCSS         : true,
        touch          : true,
        video          : true,
        smoothHeight   : true,
        easing         : 'easeInOutExpo'
      });
    });

  });


  //
  // Content columns.
  //

  xData.api.map('column', function(element, params) {

    if ( params.fade ) {

      $(element).waypoint(function() {

        var options = { opacity: '1' };

        if      ( params.animation === 'in-from-top' )    { options.top = '0';    }
        else if ( params.animation === 'in-from-left' )   { options.left = '0';   }
        else if ( params.animation === 'in-from-right' )  { options.right = '0';  }
        else if ( params.animation === 'in-from-bottom' ) { options.bottom = '0'; }

        $(this).animate( options, 750, 'easeOutExpo');

      }, { offset : '65%', triggerOnce : true });
    }

  });


  //
  // Recent posts.
  //

  xData.api.map('recent_posts', function(element, params) {

    if ( params.fade ) {

      $(element).waypoint(function() {

        $(this).find('a').each(function(i,item) {
          $(item).delay(i * 90).animate({ 'opacity' : '1' }, 750, 'easeOutExpo');
        });

        setTimeout(function() {
          $(this).addClass('complete');
        }, ($(this).find('a').length * 90) + 400);

      }, { offset : '75%', triggerOnce : true });
    }

  });


  //
  // Responsive lightbox.
  //

  xData.api.map('lightbox', function(element, params) {

    var options = {
      skin: 'light',
      overlay: {
        opacity: params.opacity,
        blur: true
      },
      styles: {
        prevScale: params.prevScale,
        prevOpacity: params.prevOpacity,
        nextScale: params.nextScale,
        nextOpacity: params.nextOpacity
      },
      path: params.orientation,
      controls: {
        thumbnail: params.thumbnails
      }
    };

    if ( params.deeplink ) {
      options.linkId = 'gallery-image';
    }

    jQuery(params.selector).iLightBox(options);

  });


  //
  // Content band.
  //

  xData.api.map('content_band', function(element, params) {
    jQuery(window).load(function() {

      if ( params.parallax ) {
        if ( Modernizr.touch ) {
          $(element).css('background-attachment', 'scroll');
        } else {
          if ( params.type == 'image'   ) speed = 0.1;
          if ( params.type == 'pattern' ) speed = 0.3;
          if ( speed ) $(element).parallaxContentBand('50%', speed );
        }
      }

      if ( params.type == 'video' ) {
        var BV = new jQuery.BigVideo();
        BV.init();
        if ( Modernizr.touch ) {
          BV.show(params.poster);
        } else {
          BV.show(params.video, { ambient : true });
        }
      }

    });
  });


  //
  // Responsive text.
  //

  xData.api.map('responsive_text', function(element, params) {

    var options = {};

    if (params.minFontSize !== '') {
      options.minFontSize = params.minFontSize;
    }

    if (params.maxFontSize !== '') {
      options.maxFontSize = params.maxFontSize;
    }

    $(params.selector).fitText( params.compression, options );

  });


  //
  // Counter.
  //

  xData.api.map('counter',function(element, params){

    $(element).waypoint(function() {
      $(this).find('.number').animateNumber({ 'number' : params.numEnd }, params.numSpeed);
    }, { offset : '85%', triggerOnce : true });

  });

})(jQuery);