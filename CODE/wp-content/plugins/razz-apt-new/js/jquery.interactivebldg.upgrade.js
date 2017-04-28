
(function($, window)
{
	$.ibldg = function(options)
	{
		var settings = $.extend({
            topOffset: 0
        }, options);
		
		(function(a){(jQuery.browser=jQuery.browser||{}).mobile=/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))})(navigator.userAgent||navigator.vendor||window.opera);
		if($.browser.mobile) return;
		$('#maincanvas').css(
		{
			'width': ibvars.width,
			'height': ibvars.height
		});

		ibvars.dims.BGLoaded = function()
		{
			return ('bg' in ibvars.dims);
		};
		
		ibvars.dims.ReCalculate = function()
		{
			ibvars.dims.screen = [];
			//ibvars.dims.screen.width = $('#maincanvas').width();
			ibvars.dims.screen.width = $(window).width();
			ibvars.dims.screen.height = $('#maincanvas').height();
			
			if(ibvars.dims.screen.height < 600)
			{
				ibvars.dims.screen.height = 600;
			}
			
			if(ibvars.dims.BGLoaded())
			{
				ibvars.dims.bg.cur = [];
				ibvars.dims.bg.cur.scale = ibvars.retina ? 0.5 : 1;
				ibvars.dims.bg.cur.width = ibvars.dims.bg.orig.width * ibvars.dims.bg.cur.scale;
				ibvars.dims.bg.cur.height = ibvars.dims.bg.orig.height * ibvars.dims.bg.cur.scale;
				ibvars.dims.screen.height = ibvars.dims.bg.cur.height;
				ibvars.dims.bg.cur.x = ibvars.dims.screen.width/2 - ibvars.dims.bg.cur.width/2;
				ibvars.dims.bg.cur.y = ibvars.dims.screen.height/2 - ibvars.dims.bg.cur.height/2;
				$('#maincanvas').height(ibvars.dims.bg.cur.height);
				/*
				ibvars.dims.bg.cur.scale = ibvars.dims.screen.width / ibvars.dims.bg.orig.width;
				ibvars.dims.bg.cur.width = ibvars.dims.bg.orig.width * ibvars.dims.bg.cur.scale;
				ibvars.dims.bg.cur.height = ibvars.dims.bg.orig.height * ibvars.dims.bg.cur.scale;
				ibvars.dims.screen.height = ibvars.dims.bg.cur.height;
				ibvars.dims.bg.cur.x = ibvars.dims.screen.width/2 - ibvars.dims.bg.cur.width/2;
				ibvars.dims.bg.cur.y = ibvars.dims.screen.height/2 - ibvars.dims.bg.cur.height/2;
				$('#maincanvas').height(ibvars.dims.bg.cur.height);*/
				
				
				for(var i = 0; i < ibvars.dims.floor.length; i++)
				{
					ibvars.dims.floor[i].poly = [];
					for(var j = 0; j < ibvars.dims.floor[i].origpoly.length; j++)
					{
						ibvars.dims.floor[i].poly.push(ibvars.dims.floor[i].origpoly[j][0] * ibvars.dims.bg.cur.scale + ibvars.dims.bg.cur.x);
						ibvars.dims.floor[i].poly.push(ibvars.dims.floor[i].origpoly[j][1] * ibvars.dims.bg.cur.scale + ibvars.dims.bg.cur.y);
					}
				}
			}
			
			ibvars.dims.info = [];
			ibvars.dims.info.y = 0;
			if(ibvars.dims.screen.width <= 200)
				ibvars.dims.info.x = 0;
			else if(ibvars.dims.screen.width <= 600)
				ibvars.dims.info.x = ibvars.dims.screen.width/2 - 100;
			else if(ibvars.dims.screen.width <= 920)
				ibvars.dims.info.x = ibvars.dims.screen.width - 250;
			else if(ibvars.dims.screen.width <= 1200)
				ibvars.dims.info.x = ibvars.dims.screen.width - 300;
			else
				ibvars.dims.info.x = ibvars.dims.screen.width - 400;
				
		};
		
		ibvars.dims.ReCalculate();
		
		var stage = new Kinetic.Stage({
			container: 'maincanvas',
			width: ibvars.dims.screen.width,
			height: ibvars.dims.screen.height
		});
		
		var layers = [];
		layers.bg = new Kinetic.Layer();
		layers.poly = new Kinetic.Layer();
		layers.fadeout = new Kinetic.Layer();
		layers.tooltip = new Kinetic.Layer();
		layers.info = new Kinetic.Layer();
		stage.add(layers.bg);
		
		infobar = new Kinetic.Group();
		
		
		var bgimgobj = new Image();
		bgimgobj.onload = function()
		{
			ibvars.dims.bg = [];
			ibvars.dims.bg.orig = [];
			ibvars.dims.bg.orig.width = bgimgobj.width;
			ibvars.dims.bg.orig.height = bgimgobj.height;
			ibvars.dims.ReCalculate();
			
			var bgimg = new Kinetic.Image(
			{
				id: 'bgimg',
				x: ibvars.dims.bg.cur.x,
				y: ibvars.dims.bg.cur.y,
				image: bgimgobj,
				width: ibvars.dims.bg.cur.width,
				height: ibvars.dims.bg.cur.height
			});
			layers.bg.add(bgimg);
			layers.bg.draw();
			InitPolys();
			InitFadeOut();
			InitTooltip();
			InitInfoBox();
		};
		bgimgobj.src = ibvars.bldg_img;
		
		function InitTooltip()
		{
		
			var tt = new Kinetic.Group({
				id: 'tooltip',
				x: 0,
				y: 0
			});
			tt.add(new Kinetic.Tag({
				fill: 'black', //TODO: Set color from settings
				pointerDirection: 'down', //TODO: Set direction from dropdown in settings
				pointerWidth: 10,
				pointerHeight: 10,
				lineJoin: 'round'
			}));
			tt.add(new Kinetic.Text({
				id: 'tooltiptext',
				text: '',
				fontFamily: 'Calibri',
				fontSize: '18px',
				padding: 5,
				fill: 'white' //TODO: Set color from settings
			}));
			layers.info.add(tt);
			/*
			var rect = new Kinetic.Rect({
				x: 300,
				y: 300,
				width: 100,
				height: 100,
				fill: 'green',
				stroke: 'black',
				strokeWidth: 4
			});
			layers.info.add(rect);*/
		}
		
		function setTooltipText(txt)
		{
			var tt = layers.info.find('#tooltiptext')[0];
			tt.setText(txt);
		}
		
		function setTooltipPos(x, y)
		{
			var tt = layers.info.find('#tooltip')[0];
			tt.position({x: x, y:  y}); //TODO: offset by height
		}
		
		function showTooltip(txt, x, y)
		{
			setTooltipText(txt);
			setTooltipPos(x, y);
			layers.info.draw();
		}
		
		function hideTooltip()
		{
			return;
			setTooltipPos(-999, 0);
		}
		
		function InitFadeOut()
		{
			var left = new Kinetic.Rect({
				id: 'foleft',
				x: ibvars.dims.bg.cur.x - 1,
				y: ibvars.dims.bg.cur.y,
				width: ibvars.fadeout.dist,
				height: ibvars.dims.bg.cur.height,
				fillLinearGradientStartPoint: {x:ibvars.fadeout.dist, y:0},
				fillLinearGradientEndPoint: {x:0,y:0},
				fillLinearGradientColorStops: [0, ibvars.fadeout.from, 0.95, ibvars.fadeout.to]
			});
			layers.fadeout.add(left);
			
			var right = new Kinetic.Rect({
				id: 'foright',
				x: ibvars.dims.bg.cur.x + ibvars.dims.bg.cur.width - ibvars.fadeout.dist,
				y: ibvars.dims.bg.cur.y,
				width: ibvars.fadeout.dist,
				height: ibvars.dims.bg.cur.height,
				fillLinearGradientStartPoint: {x:0, y:0},
				fillLinearGradientEndPoint: {x:ibvars.fadeout.dist,y:0},
				fillLinearGradientColorStops: [0, ibvars.fadeout.from, 0.95, ibvars.fadeout.to]
			});
			layers.fadeout.add(right);
			
			stage.add(layers.fadeout);
		}

		function InitInfoBox()
		{
			layers.info.add(infobar);
			infobar.position({x: ibvars.dims.info.x, y:  ibvars.infobar.offset});
			
			var infobarheadbg = new Kinetic.Rect(
			{
				x: 0,
				y: 0,
				width: 200,
				height: 130,
				fill: ibvars.infobar.block1.bg
			});
			infobar.add(infobarheadbg);
			
			var infobarheadtxt = new Kinetic.Text(
			{
				x: 30,
				y: 30,
				width: 140,
				text: 'SELECT A FLOOR TO SEE AVAILABLE APARTMENTS AND MORE INFO.',
				fontSize: 17,
				fontFamily: 'Calibri',
				fill: ibvars.infobar.block1.fg
			});
			infobar.add(infobarheadtxt);
			
			var infobarfsbg = new Kinetic.Rect(
			{
				x: 0,
				y: 130,
				width: 200,
				height: 90 + (ibvars.dims.floor.length * 44), //orig 320
				fill: ibvars.infobar.main.bg
			});
			infobar.add(infobarfsbg);
			
			var infobarfstxt = new Kinetic.Text(
			{
				x: 30,
				y: 160,
				width: 140,
				text: 'SELECT A FLOOR:',
				fontSize: 17,
				fontFamily: 'Calibri',
				fill: ibvars.infobar.main.fg
			});
			infobar.add(infobarfstxt);
			
			for(var i = 0; i < ibvars.dims.floor.length; i++)
			{
				var button = new Kinetic.Group();
				infobar.add(button);
				var buttonbg = new Kinetic.Rect(
				{
					id: 'fbtn' + i,
					x: 30,
					y: 200 + (i * 44),
					width: 170,
					height: 34,
					cornerRadius: 3,
					fill: ibvars.infobar.floorbtn.bg
				});
				button.add(buttonbg);
				
				var buttontxt = new Kinetic.Text(
				{
					id: 'fbtntxt' + i,
					x: 60,
					y: 208 + (i * 44),
					text: ibvars.dims.floor[i].name,
					fontSize: 15,
					fontFamily: 'Calibri',
					fill: ibvars.infobar.floorbtn.fg
				});
				button.add(buttontxt);
				
				var hovercolor = $.Color(ibvars.infobar.floorbtn.hover.bg);
				buttonbg.tween = new Kinetic.Tween(
				{
					node: buttonbg,
					easing: Kinetic.Easings.EaseOut,
					fill: ibvars.infobar.floorbtn.hover.bg,
					fillRed: hovercolor.red(),
					fillGreen: hovercolor.green(),
					fillBlue: hovercolor.blue(),
					opacity: hovercolor.alpha(),
					duration: 0.7
				});
				
				var hovertxtcolor = $.Color(ibvars.infobar.floorbtn.hover.fg);
				buttontxt.tween = new Kinetic.Tween(
				{
					node: buttontxt,
					easing: Kinetic.Easings.EaseOut,
					fill: ibvars.infobar.floorbtn.hover.bg,
					fillRed: hovertxtcolor.red(),
					fillGreen: hovertxtcolor.green(),
					fillBlue: hovertxtcolor.blue(),
					opacity: hovertxtcolor.alpha(),
					duration: 0.7
				});
				
				button.on('mouseover', function(i)
				{
					return (function()
					{
						document.body.style.cursor = 'pointer';
						layers.info.find('#fbtn' + i)[0].tween.play();
						layers.info.find('#fbtntxt' + i)[0].tween.play();
						layers.poly.find('#fpoly' + i)[0].tween.play();
					});
				}(i));
				
				button.on('mouseout', function(i)
				{
					return (function()
					{
						document.body.style.cursor = 'default';
						layers.info.find('#fbtn' + i)[0].tween.reverse();
						layers.info.find('#fbtntxt' + i)[0].tween.reverse();
						layers.poly.find('#fpoly' + i)[0].tween.reverse();
					});
				}(i));
				
				button.on('mousedown', function(i)
				{
					return (function(e)
					{
						if(e.which == 1)
						{
							window.location.href = '../floor-view/?id=' + ibvars.dims.floor[i].id;
						}
					});
				}(i));
			}

			stage.add(layers.info);
		}
		
		function InitPolys()
		{
			var hovercolor = $.Color(ibvars.floorhovercolor);
			for(var i = 0; i < ibvars.dims.floor.length; i++)
			{
				ibvars.dims.floor[i].polyobj = new Kinetic.Line(
				{
					id: 'fpoly' + i,
					points: ibvars.dims.floor[i].poly,
					fill: hovercolor.toHexString(),
					opacity: 0,
					closed: true
				});
				layers.poly.add(ibvars.dims.floor[i].polyobj);
				
				ibvars.dims.floor[i].polyobj.tween = new Kinetic.Tween(
				{
					node: ibvars.dims.floor[i].polyobj,
					easing: Kinetic.Easings.EaseOut,
					opacity: hovercolor.alpha(),
					duration: 0.7
				});
				ibvars.dims.floor[i].polyobj.on('mouseover touchstart', function(i)
				{
					return (function()
					{
						document.body.style.cursor = 'pointer';
						this.tween.play();
						layers.info.find('#fbtn' + i)[0].tween.play();
						layers.info.find('#fbtntxt' + i)[0].tween.play();
					});
				}(i));
				ibvars.dims.floor[i].polyobj.on('mousemove', function(i)
				{
					return (function()
					{
						//var pos = stage.findPointerPosition();
						//console.log(pos);
						//showTooltip('This is a test', pos.x, pos.y);
					});
				}(i));
				ibvars.dims.floor[i].polyobj.on('mouseout touchend', function(i)
				{
					return (function()
					{
						document.body.style.cursor = 'default';
						this.tween.reverse();
						layers.info.find('#fbtn' + i)[0].tween.reverse();
						layers.info.find('#fbtntxt' + i)[0].tween.reverse();
					});
				}(i));
				ibvars.dims.floor[i].polyobj.on('mousedown', function(i)
				{
					return (function(e)
					{
						if(e.which == 1)
						{
							window.location.href = '../floor-view/?id=' + ibvars.dims.floor[i].id;
						}
					});
				}(i));
				
			}
			
			stage.add(layers.poly);
		}
		
		function OnResize()
		{
			ibvars.dims.ReCalculate();
			stage.size({width: ibvars.dims.screen.width, height:  ibvars.dims.screen.height});
			infobar.position({x: ibvars.dims.info.x, y:  ibvars.infobar.offset});
			
			if(ibvars.dims.BGLoaded())
			{
				var bgimg = layers.bg.find('#bgimg')[0];
				bgimg.position({x: ibvars.dims.bg.cur.x, y:  ibvars.dims.bg.cur.y});
				bgimg.size({width: ibvars.dims.bg.cur.width, height:  ibvars.dims.bg.cur.height});
				
				var left = layers.fadeout.find('#foleft')[0];
				var right = layers.fadeout.find('#foright')[0];
				left.position({x: ibvars.dims.bg.cur.x - 1, y:  ibvars.dims.bg.cur.y});
				right.position({x: ibvars.dims.bg.cur.x + ibvars.dims.bg.cur.width - ibvars.fadeout.dist, y:  ibvars.dims.bg.cur.y});
				
				for(var i = 0; i < ibvars.dims.floor.length; i++)
				{
					ibvars.dims.floor[i].polyobj.setPoints(ibvars.dims.floor[i].poly);
				}
			}
		}
		
		$(window).on('resize focus', OnResize);
		OnResize();
	}
})(jQuery, window);
jQuery.ibldg();