/**
 * Spniset/Plugin to emulate a rigth vertical align on elements.
 *
 * There is some general options for the plugin:
 * breakpoint: Optional. By default is 991. Id indicate the windows.width() at the point it will stop the vertical align
 * vAlignClass: Optional. By default its valign but can be changed to whatever class you need.
 *
 * Also you need some data elements on the objets itself:
 * data-valign-target: Optional. By default is the parent element, but it can be set to whatever element you want. Has to be the id.
 *                         functionality
 * data-valign-top: Optional. If the data attribute exist then it will use it for the vertical align, if no then we will use margin-top
 * data-valign-preaction: Optional. function to call before the process, you can have access to the $htarget variable
 * data-valign-callback: Optional. function to call after the css has been applied.
 *
 */

var breakpoint = 0;
var vAlignClass = ".valign";

jQuery(vAlignClass).css("opacity", "0");

(function($){

    $(document).ready(function(){

        $(window).resize(function(){
            vAlign();
        });

        vAlign();
        $(window).resize();

    });


    $(window).load(function(){
        vAlign();
    });

})(jQuery);


function vAlign() {
    jQuery(vAlignClass).each(function(){

        $this = jQuery(this);


        if ( typeof $this.data("valign-breakpoint") != "undefined" ) {
            compareBreakpoint = $this.data("valign-breakpoint");
        }
        else {
            compareBreakpoint = breakpoint;
        }

        if ( compareBreakpoint > jQuery(window).width() ) {
            $this.removeAttr("style");
            return $this;
        }


        t = $this.data("valign-target");

        if ( typeof t != 'undefined' )
            $htarget = jQuery("#" + t );
        else
            $htarget = $this.parent();

        if ( $htarget.hasClass("adjust-h") && !$htarget.hasClass("adjust-h-done") ) return $this;

        $htarget.removeClass("adjust-h-done");

        if ( $this.outerHeight() < $htarget.outerHeight() ) {
            //we only work if the element can be vertically aligned,
            //if its taller than the target it doesnt make sense to align it

            if ( $this.data("valign-preAction") &&
                typeof($this.data("valign-preAction")) == "function" )
                $this.data("valign-preAction")();

            vTopDistance = ( $htarget.outerHeight() - $this.outerHeight() )/2;

            if ( $this.data("valign-top") )
                cssObject = { top: vTopDistance + "px" };
            else if ( $this.data("valign-padding") )
                cssObject = { paddingTop: vTopDistance + "px" };
            else
                cssObject = { marginTop: vTopDistance + "px" };

            $this.css(cssObject);

            if ( $this.data("valign-callback") &&
                typeof($this.data("valign-callback")) == "function" )
                $this.data("valign-callback")();

        }
        else {
            if ( $this.data("valign-top") )
                cssObject = { top: "0px" };
            else
                cssObject = { marginTop: "0px" };
            $this.css(cssObject);
        }

        $this.animate({
            "opacity" : "1"
        });
    });
}