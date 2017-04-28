
jQuery(function($)
{
	var $w = $(window);
	$.floorview = function(options)
	{
		var selectedapt = -1;

		var mc = $('#maincanvas');
		var stage = new Kinetic.Stage({
			container: 'maincanvas',
			width: 960,
			height: 750
		});
			
		var headerlayer = new Kinetic.Layer();
		stage.add(headerlayer);
		var layer = new Kinetic.Layer();
		stage.add(layer);
		
		var headimgloaded = false;
		var headimgobj = new Image();
		headimgobj.onload = function()
		{
			var headimgko = new Kinetic.Image(
			{
				id: 'headimgko',
				x: 0,
				y: 0,
				image: headimgobj,
				width: 960,
				height: 100,
				crop: {
					x: 0,
					y: 0,
					width: 960,
					height: 100
				}
			});
			headerlayer.add(headimgko);
			
			headimgloaded = true;
			OnResize();
		}
		headimgobj.src = 'PLACEHOLDER.jpg';
		
		var infobargroup = new Kinetic.Group();
		InitInfoBar();
		
		var imgloaded = false;
		var fpimgobj = new Image();
		fpimgobj.onload = function()
		{
			imgloaded = true;
			var fpimgw = fpimgobj.width;
			var fpimgh = fpimgobj.height;
			var swidth = 650;
			var sheight = 500;
			
			var scale = Math.min(swidth / fpimgw, sheight / fpimgh);
			var fpimgsw = fpimgw * scale;
			var fpimgsh = fpimgh * scale;
			var xoffset = 800/2 - fpimgsw/2;
			var yoffset = 600/2 - fpimgsh/2;
			var fpimgko = new Kinetic.Image(
			{
				id: 'fpimgko',
				x: xoffset,
				y: yoffset,
				image: fpimgobj,
				width: fpimgsw,
				height: fpimgsh
			});
			layer.add(fpimgko);
			
			for(var i = 0; i < fvars.apts.length; i++)
			{
				var apt = fvars.apts[i];
				apt.kobj = new Kinetic.Circle(
				{
					name: 'aptdot',
					x: 0,
					y: 0,
					radius: fvars.dots.radius,
					fill: fvars.dots.available.inner,
					stroke: fvars.dots.available.outer,
					strokeWidth: fvars.dots.stroke
				});
				layer.add(apt.kobj);
				
				var clrDotInner = $.Color(fvars.dots.selected.inner);
				var clrDotOuter = $.Color(fvars.dots.selected.outer);
				
				apt.kobj.tween = new Kinetic.Tween(
				{
					node: apt.kobj,
					easing: Kinetic.Easings.EaseInOut,
					fillR: clrDotInner.red(),
					fillG: clrDotInner.green(),
					fillB: clrDotInner.blue(),
					strokeR: clrDotOuter.red(),
					strokeG: clrDotOuter.green(),
					strokeB: clrDotOuter.blue(),
					duration: 0.1
				});
				
				apt.kobj.on('mouseover', function(i)
				{
					return (function()
					{
						$('body').css('cursor', 'pointer');
						for(var j = 0; j < fvars.apts.length; j++)
						{
							fvars.apts[j].kobj.tween.reverse();
						}
						fvars.apts[i].kobj.tween.play();
						SelectApartment(i);
						//fvars.apts[i].kobj.hovertween.play();
					});
				}(i));
				
				apt.kobj.on('mouseout', function(i)
				{
					return (function()
					{
						$('body').css('cursor', 'default');
						//fvars.apts[i].kobj.tween.reverse();
						//SelectApartment(-1);
					});
				}(i));
				
				apt.kobj.on('mouseup', function(i)
				{
					return (function(e)
					{
						if(e.which == 1)
						{
							$.address.value('/unit/'+fvars.apts[i].unit);
							//window.location.href = fvars.url.home + '/apartment/?rf=floor&unit=' + fvars.apts[i].unit;
						}
					});
				}(i));
			}
			//ResizeFloorplan();
			OnResize();
			layer.draw();
			if(fvars.apts.length > 0)
			{
				fvars.apts[0].kobj.tween.play();
				SelectApartment(0);
			}
		}
		fpimgobj.src = fvars.imgurl;
		
		function InitInfoBar()
		{
			var top = +fvars.pos.top;
			if(fvars.blocks[0].show)
			{
				var ibhead = new Kinetic.Rect(
				{
					x: 0,
					y: top,
					width: 200,
					height: 100,
					fill: fvars.blocks[0].bgcolor
				});
				infobargroup.add(ibhead);
				
				var headbarcalltxt1 = new Kinetic.Text(
				{
					x: 0,
					y: top+30,
					width: 200,
					text: 'CALL US WITH QUESTIONS!',
					fontSize: 14,
					fontFamily: 'Calibri',
					fill: fvars.blocks[0].fgcolor,
					align: 'center'
				});
				infobargroup.add(headbarcalltxt1);
				
				var headbarcalltxt2 = new Kinetic.Text(
				{
					x: 0,
					y: top+50,
					width: 200,
					text: '800-555-0123',
					fontSize: 20,
					fontFamily: 'Calibri',
					fill: fvars.blocks[0].fgcolor,
					align: 'center'
				});
				infobargroup.add(headbarcalltxt2);
				
				top += 100;
			}
			
			var ibtop = new Kinetic.Rect(
			{
				x: 0,
				y: top,
				width: 200,
				height: 200,
				fill: fvars.blocks[1].bgcolor
			});
			infobargroup.add(ibtop);
			
			var ibtoptxt = new Kinetic.Text(
			{
				x: 30,
				y: top+30,
				width: 140,
				text: 'SELECT AN AVAILABLE APT FOR PRICING AND MORE INFO.',
				fontSize: 19,
				fontFamily: 'Calibri',
				fill: fvars.blocks[1].fgcolor
			});
			infobargroup.add(ibtoptxt);
			
			var ibtopdot = new Kinetic.Circle(
			{
				x: 38,
				y: top+150,
				radius: fvars.dots.radius,
				fill: fvars.dots.available.inner,
				stroke: fvars.dots.available.outer,
				strokeWidth: fvars.dots.stroke
			});
			infobargroup.add(ibtopdot);
			
			var ibtopdottxt = new Kinetic.Text(
			{
				x: 55,
				y: top+144,
				text: 'AVAILABLE APT',
				fontSize: 12,
				fontFamily: 'Calibri',
				fill: fvars.blocks[1].fgcolor
			});
			infobargroup.add(ibtopdottxt);
			top += 200;
			
			var ibselbg = new Kinetic.Rect(
			{
				x: 0,
				y: top,
				width: 200,
				height: 375,
				fill: fvars.blocks[4].bgcolor,
				opacity: 0.7
			});
			infobargroup.add(ibselbg);
			
			var ibselheadbg = new Kinetic.Rect(
			{
				name: 'selhide',
				x: 0,
				y: top,
				width: 200,
				height: 50,
				fill: fvars.blocks[2].bgcolor,
				visible: true
			});
			infobargroup.add(ibselheadbg);
			
			var ibselheadtxt = new Kinetic.Text(
			{
				name: 'selhide',
				x: 30,
				y: top+15,
				text: 'YOU\'VE SELECTED:',
				fontSize: 18,
				fontFamily: 'Calibri',
				fill: fvars.blocks[2].fgcolor,
				visible: true
			});
			infobargroup.add(ibselheadtxt);
			
			top += 50;
			var ibseltitlebg = new Kinetic.Rect(
			{
				name: 'selhide',
				x: 0,
				y: top,
				width: 200,
				height: 50,
				fill: fvars.blocks[3].bgcolor,
				visible: true
			});
			infobargroup.add(ibseltitlebg);
			
			var ibseltitledot = new Kinetic.Circle(
			{
				name: 'selhide',
				x: 38,
				y: top+24,
				radius: fvars.dots.radius,
				fill: fvars.dots.selected.inner,
				stroke: fvars.dots.selected.outer,
				strokeWidth: fvars.dots.stroke,
				visible: true
			});
			infobargroup.add(ibseltitledot);
			
			var ibseltitletxt = new Kinetic.Text(
			{
				id: 'seltitle',
				name: 'selhide',
				x: 57,
				y: top+15,
				text: '',
				fontSize: 18,
				fontFamily: 'Calibri',
				fill: fvars.blocks[3].fgcolor,
				visible: true
			});
			infobargroup.add(ibseltitletxt);
			
			top += 50;
			var ibseldatalbl_model = new Kinetic.Text(
			{
				name: 'selhide',
				x: 25,
				y: top+20,
				text: 'MODEL:',
				fontSize: 12,
				fontFamily: 'Calibri',
				fill: fvars.blocks[4].fgcolor.title,
				visible: true
			});
			infobargroup.add(ibseldatalbl_model);
			
			var ibseldatalbl_bedrooms = new Kinetic.Text(
			{
				name: 'selhide',
				x: 25,
				y: top+45,
				text: 'BEDROOM(S):',
				fontSize: 12,
				fontFamily: 'Calibri',
				fill: fvars.blocks[4].fgcolor.title,
				visible: true
			});
			infobargroup.add(ibseldatalbl_bedrooms);
			
			var ibseldatalbl_bathrooms = new Kinetic.Text(
			{
				name: 'selhide',
				x: 25,
				y: top+70,
				text: 'BATHROOM(S):',
				fontSize: 12,
				fontFamily: 'Calibri',
				fill: fvars.blocks[4].fgcolor.title,
				visible: true
			});
			infobargroup.add(ibseldatalbl_bathrooms);
			
			var ibseldatalbl_sqft = new Kinetic.Text(
			{
				name: 'selhide',
				x: 25,
				y: top+95,
				text: 'SQUARE FEET:',
				fontSize: 12,
				fontFamily: 'Calibri',
				fill: fvars.blocks[4].fgcolor.title,
				visible: true
			});
			infobargroup.add(ibseldatalbl_sqft);
			
			var ibseldatalbl_price = new Kinetic.Text(
			{
				name: 'selhide',
				x: 25,
				y: top+120,
				text: 'PRICE:',
				fontSize: 12,
				fontFamily: 'Calibri',
				fill: fvars.blocks[4].fgcolor.title,
				visible: true
			});
			infobargroup.add(ibseldatalbl_price);
			
			
			var ibseldata_model = new Kinetic.Text(
			{
				id: 'selmodel',
				name: 'selhide',
				x: 70,
				y: top+20,
				width: 133,
				text: '',
				fontSize: 12,
				fontFamily: 'Calibri',
				fontStyle: 'bold',
				fill: fvars.blocks[4].fgcolor.data,
				visible: true
			});
			infobargroup.add(ibseldata_model);
			
			var ibseldata_bedrooms = new Kinetic.Text(
			{
				id: 'selbedroom',
				name: 'selhide',
				x: 100,
				y: top+45,
				text: '',
				fontSize: 12,
				fontFamily: 'Calibri',
				fontStyle: 'bold',
				fill: fvars.blocks[4].fgcolor.data,
				visible: true
			});
			infobargroup.add(ibseldata_bedrooms);
			
			var ibseldata_bathrooms = new Kinetic.Text(
			{
				id: 'selbathroom',
				name: 'selhide',
				x: 105,
				y: top+70,
				text: '',
				fontSize: 12,
				fontFamily: 'Calibri',
				fontStyle: 'bold',
				fill: fvars.blocks[4].fgcolor.data,
				visible: true
			});
			infobargroup.add(ibseldata_bathrooms);
			
			var ibseldata_sqft = new Kinetic.Text(
			{
				id: 'selsqft',
				name: 'selhide',
				x: 100,
				y: top+95,
				text: '',
				fontSize: 12,
				fontFamily: 'Calibri',
				fontStyle: 'bold',
				fill: fvars.blocks[4].fgcolor.data,
				visible: true
			});
			infobargroup.add(ibseldata_sqft);
			
			var ibseldata_price = new Kinetic.Text(
			{
				id: 'selprice',
				name: 'selhide',
				x: 60,
				y: top+120,
				text: '',
				fontSize: 12,
				fontFamily: 'Calibri',
				fontStyle: 'bold',
				fill: fvars.blocks[4].fgcolor.data,
				visible: true
			});
			infobargroup.add(ibseldata_price);
			
			top += 200;
			
			var ibfootbg = new Kinetic.Rect(
			{
				x: 0,
				y: top+75,
				width: 200,
				height: 75,
				fill: 'black',
				opacity: 0.9
			});
			infobargroup.add(ibfootbg);
			
			var ibfootbtn = new Kinetic.Group(
			{
				x: 30,
				y: top+86
			});
			var ibfootbtnbg = new Kinetic.Rect(
			{
				x: 0,
				y: 0,
				width: 140,
				height: 50,
				fill: '#626061',
				cornerRadius: 4,
			});
			ibfootbtn.add(ibfootbtnbg);
			
			var ibfootbtntxt = new Kinetic.Text(
			{
				x: 10,
				y: 14,
				width: 120,
				text: 'GO BACK TO SELECT ANOTHER FLOOR',
				fontSize: 12,
				fontFamily: 'Calibri',
				fill: 'white',
				align: 'center'
			});
			ibfootbtn.add(ibfootbtntxt);
			
			infobargroup.add(ibfootbtn);
			
			ibfootbtn.on('mouseup', function(e)
			{
				if(e.which == 1)
				{
					window.location.href = fvars.url.home + '/floor-plans/';
				}
			});
			
			ibfootbtn.on('mouseover', function(e)
			{
				$('body').css('cursor', 'pointer');
			});
			
			ibfootbtn.on('mouseout', function(e)
			{
				$('body').css('cursor', 'default');
			});
			
			
			layer.add(infobargroup);
			layer.draw();
		}
		
		function SelectApartment(index)
		{
			selectedapt = index;
			
			if(index == -1)
			{
				infobargroup.get('#seltitle')[0].setText('');
				infobargroup.get('#selmodel')[0].setText('');
				infobargroup.get('#selbedroom')[0].setText('');
				infobargroup.get('#selbathroom')[0].setText('');
				infobargroup.get('#selsqft')[0].setText('');
				infobargroup.get('#selprice')[0].setText('');
				infobargroup.get('.selhidee').each(function(obj)
				{
					obj.hide();
				});
				return;
			}
			
			infobargroup.get('#seltitle')[0].setText(fvars.apts[index].title);
			infobargroup.get('#selmodel')[0].setText(decodeEntities(fvars.apts[index].mid));
			infobargroup.get('#selbedroom')[0].setText(fvars.apts[index].bedrooms);
			infobargroup.get('#selbathroom')[0].setText(fvars.apts[index].bathrooms);
			infobargroup.get('#selsqft')[0].setText(fvars.apts[index].sqft);
			infobargroup.get('#selprice')[0].setText(fvars.apts[index].price);
			infobargroup.get('.selhidee').each(function(obj)
			{
				obj.show();
			});
		}
		
		function ResizeFloorplan()
		{
			if(!imgloaded) return;
			var xoffset = 0;
			var fpwidth = 0;
			if(mc.width() > 1160)
			{
				xoffset = infobargroup.getPosition().x - 800;
				fpwidth = 700;
			}
			else if(mc.width() <= 1160)
			{
				xoffset = infobargroup.getPosition().x - 640;
				fpwidth = 600;
			}
			
			var fpimgw = fpimgobj.width;
			var fpimgh = fpimgobj.height;
			var swidth = fpwidth;
			var sheight = 500;
			
			var scale = Math.min(swidth / fpimgw, fpimgh / sheight);
			var fpimgsw = fpimgw * scale;
			var fpimgsh = fpimgh * scale;
			var yoffset = (600/2 - fpimgsh/2) + 100;
			var fpimgko = layer.get('#fpimgko')[0];

			if(fvars.nospace)
			{
				xoffset = infobargroup.getPosition().x - fpwidth;
				yoffset = 0;
			}
			if(fvars.valign)
			{
				yoffset = 20;
			}
			
			fpimgko.setPosition(xoffset, yoffset);
			fpimgko.setSize(fpimgsw, fpimgsh);
			
			//headbarfloortxt.setPosition(xoffset, 35);
			//headertxtlayer.draw();
			
			for(var i = 0; i < fvars.apts.length; i++)
			{
				var apt = fvars.apts[i];
				apt.kobj.setPosition((apt.x * scale) + xoffset, (apt.y * scale) + yoffset);
				apt.kobj.setRadius(fvars.dots.radius);
			}
		}
		
		function decodeEntities(s)
		{
			var str, temp = document.createElement('p');
			temp.innerHTML = s;
			str = temp.textContent || temp.innerText;
			temp = null;
			return str;
		}
		
		function OnResize()
		{
			var mcwidth = mc.width();
			if(mcwidth <= 934)
			{
				if(mc.is(":visible"))
					mc.hide();
				return;
			}
			else if(mcwidth > 934 && !mc.is(":visible"))
			{
				mc.show();
			}
			stage.setSize(mcwidth, 750);
			
			if(headimgloaded)
			{
				var headimgko = headerlayer.get('#headimgko')[0];
				headimgko.setSize(mcwidth, 100);
				headimgko.setCrop({
					x: 0,
					y: 200,
					width: Math.min(mcwidth, headimgobj.width),
					height: 100
				});
				headimgko.draw();
			}
			
			var xoffset = 0;
			if(mcwidth > 1160)
			{
				xoffset = ((mcwidth - 1000) / 2) + 800;
			}
			else if(mcwidth <= 1160)
			{
				xoffset = ((mcwidth - 846) / 2) + 646;
			}
			
			infobargroup.setPosition(xoffset, 0);
			
			//headbarcalltxt1.setPosition(xoffset, 30);
			//headbarcalltxt2.setPosition(xoffset, 50);
			
			ResizeFloorplan();
		}
		
		$w.resize(OnResize);
		OnResize();
		
		return this;
	}
	
	var fv = jQuery.floorview();
});