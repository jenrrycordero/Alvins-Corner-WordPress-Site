//animationframe polyfill
(function() {
    var lastTime = 0;
    var vendors = ['ms', 'moz', 'webkit', 'o'];
    for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
        window.cancelAnimationFrame = window[vendors[x]+'CancelAnimationFrame']
        || window[vendors[x]+'CancelRequestAnimationFrame'];
    }

    if (!window.requestAnimationFrame)
        window.requestAnimationFrame = function(callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function() { callback(currTime + timeToCall); },
                timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };

    if (!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function(id) {
            clearTimeout(id);
        };
}());

(function($) {
    var FloorPlans = function() {
        var $w = $(window),
            $img_cont = $('.wraptocenter'),
            $img = $img_cont.find('img'),
            $mrow = $('.model-info-row'),
            oldWidth=-1,
            oldHeight=-1;
        var runImageSize = function() {
            var sWidth = $w.width(),
                sHeight = $w.height(),
                wrapHeight = sHeight - 150;

            if(sHeight !== oldHeight)
            {
                if (sWidth > 800) {
                    $img_cont.height(wrapHeight);
                } else {
                    $img_cont.height('auto');
                }
            }
            if(sWidth !== oldWidth)
            {
                var wrapWidth = $mrow.width();
                $img_cont.width(wrapWidth);
                if(navigator.userAgent.toLowerCase().indexOf('chrome') === -1)
                    $img.css({'max-width': wrapWidth});
            }

            oldWidth = sWidth;
            oldHeight = sHeight;
            window.requestAnimationFrame(runImageSize);
        };

        return {
            //main function to initiate template pages
            init: function() {
                runImageSize();
            }
        };

    }();


    $(window).load(function() {

        // init scripts
        FloorPlans.init();

    });



})(jQuery);