/***              Isotope     */




function setDimensionImageModel(){
    h = jQuery(window).height();
    w = jQuery(window).width();

    if ( h < 800 ) {
        jQuery("html").css("position", "relative");
        jQuery("div#response-overlay").css("position", "absolute");
        jQuery("#response-wrapper-content").css("margin-top", jQuery(window).scrollTop() + 50 + "px");

    }
    else {
        jQuery("html").css("position", "static");
        jQuery("div#response-overlay").css("position", "fixed");
    }

    $img = jQuery("#content-column").find("img");
    if ( w<768 ) {
        //mobile.
        nh_img = h - 280;
        $img.css({
            width: "auto",
            height: nh_img + "px"
        });
    }
    else {
        nh_img = h - 280;
        $img.css({
            width: "auto",
            height: nh_img + "px"
        });
    }

    if ( h < 800 || $img.width() > $img.parent().width() - 20 ){
        $img.css({
            width: $img.parent().width() - 20 + "px",
            height: "auto"
        });
    }
}

jQuery(document).ready(function($) {

    $body = $("body");

    var $isotope_container = $('#razz-model-wrapper'),
        $filters = $('#razz-model-filter-wrapper');

    $(window).load(function () {
        $isotope_container.isotope({
            itemSelector: '.item',
            layoutMode: 'fitRows',
            bindResize: true
        });

        if ( window.location.hash != "" && window.location.hash != "#" ) {
            target_id = window.location.hash;
			
			if ( target_id.indexOf("#!") == -1 && $(target_id).length > 0 )
				$(target_id).click();
        }

        $(window).resize(function(){
            $isotope_container.isotope('layout');

            setDimensionImageModel();
            //overH = h - ( $("#content-column").height() + $("#response-content").offset().top );

        });
        //window.requestAnimationFrame(function(){
        //    console.log('here');
        //    $isotope_container.isotope('layout');
        //});

        $filters.find('a').filter(function () {
            target = $(this).data('filter');
            console.log( $(this).data('filter') );
            if ( target != "." )    return !$(target).length;
            else return false;
        }).remove();		
		
        $filters.on('click', 'a', function (e) {
            e.preventDefault();
            $filters.find('a').removeClass('selected');
            $(this).addClass('selected');

            $isotope_container.isotope({
                filter: $(this).data('filter')
            });

        });
    });






    $body.on("click", function(e){
        $this = $(e.target);
        e.stopPropagation();
        if ( $this.parents("#response-content").is(":visible") && $this.parents("#response-content").length == 0 ) {
            $(".close").click();
        }
    });

    $body.on("click", ".close", function(e){
        $this = $(e.target);
        e.stopPropagation();
        $responseWrapper.removeClass("visible");
        $responseWrapper.find("#response-content").html("");

    });


    var $responseWrapper = $("#response-overlay");
    var elemnt_actual;

    $body.on("click", ".nav", function(e){

        target_id = "#" + elemnt_actual;
        $actual = $(target_id).parent(".item");

        console.log( $(target_id) );

        if ( $(this).hasClass("pre") ) {
            $target = $actual.prev(".item");
            if ( $target.length == 0 ) $target = $actual.parent().find(".item:last");
        }
        else {
            $target = $actual.next(".item");
            if ( $target.length == 0 ) $target = $actual.parent().find(".item:first");
        }

        $target.find('.item-contents').click();

        e.preventDefault();
        e.stopPropagation();
    });

    $('.item-contents').on('click', function (e) {
        e.preventDefault();

        $this = $(this);

        currentScroll = jQuery(window).scrollTop();
        window.location.hash = encodeURIComponent($(this).data('model'));
        jQuery(window).scrollTop(currentScroll);

        $responseWrapper.appendTo("body");
        $responseWrapper.addClass("visible");

        model_id = $this.data('id');
        node_id = $this.attr("id");

        //console.log( $this.parents(".item") );

        $.ajax({
            url: ajax_object.ajax_url,
            data: {
                'action' : 'get_model_view',
                'model' : model_id,
                'current-url' : window.location.href
            }
        })
        .done(function(data) {
            elemnt_actual = node_id;
            $responseWrapper.find("#response-content").html(data);

            pdfUrl = ajax_object.ajax_url + "?action=model_print_pdf&model=" + model_id
            $responseWrapper.find(".generate-pdf").attr("href", pdfUrl);

            setDimensionImageModel();

            $responseWrapper.find("img").load(function() {
                setDimensionImageModel();
            });

        })
        .fail(function(data){
        });
    });
});







//animationframe polyfill
(function () {
    var lastTime = 0;
    var vendors = ['ms', 'moz', 'webkit', 'o'];
    for (var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x] + 'RequestAnimationFrame'];
        window.cancelAnimationFrame = window[vendors[x] + 'CancelAnimationFrame']
        || window[vendors[x] + 'CancelRequestAnimationFrame'];
    }

    if (!window.requestAnimationFrame) {
        window.requestAnimationFrame = function (callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function () {
                    callback(currTime + timeToCall);
                },
                timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };
    }

    if (!window.cancelAnimationFrame) {
        window.cancelAnimationFrame = function (id) {
            clearTimeout(id);
        };
    }
}());