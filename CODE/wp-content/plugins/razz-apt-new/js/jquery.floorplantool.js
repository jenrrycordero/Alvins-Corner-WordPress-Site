(function($, window)
{
	if(!window['razz']) window.razz = {};
	if(!razz.aptdata) razz.aptdata = {};
	if(!razz.aptdata.raw) razz.aptdata.raw = {};
	
	razz.aptdata.units = function()
	{
		if(!razz.aptdata.raw.units)
		{
			
		}
		return razz.aptdata.raw.units;
	}
	
	
	
	
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
