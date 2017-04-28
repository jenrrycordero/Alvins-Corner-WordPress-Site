(function($, window)
{
	if(!window['razz']) window.razz = {};
	if(!razz.popup) razz.popup = {};
	
	razz.popup.unit = function(id)
	{
		$.magnificPopup.open(
		{
			items: {
				type: 'iframe',
				src: 'test.html'
			},
			mainClass: 'mfp-fade'
		});
	};
})(jQuery, window);
