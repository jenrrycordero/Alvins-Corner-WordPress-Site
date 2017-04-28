// =============================================================================
// JS/X-SCRIPTS-HEAD.JS
// -----------------------------------------------------------------------------
// Theme specific functions.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Global Functions
// =============================================================================

// Global Functions
// =============================================================================

jQuery(document).ready(function($) {

  //
  // Prevent default behavior of various toggles.
  //

  $('.x-btn-navbar, .x-btn-navbar-search, .x-btn-widgetbar').click(function(e) {
    e.preventDefault();
  });


  //
  // Scroll to the bottom of the slider.
  //

  $('.x-slider-container.above .x-slider-scroll-bottom').click(function(e) {
    e.preventDefault();
    $('html, body').animate({
      scrollTop: $('.x-slider-container.above').outerHeight()
    }, 850, 'easeInOutExpo');
  });

  $('.x-slider-container.below .x-slider-scroll-bottom').click(function(e) {
    e.preventDefault();
    var $mastheadHeight       = $('.masthead').outerHeight();
    var $navbarFixedTopHeight = $('.x-navbar-fixed-top-active .x-navbar').outerHeight();
    var $sliderAboveHeight    = $('.x-slider-container.above').outerHeight();
    var $sliderBelowHeight    = $('.x-slider-container.below').outerHeight();
    var $heightSum            = $mastheadHeight + $sliderAboveHeight + $sliderBelowHeight - $navbarFixedTopHeight;
    $('html, body').animate({
      scrollTop: $heightSum
    }, 850, 'easeInOutExpo');
  });


  //
  // Apply appropriate classes for the fixed-top navbar.
  //

  var $window     = $(window);
  var $this       = $(this);
  var $body       = $('body');
  var $navbar     = $('.x-navbar');
  var $navbarWrap = $('.x-navbar-fixed-top-active .x-navbar-wrap');

  if ( ! $body.hasClass('page-template-template-blank-3-php') && ! $body.hasClass('page-template-template-blank-6-php') && ! $body.hasClass('page-template-template-blank-7-php') && ! $body.hasClass('page-template-template-blank-8-php') ) {
    if ( $body.hasClass('x-boxed-layout-active') && $body.hasClass('x-navbar-fixed-top-active') && $body.hasClass('admin-bar') ) {
      $window.scroll(function() {
        var $adminbarHeight = $('#wpadminbar').outerHeight();
        var $menuTop        = $navbarWrap.offset().top - $adminbarHeight;
        var $current        = $this.scrollTop();
        if ($current >= $menuTop) {
          $navbar.addClass('x-navbar-fixed-top x-container-fluid max width');
        } else {
          $navbar.removeClass('x-navbar-fixed-top x-container-fluid max width');
        }
      });
    } else if ( $body.hasClass('x-navbar-fixed-top-active') && $body.hasClass('admin-bar') ) {
      $window.scroll(function() {
        var $adminbarHeight = $('#wpadminbar').outerHeight();
        var $menuTop        = $navbarWrap.offset().top - $adminbarHeight;
        var $current        = $this.scrollTop();
        if ($current >= $menuTop) {
          $navbar.addClass('x-navbar-fixed-top');
        } else {
          $navbar.removeClass('x-navbar-fixed-top');
        }
      });
    } else if ( $body.hasClass('x-boxed-layout-active') && $body.hasClass('x-navbar-fixed-top-active') ) {
      $window.scroll(function() {
        var $menuTop = $navbarWrap.offset().top;
        var $current = $this.scrollTop();
        if ($current >= $menuTop) {
          $navbar.addClass('x-navbar-fixed-top x-container-fluid max width');
        } else {
          $navbar.removeClass('x-navbar-fixed-top x-container-fluid max width');
        }
      });
    } else if ( $body.hasClass('x-navbar-fixed-top-active') ) {
      $window.scroll(function() {
        var $menuTop = $navbarWrap.offset().top;
        var $current = $this.scrollTop();
        if ($current >= $menuTop) {
          $navbar.addClass('x-navbar-fixed-top');
        } else {
          $navbar.removeClass('x-navbar-fixed-top');
        }
      });
    }
  }


  //
  // Desktop dropdown functionality.
  //

  var $desktopMenu   = $('.desktop .x-nav');
  var desktopTargets = 'li.menu-item-has-children';
  var mActiveClass   = 'x-active';
  var timer          = {};

  function over(e) {
    clearTimeout(timer.id);
    var $li = $(e.target).closest('li');
    if ( $li.hasClass('menu-item-has-children') ) {
      $li.addClass(mActiveClass).siblings(desktopTargets).removeClass(mActiveClass);
    }
  }

  function out(e) {
    clearTimeout(timer.id);
    var $this = $(this);
    timer.id = setTimeout( function() {
      $this.closest('ul').find('.' + mActiveClass).removeClass(mActiveClass);
    }, 500 );
  }

  $desktopMenu.hoverIntent(over, out, desktopTargets);
  $desktopMenu.on('focusin', desktopTargets, over);
  $desktopMenu.on('focusout', desktopTargets, out);


  //
  // Mobile dropdown functionality.
  //

  var $mobileMenu    = $('.mobile .x-nav');
  var $mobileTargets = $mobileMenu.find('li.menu-item-has-children > a');
  var $mobileSubs    = $mobileMenu.find('.sub-menu');

  $mobileTargets.each(function(i) {
    $(this).append('<div class="x-sub-toggle" data-toggle="collapse" data-target=".sub-menu.sm-' + i + '"><span><i class="x-icon x-icon-angle-double-down"></i></span></div>');
  });

  $mobileSubs.each(function(i) {
    $(this).addClass('sm-' + i + ' collapse');
  });

  $('.x-sub-toggle').on('click', function(e) {
    e.preventDefault();
    $(this).toggleClass(mActiveClass).closest('li').toggleClass(mActiveClass);
  });


  //
  // YouTube z-index fix.
  //

  $('iframe[src*="youtube.com"]').each(function() {
    var url = $(this).attr('src');
    if ($(this).attr('src').indexOf('?') > 0) {
      $(this).attr({
        'src'   : url + '&wmode=transparent',
        'wmode' : 'Opaque'
      });
    } else {
      $(this).attr({
        'src'   : url + '?wmode=transparent',
        'wmode' : 'Opaque'
      });
    }
  });


  //
  // Navbar search.
  //

  var $trigger  = $('.x-btn-navbar-search');
  var $formWrap = $('.x-searchform-overlay');
  var $form     = $formWrap.find('.form-search');
  var $input    = $formWrap.find('.search-query');
  var escKey    = 27;

  function clearSearch() {
    $formWrap.toggleClass('in');
    setTimeout( function() { $input.val(''); }, 350 );
  }

  $trigger.click(function(e) {
    e.preventDefault();
    $formWrap.toggleClass('in');
    $input.focus();
  });

  $formWrap.click(function(e) {
    if ( ! $(e.target).hasClass('search-query') ) {
      clearSearch();
    }
  });

  $(document).keydown(function(e) {
    if ( e.which === escKey ) {
      if ( $formWrap.hasClass('in') ) {
        clearSearch();
      }
    }
  });


  //
  // Resize isotope container if gallery navigation is clicked or if an arrow
  // key is pressed to ensure that elements are spaced out properly.
  //

  $('body').on('click', '.x-iso-container .flex-direction-nav a', function() {
    setTimeout(function() { $window.smartresize(); }, 750);
  });

  $('body.x-masonry-active').on('keyup', function(e) {
    if (e.which >= 37 && e.which <= 40) {
      setTimeout(function() { $window.smartresize(); }, 750);
    }
  });


  //
  // scrollBottom function.
  //

  $.fn.scrollBottom = function() {
    return $(document).height() - this.scrollTop() - this.height();
  };

});