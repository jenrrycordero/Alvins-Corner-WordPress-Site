jQuery(document).ready(function($) {
    $('#upload_logo_button').click(function() {
        tb_show('Upload a logo', 'media-upload.php?referer=theme-options&spec=logo&type=image&TB_iframe=true&post_id=0', false);

		window.send_to_editor = function(html) {
		    var image_url = $('img',html).attr('src');
		    $('#logo_url').val(image_url);
		    tb_remove();
		    $('#upload_logo_preview img').attr('src',image_url);
		 
		    $('#submit_options_form').trigger('click');
		}
        return false;
    });

    $('#upload_logo_preview img')
    	.css("cursor","pointer")
    	.click(function(){
    		$('#upload_logo_button').trigger("click");
    	});
    $('#logo_url').click(function(){
    	$('#upload_logo_button').trigger("click");
    });


    $('#upload_headerbg_button').click(function() {
        tb_show('Upload an image', 'media-upload.php?referer=theme-options&spec=headerbg&type=image&TB_iframe=true&post_id=0', false);

		window.send_to_editor = function(html) {
		    var image_url = $('img',html).attr('src');
		    $('#headerbg_url').val(image_url);
		    tb_remove();
		    $('#upload_headerbg_preview img').attr('src',image_url);
		 
		    $('#submit_options_form').trigger('click');
		}
        return false;
    });

    $('#upload_headerbg_preview img')
    	.css("cursor","pointer")
    	.click(function(){
    		$('#upload_headerbg_button').trigger("click");
    	});
    $('#headerbg_url').click(function(){
    	$('#upload_headerbg_button').trigger("click");
    });
});