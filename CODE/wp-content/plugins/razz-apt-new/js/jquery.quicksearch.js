(function($, window)
{
	var $w = $(window),
		$body = $('body'),
		ajo;
	window.filter = {bed: 0, bath: 0};
	
	$.fn.quicksearch = function(options)
	{
		var settings = $.extend({
            topOffset: 0,
			qs: $('#qsbar'),
			topbarwrap: $('#qsheadwrapper'),
			topbar: $('#qshead')
        }, options);
		
		function onScroll()
		{
			if(settings.topbarwrap.offset().top + settings.topbarwrap.height() > $w.height() + $w.scrollTop())
			{
				settings.topbar.css('position', 'fixed');
			}
			else
			{
				settings.topbar.css('position', '');
			}
			
			
			var mtop = 0;
			if($('#wpadminbar').length > 0)
				mtop += +$('#wpadminbar').height();
			
			mtop += +settings.topOffset;
			if(settings.topbarwrap.offset().top + settings.topbarwrap.height() - mtop < $w.scrollTop() && $w.width() >= 1080)
			{
				$('#qsleft').css({
					position: 'fixed',
					top: mtop
				});
			}
			else
			{
				$('#qsleft').css({
					position: '',
					top: ''
				});
			}
		}
		onScroll();
		$(window).scroll(onScroll);
		$(window).resize(onScroll);
		
		$(window).load(function(){
			if(window.location.hash == "#search")
			{
				setTimeout(function () {
					$('#qshead').trigger('click');
				}, 200);
			}
		});
		
		$('body').on('click', '#qshead, a[title="Apartment Search"], a[href="#search"]', function(e)
		{
			e.preventDefault();
			window.location.hash = "#search";
			var top = settings.topbarwrap.offset().top - settings.topOffset;
			if($('#wpadminbar').length > 0)
				top -= $('#wpadminbar').height();
			$('html, body').animate({
				scrollTop: top
			}, 500);
		});
		
		$('body').on('click', '.qsfilter_btn_circle', function(e)
		{
			e.preventDefault();
			var $this = $(this),
				type = $this.data('type'),
				inc = +$this.data('inc');
			
			
			var newval = Math.min(Math.max(filter[type] + inc, 0), qsvars.filter[type].length-1);
			if(newval != filter[type])
			{
				filter[type] = newval;
				UpdateQS();
			}
		});
		
		function UpdateQS()
		{
			if(filter.bed == 0)
				$('.qsfilter_btn_circle.btndn[data-type="bed"]').addClass('disabled');
			else
				$('.qsfilter_btn_circle.btndn[data-type="bed"]').removeClass('disabled');
			if(filter.bed == qsvars.filter.bed.length-1)
				$('.qsfilter_btn_circle.btnup[data-type="bed"]').addClass('disabled');
			else
				$('.qsfilter_btn_circle.btnup[data-type="bed"]').removeClass('disabled');
			if(filter.bath == 0)
				$('.qsfilter_btn_circle.btndn[data-type="bath"]').addClass('disabled');
			else
				$('.qsfilter_btn_circle.btndn[data-type="bath"]').removeClass('disabled');
			if(filter.bath == qsvars.filter.bath.length-1)
				$('.qsfilter_btn_circle.btnup[data-type="bath"]').addClass('disabled');
			else
				$('.qsfilter_btn_circle.btnup[data-type="bath"]').removeClass('disabled');
			
			
			var dispbed = qsvars.filter.bed[filter.bed];
			var dispbath = qsvars.filter.bath[filter.bath];
			
			
			$('.unitrow').hide().filter(function()
			{
				var $row = $(this),
					index = $row.data('id'),
					bed = qsvars.units[index].bed,
					bath = qsvars.units[index].bath;
				return ((dispbed == -1 || dispbed == bed) && (dispbath == -1 || dispbath == bath));
			}).show();
			
			$('#qsdata tfoot').toggle(!$('.unitrow:visible').length);
			
			
			if(dispbed == -1) dispbed = "ANY";
			if(dispbath == -1) dispbath = "ANY";
			if(dispbed == 0)
			{
				dispbed = "STUDIO";
				$('#qsfilter_disp_bed').css('font-size', qsvars.studio_size + 'px');
			}
			else
			{
				$('#qsfilter_disp_bed').css('font-size', '');
			}
			$('#qsfilter_disp_bed').html(dispbed);
			$('#qsfilter_disp_bath').html(dispbath);
		}
		window.UpdateQS = UpdateQS;
		
		function getUnits()
		{
			var $tb = $('#qsdata tbody');
			$tb.html('');
			for(var i = 0; i < qsvars.units.length; i++)
			{
				var unit = qsvars.units[i];
				var $tr = $('<tr />', {
					'class': 'unitrow' + unit.special,
					'data-id': i
				});
				$tr.append($('<td />', { //Unit Number
					'html': unit.unit
				}));
				$tr.append($('<td />', { //Bedroom
					'html': (unit.bed == 0) ? "Studio" : unit.bed,
					'data-value': unit.bed
				}));
				$tr.append($('<td />', { //Bathroom
					'html': unit.bath
				}));
				$tr.append($('<td />', { //Sqft
					'html': unit.sqft,
					'data-value': unit.sqft_raw
				}));
				$tr.append($('<td />', { //Rent
					'html': unit.rent,
					'data-value': unit.rent_raw
				}));
				$tr.append($('<td />', { //Date
					'html': unit.date
				}));
				$tr.append(unit.buttons); //Buttons shortcut
				$tb.append($tr);
			}
			$('#qsdata tbody').trigger('footable_initialize');
		}

		setTimeout(getUnits, 100);
		//UpdateQS();
		
		$('.footable').footable( //TODO: try table plugin from attadmin
		{
			delay: 100,
			breakpoints:{
				tablet: 800,
				medium: 650,
				phone: 500,
				small: 410,
				xsmall: 340
			}
		});
	};
})(jQuery, window);