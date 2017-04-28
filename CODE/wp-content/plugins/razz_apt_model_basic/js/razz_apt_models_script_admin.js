/*
 * Attaches the image uploader to the input field
 */

var autocompleteSourceArray = new Array();

jQuery(document).ready(function($){

    // Instantiates the variable that holds the media library frame.
    // meta_image_frame;

    // Runs when the image button is clicked.
    $('#meta-image-button').click(function(e){

        console.log(typeof meta_image);

        // Prevents the default action from occuring.
        e.preventDefault();

        // If the frame already exists, re-open it.
        if ( typeof meta_image_frame !== 'undefined' ) {
            meta_image_frame.open();
            return;
        }


        // Sets up the media library frame
        meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
            title: meta_image.title,
            button: { text:  meta_image.button },
            library: { type: 'image' }
        });

        // Runs when an image is selected.
        meta_image_frame.on('select', function(){

            // Grabs the attachment selection and creates a JSON representation of the model.
            var media_attachment = meta_image_frame.state().get('selection').first().toJSON();

            // Sends the attachment URL to our custom image input field.
            $('#meta-image').val(media_attachment.url);
            $('#meta-image-id').val(media_attachment.id);

            //console.log(media_attachment);
        });

        // Opens the media library frame.
        meta_image_frame.open();
    });

    $("#remove-icon").click(function(){
        $(this).parent().remove();
        $("#meta-image-razz-model-wrapper").removeClass("hidden").find("input").val("");
    });


    // to delete the filters.
    $filterWrapper = $("#razz-model-filter-wrapper");
    var filters = {}

    if ( $filterWrapper.length > 0 ) {
        $filterWrapper.append( $("<div/>", {
            id:"to-del-button",
            html: "Delete selected filters"
        }));

        $("#to-del-button").click(function(){

            console.log( (filters) );

            $.ajax({
                url: ajax_object.ajax_url,
                data: {
                    'action' : 'delete_filters',
                    'filters' : JSON.stringify(filters)
                },
                success: function( data ) {
                    if ( data = 0 ) {
                        $(".to-del").fadeOut( function(){
                            $(this).remove();
                        });
                        $("#to-del-button").fadeOut();
                    }
                }
            });
        });
    }

    $('body').on("click", ".filter-element", function(){
        $(this).toggleClass("to-del");

        filters[ '' + $(this).text() ] = $(this).hasClass("to-del");

        if ( $(".to-del").length > 0 ) {
            $("#to-del-button").fadeIn();
        }
        else {
            $("#to-del-button").fadeOut();
        }
    });


    //    Autocomplete for the add new model interface.
    //$.ajax({
    //    url: ajax_object.ajax_url,
    //    data: {
    //        'action' : 'load_filters'
    //    },
    //    success: function( data ) {
    //
    //        try {
    //            data = JSON.parse(data);
    //        }
    //        catch (e) {
    //            return;
    //        }
    //
    //
    //        jQuery.each(data, function(i, v){
    //            autocompleteSourceArray.push({ value:v, label:v});
    //
    //            $("<div/>", {
    //                class: "filter-element",
    //                html: v
    //            }).prependTo( $filterWrapper );
    //        });
    //    }
    //});
    //
    //$autocompleteTarget = jQuery("#autocomplete");
    //
    //if ( !$autocompleteTarget.is("input") ) $autocompleteTarget = $autocompleteTarget.find("input");
    //
    //if ($autocompleteTarget.length > 0 ) {
    //    $autocompleteTarget.autocomplete({
    //        minLength: 0,
    //        source: function( request, response ) {
    //            // delegate back to autocomplete, but extract the last term
    //            response( jQuery.ui.autocomplete.filter(
    //                autocompleteSourceArray, extractLast( request.term ) ) );
    //        },
    //        focus: function() {
    //            return false;
    //        },
    //        select: function( event, ui ) {
    //            var terms = split( this.value );
    //
    //            terms.pop();                        // remove the current input
    //            terms.push( ui.item.value );        // add the selected item
    //            terms.push( "" );                   // add placeholder to get the comma-and-space at the end
    //            this.value = terms.join( ", " );
    //            return false;
    //        }
    //    }).focus(function(){
    //        jQuery(this).data("uiAutocomplete").search(jQuery(this).val());
    //    }).click(function(){
    //        jQuery(this).data("uiAutocomplete").search(jQuery(this).val());
    //    });
    //
    //    function split( val ) {
    //        return val.split( /,\s*/ );
    //    }
    //    function extractLast( term ) {
    //        return split( term ).pop();
    //    }
    //}

});
