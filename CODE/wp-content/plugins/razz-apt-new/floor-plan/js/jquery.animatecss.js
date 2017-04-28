;(function($)
{
	window.animend = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend cancelanimation';
	$.fn.extend({
		animatecss: function(type, settings)
		{
			if(!settings) settings = {};
			settings = $.extend({
				onComplete: function(){}
			}, settings);
			var cananimate = $('html').hasClass('cssanimations');
			return this.each(function()
			{
				var $this = $(this);
				if(!cananimate)
				{
					settings.onComplete.call(this);
				}
				else
				{
					if($this.hasClass('animating'))
					{
						$this.trigger('cancelanimation');
					}
					
					var tmr = setTimeout(function()
					{
						$this.trigger('cancelanimation');
					},2100);
					$this.addClass('animated animating ' + type).one(animend, function(e)
					{
						e.stopPropagation();
						clearTimeout(tmr);
						$this.removeClass('animated animating ' + type);
						settings.onComplete.call(this);
					});
				}
				return true;
			});
		}
	});

})(jQuery);