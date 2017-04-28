jQuery(document).ready(function ($) {

    var regex = new RegExp(/#available$/);
    if (regex.test(window.location.href)) {
        var $isotope_container = $('#razz-model-wrapper');
        $isotope_container.isotope({
            filter: '.Available'
        });
        searchFilter(true);
    }
    searchFilter();

    function searchFilter(flag) {
        var $available = '';

        $('.filter-list a').each(function () {
            if ($(this).data('filter') == '.Available') {
                if (flag)  $(this).addClass('selected');
                $available = $(this).remove()
            }
        });
        if($available.length) {
             $available.insertAfter($('.filter-list a:nth-child(1)'))
        }
       
    }


    $('<input type="text" value="Move In Date" class="move-in-placeholder" style="color: #555; position: absolute; width: 135px; border: none; height: 30px; margin-top: 5px; margin-left: 5px;">').prependTo('.date-movein');
    $('.move-in-placeholder').bind("click", function () {
        $(this).remove();
        $('input[name="date-movein"]').focus();
    });
    $('#floorplans-gallery .gallery').children('.gallery-item').each(function () {
        $(this).find('a').attr("title", $(this).find('a').children('img').attr("alt"));
        $(this).find('a').click(function (e) {

            e.preventDefault();

            $.magnificPopup.close();

            $('#floorplans-gallery .gallery').magnificPopup({
                delegate: '.gallery-item .gallery-icon a',
                type: 'image',
                gallery: {
                    enabled: true
                },
                callbacks: {
                    open: function () {
                        $('.mfp-close').css("cursor", "pointer");
                        $('.mfp-close').bind("click", function (e) {
                            e.preventDefault();
                            $.magnificPopup.close();
                        });
                    },
                    close: function () {
                        console.clear();
                        $('#view-floorplans').trigger('click');
                    }
                }
            });

            $('#floorplans-gallery .gallery').magnificPopup('open');

        });

    });


    $('#view-floorplans').click(function (e) {
        e.preventDefault();
        $('#floorplans-gallery').addClass('mfp-inline');
        $.magnificPopup.open({
            items: {
                src: '#floorplans-gallery',
                type: 'inline'
            },
            callbacks: {
                open: function () {
                    $('.mfp-arrow').remove();
                    $('.mfp-close').prependTo('#floorplans-gallery');
                    $('#floorplans-gallery .gallery').magnificPopup.instance('close');

                },
                close: function () {
                    console.clear();
                    $('#floorplans-gallery')
                        .removeAttr('style');
                }
            }
        });
    });


    /* code to make fixed the menu bar when it reach the top.*/
    setHeaderStyle();

    jQuery(window).scroll(function () {
        setHeaderStyle();
    });

    // call to the block for the material design
    material_design_initialice();

    /*******       code for the dropdown menus */
    jQuery("select option").each(function () {
        jQuery(this).data("description", jQuery(this).text());
    });

    jQuery("select").each(function () {
        var name = jQuery(this).attr("name");
        var parent = jQuery(this).parent();

        jQuery(this).ddslick({
            width: "100%"
            /*
             data: ddData,
             imagePosition: "left",
             selectText: "Select your favorite social network",
             onSelected: function (data) {
             console.log(data);
             }
             */
        });

        jQuery(parent).find("input.dd-selected-value").attr("name", name);
    });
    /******        end of */


        //to create an easy transition to the original image of the header on home.
    var img = jQuery("#img-replace-header").attr("src");
    var tmpImg = new Image();
    tmpImg.src = img;

    tmpImg.onload = function () {
        jQuery('.x-logobar-inner').css({
            backgroundImage: 'url("' + img + '")'
        });
    };


    /** code for the style and functionality */
    set_functionality_for_the_gallery();

    /** code for the ligthbox effect */
    set_ligth_box_listenner();


    //set the rules for the animation hover effect
    var parent_container = '.x-navbar-wrap .x-container-fluid';

    function moveMenuTop($ele, speed) {
        var $a = $ele.find('a');
        var $bar = $ele.closest(parent_container).find('#head_menu_top');
        //var padding = ($ele.width() - $a.width()) / 2;
        var padding = parseInt($ele.css("paddingLeft").replace("px", ''));
        var mleft = parseInt($ele.css("marginLeft").replace("px", ''));
        var mleft_a = parseInt($a.css("marginLeft").replace("px", ''));

        $bar.stop().animate({
            width: $a.width(),
            left: $ele.position().left + padding + mleft + mleft_a
        }, speed);
    }

    $(parent_container).each(function () {
        var $this = $(this);

        var $cur = $this.find('.current-menu-item');
        var $bar = $this.find('#head_menu_top');

        if ($cur.length > 0) {
            moveMenuTop($cur, 1);
            $(window).on('resize', function () {
                moveMenuTop($cur, 350);
            });
        }

        $this.find('ul#menu-main > li').on('mouseover', function () {
            if ($bar.width() == 0)
                $bar.css('left', $(this).position().left + $(this).width() / 2 - $bar.width() / 2);
            moveMenuTop($(this), 200);
        });

        $this.find('ul#menu-main > li').on('mouseout', function () {
            if ($cur.length > 0)
                moveMenuTop($cur, 350);
            else {
                $bar.stop().animate({
                    width: 0,
                    left: $bar.position().left + ($bar.width() / 2)
                }, 200);
            }
        });
    });


    $(window).scroll(function () {

        if ($(".x-btn-navbar").is(":visible") &&
            $(window).scrollTop() >= $(".home .x-logobar").height()) {
            $("body").addClass("scrolled");
        }
        else {
            $("body.scrolled").removeClass("scrolled");
        }
    });

});


/** function to set the header fixed */
function setHeaderStyle() {
    if (jQuery(window).scrollTop() > jQuery(".x-navbar-wrap").offset().top) {
        jQuery(".x-navbar-wrap").addClass('menu-fixed');
    }
    else {
        jQuery('.menu-fixed').removeClass('menu-fixed');

    }
}


/**
 * this block below is for the code to make it look like a material design from google, to the forms
 */
function material_design_initialice() {


    jQuery.datepicker.setDefaults({
        "onSelect": function (dateText, obj) {
            var parent = jQuery(this).parents(".group");

            jQuery(this).addClass("no-focusout");
        },
        "onClose": function (dateText, obj) {
            var parent = jQuery(this).parents(".group");

            if (jQuery(this).hasClass("no-focusout")) {
                jQuery(this).removeClass("no-focusout");
                return;
            }
            jQuery(this).trigger("change");
            animate_out_focus(parent);
        },
        "minDate": 0
    });

    //initial configuration for the elementos of the form
    jQuery("form .group").each(function () {

        jQuery(this).find("input, textarea, select").attr("autocomplete", "off");

        var top_original = jQuery(this).find("label").css("top");
        jQuery(this).data("top_original", top_original);

        //if the input is a datepicker we should add listeners to the select and close events
        if (jQuery(this).find(".hasDatepicker").size() > 0) {

            //if the user select a date there shouldn't be a focusout event.
            //through the class we (on close) control if the focusout event
            //should be trigger or not.
            jQuery(this).find(".hasDatepicker")
                .datepicker("option", "onSelect", function (dateText, obj) {
                    var parent = jQuery(this).parents(".group");

                    jQuery(this).addClass("no-focusout");
                });


            //when the datepicker close we should fire the focusout effect, except if
            //its closed because the user pickup a date.
            jQuery(this).find(".hasDatepicker")
                .datepicker("option", "onClose", function (dateText, obj) {
                    var parent = jQuery(this).parents(".group");

                    if (jQuery(this).hasClass("no-focusout")) {
                        jQuery(this).removeClass("no-focusout");
                        return;
                    }

                    animate_out_focus(parent);
                });
        }

        //if there is a value on the input/textarea fire the focusin effect
        if ((jQuery(this).find("input").size() > 0 && jQuery(this).find("input").val() != "" ) ||
            (jQuery(this).find("textarea").size() > 0 && jQuery(this).find("textarea").val() != "" ) ||
            (jQuery(this).find("select").size() > 0 && jQuery(this).find("select").val() != "" )) {
            animate_in_focus(jQuery(this));
        }
    });


    //listener for the click on label. if clicked it would trigger the focus of the input behind
    jQuery("body").on("click", "form label", function () {

        var parent = jQuery(this).parents(".group");
        jQuery(parent).find("input, textarea, select").focus();

        //if ( jQuery(parent).find(".dd-container").size() > 0 ) {
        //	animate_in_focus(parent);
        //	jQuery(parent).find(".dd-container").click();
        //}
    });

    //listener of focus on the input area. If it doesnt have the active class should work fine.
    jQuery("body").on("focus", "form input, form textarea, form select", function () {

        var parent = jQuery(this).parents(".group");
        animate_in_focus(parent);
    });


    //listener for when the input lose the focus, to get the label back in place if there is no text on the input.
    jQuery("body").on("focusout", "form input, form textarea, form select", function () {
        var parent = jQuery(this).parents(".group");
        //if the input is a datepicker the focusout effect is controlled trougth other logic
        if (jQuery(parent).find(".hasDatepicker").size() > 0) {
            return;
        }

        animate_out_focus(parent);
    });

}

//function to process the animation of the focus in event.
function animate_in_focus(parent) {

    if (!jQuery(parent).hasClass('material-design')) {
        jQuery(parent).find("label").finish().animate({
            top: "2px",
            fontSize: "80%",
            opacity: "0.3"
        }, 400, function () {

            jQuery(parent).addClass('material-design');
        });
    }

}

//function to process the animation of the focus out event
function animate_out_focus(parent) {
    var top = jQuery(parent).data("top_original");

    if (jQuery(parent).find("input").val() == "" ||
        jQuery(parent).find("textarea").val() == "" ||
        jQuery(parent).find("select").val() == "") {

        jQuery(parent).find("label").finish().animate({
                top: top + "",
                fontSize: "100%",
                opacity: "1"
            },
            400,
            function () {
                jQuery(parent).removeClass('material-design');
            });

    }
}
/*******************************************    end */


/*******************************************    function to work around the contact form ajax settings */
var validation = false;

function personal_ajaxForm_pre_send($form) {
    /* the idea is to hide the form and show the block with the loading info and so.
     */
    jQuery("img.ajax-loader").remove();

    if (validation) return true;

    var i = $form.find("[aria-required]").size();

    $form.find('input-required').removeClass('input-required');

    $form.find("[aria-required]").each(function () {
        if (jQuery(this).val() == '') {
            i = -1;	//this way the script will never go through i==0

            jQuery(this).addClass('input-required').change(function () {
                if (jQuery(this).val() != '') jQuery(this).removeClass('input-required');
            });
        }

        i--;

        if (i == 0) {
            jQuery($form).fadeOut(function () {
                jQuery('.submit-info').fadeIn();

                validation = true;
                $form.submit();
            });
        }
    });

    return false;
}

function personal_ajaxForm_after_send($form) {
    /* the idea is to hide the loading info.
     */
    jQuery("img.ajax-loader, .screen-reader-response").remove();

    validation = false;

    setTimeout(function () {
        jQuery('.submit-info').finish().fadeOut(function () {
            jQuery('.submit-info').remove();
            jQuery('.form-submission-msg').fadeIn();

            jQuery(".fa-envelope-o").toggleClass("fa-envelope-o fa-check");
        });
    }, 100);

}

/**************************************************************************  function if there is an error during the ajaxform submit */
function personal_ajaxForm_after_send_error($form) {
    jQuery('submit-info').fadeOut(function () {
        //jQuery('.form-submission-msg').fadeIn();
    });
}

/*************************************************************************    code for the ligthbox effect   */
function set_ligth_box_listenner() {

    jQuery("#gallery-1").on("click", ".owl-item.active", function (event) {

        if (jQuery(this).hasClass(".order1, .order3")) return;

        event.preventDefault();
        event.stopPropagation();

        //if ( isMobile.any() ) {return;}

        var $this = jQuery(this);

        var id_wrapper = "image-wrapper-id";

        var element = jQuery("<div id='close-wrapper' class='close-wrapper white-bg'>\
                       <div class='x-container-fluid max width'><div class='x-column x-1-1'>\
                         <div class='close'></div>\
                         <div id='" + id_wrapper + "' class='image-wrapper'></div>\
                       <div></div></div>");

        jQuery("#top").after(element);
        jQuery("#" + id_wrapper).append($this.find("img").clone());

        var reg = /-([0-9]*){1}x([0-9]*){1}/;
        var imgSrc = jQuery("#" + id_wrapper).find("img").attr('src');
        var newImgSrc = imgSrc.replace(reg, '');

        jQuery("#" + id_wrapper).find("img").attr('src', newImgSrc);
        jQuery("#" + id_wrapper).find("img").removeAttr("width");

        jQuery("#top").addClass("white-bg-overlay");
        jQuery("#close-wrapper").hide().fadeIn();

        jQuery("#close-wrapper").find(".close").click(function (event) {
            event.stopPropagation();

            jQuery("#top").removeClass("white-bg-overlay");

            jQuery(this).parents("#close-wrapper").fadeOut(function () {
                jQuery(this).remove();
            });
        });
    });


    setTimeout(function () {
        jQuery("body").on("click", function () {

            if (jQuery("#close-wrapper").size() > 0) {
                jQuery("#close-wrapper").find(".close").click();
            }
        });
    }, 1000);
}

/**************************************************************************   code for the functionality of the gallery */
function set_functionality_for_the_gallery() {
    //jQuery("#gallery-1").data('owlCarousel').destroy();
    //jQuery("#gallery-thumb").remove();
    //jQuery("#gallery-1:before, #gallery-1:after").css("background", "none");


    //create the structure for the thumb block
    var $owl_galery1 = jQuery("#gallery-1");
    jQuery("#gallery-1").after("<div id='gallery-thumb'></div>");
    var $owl_galery_thumb = jQuery("#gallery-thumb");

    //now we copy every element from galery1 to galery_thumb
    $owl_galery_thumb.append($owl_galery1.children().clone());

    //add the pre and last element.
    var current_last = $owl_galery1.find(".gallery-item:last-child");
    var current_first = $owl_galery1.find(".gallery-item:first-child");

    $owl_galery1.prepend(current_last.clone());
    //$owl_galery1.append(current_first.clone());

    $owl_galery1.owlCarousel({
        slideSpeed: 1000,
        navigation: true,
        navigationText: ["<", ">"],
        pagination: false,
        responsiveRefreshRate: 200,
        items: 2,
        itemsDesktop: [1199, 2],
        itemsDesktopSmall: [979, 2],
        itemsTablet: [768, 1],
        itemsMobile: [479, 1],
        autoWidth: false,
        addClassActive: true,
        afterAction: callbackAfterAction,
        afterInit: function () {

            if (isMobile.any()) {
                return;
            }

            //to add a left margin to the owl gallery
            var first_element_width = $owl_galery1.find(".owl-item:first").css("width");
            var left_width = first_element_width.replace("px", "") * 0.5;
            $owl_galery1.find(".owl-wrapper").css("left", "-" + left_width + "px");
            $owl_galery_thumb.css("width", first_element_width);
        },
        afterUpdate: function () {		//callback for responsivenes

            if (isMobile.any()) {
                return;
            }

            //to add a left margin to the owl gallery
            var first_element_width = $owl_galery1.find(".owl-item:first").css("width");
            var left_width = first_element_width.replace("px", "") * 0.5;
            $owl_galery1.find(".owl-wrapper").css("left", "-" + left_width + "px");
            $owl_galery_thumb.css("width", first_element_width);
        }
    });

    $owl_galery_thumb.owlCarousel({
        items: 6,
        itemsDesktop: [1199, 6],
        itemsDesktopSmall: [979, 5],
        itemsTablet: [768, 4],
        itemsMobile: [479, 3],
        pagination: true,
        responsiveRefreshRate: 100,
        afterInit: function (el) {
            el.find(".owl-item").eq(0).addClass("synced");
        }
    });

    /* callback after the main owl render the move */
    function callbackAfterAction(el) {
        //here we would keep in sinc the 2 owl carrusel
        var current = this.currentItem;
        var prev = this.prevItem;

        jQuery("#gallery-thumb")
            .find(".owl-item")
            .removeClass("synced")
            .eq(current)
            .addClass("synced")
        if ($owl_galery_thumb.data("owlCarousel") !== undefined) {
            center(current)
        }

        //detect if we are on chrome.
        if (isMobile.Chrome()) {
            //first_element_width = $owl_galery1.find(".owl-item:first").css("width").replace("px", "");
            //left_width = first_element_width * 0.5;
            //goToPixel = current*first_element_width + left_width;
            //preLeftShouldBe = prev*first_element_width + left_width;;
            //
            //$owl_galery1.find('.owl-wrapper').finish().css("left", "-" + preLeftShouldBe+"px");
            //console.log( $owl_galery1.find('.owl-wrapper').css("left") );
            //
            ////the first one and you click prev
            ////the last one and you click next
            //
            //$owl_galery1.find('.owl-wrapper').animate({
            //	"left" : "-" + goToPixel
            //},1000);

        }


        if (isMobile.any()) {
            return;
        }

        var i = 0;
        jQuery(".owl-item.overlay").removeClass('overlay order1 order3');
        jQuery(".owl-item.active").each(function () {
            i++;
            if (i == 2) {
                jQuery(this).next(".owl-item").addClass('active overlay order3');
            }
            else {
                jQuery(this).addClass('overlay order' + i);
            }
        });
    }

    /* listener to a click on the thumb collection, to trigger the proper movemment on the main owl carousel*/
    $owl_galery_thumb.on("click", ".owl-item", function (e) {
        e.preventDefault();
        var number = jQuery(this).data("owlItem");
        $owl_galery1.trigger("owl.goTo", number);
    });

    /* auxiliar function to center properly the things... */
    function center(number) {
        var sync2visible = $owl_galery_thumb.data("owlCarousel").owl.visibleItems;
        var num = number;
        var found = false;
        for (var i in sync2visible) {
            if (num === sync2visible[i]) {
                var found = true;
            }
        }

        if (found === false) {
            if (num > sync2visible[sync2visible.length - 1]) {
                $owl_galery_thumb.trigger("owl.goTo", num - sync2visible.length + 2)
            } else {
                if (num - 1 === -1) {
                    num = 0;
                }
                $owl_galery_thumb.trigger("owl.goTo", num);
            }
        } else if (num === sync2visible[sync2visible.length - 1]) {
            $owl_galery_thumb.trigger("owl.goTo", sync2visible[1])
        } else if (num === sync2visible[0]) {
            $owl_galery_thumb.trigger("owl.goTo", num - 1)
        }
    }


    /* listener to display properly only the higlighten image */
    jQuery(".owl-wrapper").on("click", ".owl-item.overlay", function (event) {
        event.preventDefault();
        event.stopPropagation();

        var owl = jQuery("#gallery-1").data('owlCarousel');

        if (jQuery(this).hasClass("order1")) owl.prev();   // Go to previous slide
        else                                   owl.next();   // Go to next slide

        return false;
    });
}


function set_functionality_for_the_gallery__old_version() {
    if (jQuery("#gallery-1").size() == 0) return;

    //we should add a loading icon

    //create the structure for the thumb block
    var $owl_galery1 = jQuery("#gallery-1");
    jQuery("#gallery-1").after("<div id='gallery-thumb'></div>");
    var $owl_galery_thumb = jQuery("#gallery-thumb");

    //now we copy every element from galery1 to galery_thumb
    $owl_galery_thumb.append($owl_galery1.children().clone());

    //add the pre and last element.
    var current_last = $owl_galery1.find(".gallery-item:last-child");
    var current_first = $owl_galery1.find(".gallery-item:first-child");

    $owl_galery1.prepend(current_last.clone());
    $owl_galery1.append(current_first.clone());

    $owl_galery1.owlCarousel({
        slideSpeed: 1000,
        navigation: true,
        navigationText: ["<", ">"],
        pagination: false,
        responsiveRefreshRate: 200,
        items: 3,
        width: 600,
        autoWidth: false,
        addClassActive: true,
        afterAction: callbackAfterAction
    });

    $owl_galery_thumb.owlCarousel({
        items: 6,
        itemsDesktop: [1199, 6],
        itemsDesktopSmall: [979, 5],
        itemsTablet: [768, 4],
        itemsMobile: [479, 3],
        pagination: true,
        responsiveRefreshRate: 100,
        afterInit: function (el) {
            el.find(".owl-item").eq(0).addClass("synced");
        }
    });

    /* callback after the main owl render the move */
    function callbackAfterAction(el) {
        //here we would keep in sinc the 2 owl carrusel
        var current = this.currentItem;
        jQuery("#gallery-thumb")
            .find(".owl-item")
            .removeClass("synced")
            .eq(current)
            .addClass("synced")
        if ($owl_galery_thumb.data("owlCarousel") !== undefined) {
            center(current)
        }


        var i = 0;
        jQuery(".owl-item.overlay").removeClass('overlay order1 order3');
        jQuery(".owl-item.active").each(function () {
            i++;
            if (i == 2) return;
            jQuery(this).addClass('overlay order' + i);
        });
    }

    /* this function syn the main owl with the thumb list of elements */
    function syncPosition(el) {
    }

    /* listener to a click on the thumb collection, to trigger the proper movemment on the main owl carousel*/
    $owl_galery_thumb.on("click", ".owl-item", function (e) {
        e.preventDefault();
        var number = jQuery(this).data("owlItem");
        $owl_galery1.trigger("owl.goTo", number);
    });

    /* auxiliar function to center properly the things... */
    function center(number) {
        var sync2visible = $owl_galery_thumb.data("owlCarousel").owl.visibleItems;
        var num = number;
        var found = false;
        for (var i in sync2visible) {
            if (num === sync2visible[i]) {
                var found = true;
            }
        }

        if (found === false) {
            if (num > sync2visible[sync2visible.length - 1]) {
                $owl_galery_thumb.trigger("owl.goTo", num - sync2visible.length + 2)
            } else {
                if (num - 1 === -1) {
                    num = 0;
                }
                $owl_galery_thumb.trigger("owl.goTo", num);
            }
        } else if (num === sync2visible[sync2visible.length - 1]) {
            $owl_galery_thumb.trigger("owl.goTo", sync2visible[1])
        } else if (num === sync2visible[0]) {
            $owl_galery_thumb.trigger("owl.goTo", num - 1)
        }
    }


    /* listener to display properly only the higlighten image */
    jQuery(".owl-wrapper").on("click", ".owl-item.overlay", function (event) {
        event.preventDefault();
        event.stopPropagation();

        var owl = jQuery("#gallery-1").data('owlCarousel');

        if (jQuery(this).hasClass("order1")) owl.prev();   // Go to previous slide
        else                                   owl.next();   // Go to next slide

        return false;
    });

    /*
     for the original list of gallery elements and BEFORE the owl activation we should
     add the original first element as duplicate on the end of the list, and the original end
     shoudl be duplicated and added to the start of the lsit, so everithing will work just fine.

     */

}

/******************************************************************************************************************** **/

/**************************************************************************  script to detect if we are on mobile */
var isMobile = {
    Android: function () {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function () {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function () {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function () {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function () {
        return navigator.userAgent.match(/IEMobile/i);
    },
    Chrome: function () {
        return navigator.userAgent.match(/Chrome/i);
    },
    any: function () {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};