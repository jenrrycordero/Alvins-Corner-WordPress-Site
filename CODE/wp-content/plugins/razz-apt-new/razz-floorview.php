<?php

add_shortcode('rapt_floor_title', 'rapt_floor_title');
add_shortcode('rapt_floor_content', 'rapt_floor_content');
add_action('wp_footer', 'rapt_floor_footer', 101);

function rapt_floor_title()
{
	$pid = $_GET['id'];
	
	if(!is_numeric($pid) || get_post_type($pid) != 'apt-floor') return '';
	
	return get_the_title($pid);
}

function rapt_floor_footer()
{
	$pid = $_GET['id'];
	if(!is_numeric($pid) || get_post_type($pid) != 'apt-floor') return;
	$imgurl = wp_get_attachment_image_src(get_post_thumbnail_id($pid), 'full');
	$imgurl = $imgurl[0];
	$floortitle = get_the_title($pid);
	
	$xmlident = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_ident', true));
?>
	<script src="<?php echo plugins_url('js/kinetic-v4.5.4.min.js', __FILE__); ?>"></script>
	<script type="text/javascript">
		jQuery(function($)
		{
			var selectedapt = -1;
			var apts = [];
<?php
	$args = array(
		'post_type' 	=> 'apt',
		'post_status'	=> 'publish',
		'meta_query'	=> array(
			array(
				'key'	=> '_rapt_apt_floor',
				'value'	=> $pid
			),
			array(
				'key'	=> '_rapt_apt_available',
				'value'	=> 'yes'
			)
		)
	);
	$loop = new WP_Query($args);
	$i = 0;
	while($loop->have_posts()) : $loop->the_post();
		$lid = get_the_ID();
		echo "apts[$i] = [];\n";
		echo "apts[$i].x = " . get_post_meta($lid, '_rapt_unit_locx', true) . ";\n";
		echo "apts[$i].y = " . get_post_meta($lid, '_rapt_unit_locy', true) . ";\n";
		echo "apts[$i].id = '" . $lid . "';\n";
		echo "apts[$i].title = '" . get_the_title($lid) . "';\n";
		echo "apts[$i].floor = '" . get_post_meta($lid, '_rapt_apt_floor', true) . "';\n";
		echo "apts[$i].price = '$" . number_format(get_post_meta($lid, '_rapt_apt_price', true), 2) . "';\n";
		echo "apts[$i].deposit = '$" . number_format(get_post_meta($lid, '_rapt_apt_deposit', true), 2) . "';\n";
		echo "apts[$i].leaseterm = '" . get_post_meta($lid, '_rapt_apt_leaseterm', true) . "';\n";
		echo "apts[$i].pets = '" . get_post_meta($lid, '_rapt_apt_pets', true) . "';\n";
		echo "apts[$i].occupancy = '" . get_post_meta($lid, '_rapt_apt_occupancy', true) . "';\n";
		$mid = get_post_meta($lid, '_rapt_apt_model_id', true);
		echo "apts[$i].mid = '" . get_the_title($mid) . "';\n";
		echo "apts[$i].bedrooms = '" . get_post_meta($mid, '_rapt_model_bedroom', true) . "';\n";
		echo "apts[$i].bathrooms = '" . get_post_meta($mid, '_rapt_model_bathroom', true) . "';\n";
		echo "apts[$i].sqft = '" . number_format(get_post_meta($mid, '_rapt_model_sqft_min', true), 0) . "';\n";
		$i++;
	endwhile;
?>
		
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
			var headertxtlayer = new Kinetic.Layer();
			stage.add(headertxtlayer);
			
			var headbarfloortxt = new Kinetic.Text(
			{
				x: 0,
				y: 0,
				width: 200,
				text: '<?php echo $floortitle; ?>',
				fontSize: 30,
				fontWeight: 800,
				fontFamily: 'Calibri',
				fill: 'white'
			});
			headertxtlayer.add(headbarfloortxt);
			
			var headbarcalltxt1 = new Kinetic.Text(
			{
				x: 0,
				y: 0,
				width: 200,
				text: 'CALL US WITH QUESTIONS!',
				fontSize: 14,
				fontFamily: 'Calibri',
				fill: 'white',
				align: 'center'
			});
			headertxtlayer.add(headbarcalltxt1);
			
			var headbarcalltxt2 = new Kinetic.Text(
			{
				x: 0,
				y: 0,
				width: 200,
				text: '<?php echo $xmlident->Phone->Number; ?>',
				fontSize: 20,
				fontFamily: 'Calibri',
				fill: 'white',
				align: 'center'
			});
			headertxtlayer.add(headbarcalltxt2);
			
			
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
			headimgobj.src = '<?php echo home_url("/files/2013/07/Alta_Parallax_FLPL_Inner_Pages.jpg"); ?>';
			
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
				
				for(var i = 0; i < apts.length; i++)
				{
					var apt = apts[i];
					apt.kobj = new Kinetic.Circle(
					{
						name: 'aptdot',
						x: 0,
						y: 0,
						radius: 6,
						fill: '#696B2C',
						stroke: '#BBC263',
						strokeWidth: 3
					});
					layer.add(apt.kobj);
					
					apt.kobj.tween = new Kinetic.Tween(
					{
						node: apt.kobj,
						easing: Kinetic.Easings.EaseInOut,
						fillR: 44,
						fillG: 105,
						fillB: 124,
						strokeR: 137,
						strokeG: 189,
						strokeB: 210,
						duration: 0.01
					});
					
					apt.kobj.on('mouseover', function(i)
					{
						return (function()
						{
							$('body').css('cursor', 'pointer');
							apts[i].kobj.tween.play();
							SelectApartment(i);
							//apts[i].kobj.hovertween.play();
						});
					}(i));
					
					apt.kobj.on('mouseout', function(i)
					{
						return (function()
						{
							$('body').css('cursor', 'default');
							apts[i].kobj.tween.reverse();
							SelectApartment(-1);
						});
					}(i));
					
					apt.kobj.on('mouseup', function(i)
					{
						return (function(e)
						{
							if(e.which == 1)
							{
								window.location.href = '<?php echo home_url("/apartment/?rf=$pid&id="); ?>' + apts[i].id;
							}
						});
					}(i));
				}
				ResizeFloorplan();
				layer.draw();
			}
			fpimgobj.src='<?php echo $imgurl; ?>';
			
			function InitInfoBar()
			{
				var ibhead = new Kinetic.Rect(
				{
					x: 0,
					y: 0,
					width: 200,
					height: 100,
					fill: 'black',
					opacity: 0.8
				});
				infobargroup.add(ibhead);
				
				var ibtop = new Kinetic.Rect(
				{
					x: 0,
					y: 100,
					width: 200,
					height: 200,
					fill: '#868C36'
				});
				infobargroup.add(ibtop);
				
				var ibtoptxt = new Kinetic.Text(
				{
					x: 30,
					y: 130,
					width: 140,
					text: 'SELECT AN AVAILABLE APT FOR PRICING AND MORE INFO.',
					fontSize: 19,
					fontFamily: 'Calibri',
					fill: 'white'
				});
				infobargroup.add(ibtoptxt);
				
				var ibtopdot = new Kinetic.Circle(
				{
					x: 38,
					y: 250,
					radius: 6,
					fill: '#696B2C',
					stroke: '#BBC263',
					strokeWidth: 3
				});
				infobargroup.add(ibtopdot);
				
				var ibtopdottxt = new Kinetic.Text(
				{
					x: 55,
					y: 244,
					text: 'AVAILABLE APT',
					fontSize: 12,
					fontFamily: 'Calibri',
					fill: 'white'
				});
				infobargroup.add(ibtopdottxt);
				
				var ibselbg = new Kinetic.Rect(
				{
					x: 0,
					y: 300,
					width: 200,
					height: 375,
					fill: 'black',
					opacity: 0.7
				});
				infobargroup.add(ibselbg);
				
				var ibselheadbg = new Kinetic.Rect(
				{
					name: 'selhide',
					x: 0,
					y: 300,
					width: 200,
					height: 50,
					fill: '#1E4C5B',
					visible: true
				});
				infobargroup.add(ibselheadbg);
				
				var ibselheadtxt = new Kinetic.Text(
				{
					name: 'selhide',
					x: 30,
					y: 315,
					text: 'YOU\'VE SELECTED:',
					fontSize: 18,
					fontFamily: 'Calibri',
					fill: 'white',
					visible: true
				});
				infobargroup.add(ibselheadtxt);
				
				var ibseltitlebg = new Kinetic.Rect(
				{
					name: 'selhide',
					x: 0,
					y: 350,
					width: 200,
					height: 50,
					fill: '#163740',
					visible: true
				});
				infobargroup.add(ibseltitlebg);
				
				var ibseltitledot = new Kinetic.Circle(
				{
					name: 'selhide',
					x: 38,
					y: 374,
					radius: 6,
					fill: '#2C697C',
					stroke: '#89BDD2',
					strokeWidth: 3,
					visible: true
				});
				infobargroup.add(ibseltitledot);
				
				var ibseltitletxt = new Kinetic.Text(
				{
					id: 'seltitle',
					name: 'selhide',
					x: 57,
					y: 365,
					text: '',
					fontSize: 18,
					fontFamily: 'Calibri',
					fill: 'white',
					visible: true
				});
				infobargroup.add(ibseltitletxt);
				
				
				var ibseldatalbl_model = new Kinetic.Text(
				{
					name: 'selhide',
					x: 25,
					y: 420,
					text: 'MODEL:',
					fontSize: 12,
					fontFamily: 'Calibri',
					fill: '#C7C5C6',
					visible: true
				});
				infobargroup.add(ibseldatalbl_model);
				
				var ibseldatalbl_bedrooms = new Kinetic.Text(
				{
					name: 'selhide',
					x: 25,
					y: 445,
					text: 'BEDROOM(S):',
					fontSize: 12,
					fontFamily: 'Calibri',
					fill: '#C7C5C6',
					visible: true
				});
				infobargroup.add(ibseldatalbl_bedrooms);
				
				var ibseldatalbl_bathrooms = new Kinetic.Text(
				{
					name: 'selhide',
					x: 25,
					y: 470,
					text: 'BATHROOM(S):',
					fontSize: 12,
					fontFamily: 'Calibri',
					fill: '#C7C5C6',
					visible: true
				});
				infobargroup.add(ibseldatalbl_bathrooms);
				
				var ibseldatalbl_sqft = new Kinetic.Text(
				{
					name: 'selhide',
					x: 25,
					y: 495,
					text: 'SQUARE FEET:',
					fontSize: 12,
					fontFamily: 'Calibri',
					fill: '#C7C5C6',
					visible: true
				});
				infobargroup.add(ibseldatalbl_sqft);
				
				var ibseldatalbl_price = new Kinetic.Text(
				{
					name: 'selhide',
					x: 25,
					y: 520,
					text: 'PRICE:',
					fontSize: 12,
					fontFamily: 'Calibri',
					fill: '#C7C5C6',
					visible: true
				});
				infobargroup.add(ibseldatalbl_price);
				
				
				var ibseldata_model = new Kinetic.Text(
				{
					id: 'selmodel',
					name: 'selhide',
					x: 120,
					y: 420,
					text: '',
					fontSize: 12,
					fontFamily: 'Calibri',
					fontStyle: 'bold',
					fill: 'white',
					visible: true
				});
				infobargroup.add(ibseldata_model);
				
				var ibseldata_bedrooms = new Kinetic.Text(
				{
					id: 'selbedroom',
					name: 'selhide',
					x: 120,
					y: 445,
					text: '',
					fontSize: 12,
					fontFamily: 'Calibri',
					fontStyle: 'bold',
					fill: 'white',
					visible: true
				});
				infobargroup.add(ibseldata_bedrooms);
				
				var ibseldata_bathrooms = new Kinetic.Text(
				{
					id: 'selbathroom',
					name: 'selhide',
					x: 120,
					y: 470,
					text: '',
					fontSize: 12,
					fontFamily: 'Calibri',
					fontStyle: 'bold',
					fill: 'white',
					visible: true
				});
				infobargroup.add(ibseldata_bathrooms);
				
				var ibseldata_sqft = new Kinetic.Text(
				{
					id: 'selsqft',
					name: 'selhide',
					x: 120,
					y: 495,
					text: '',
					fontSize: 12,
					fontFamily: 'Calibri',
					fontStyle: 'bold',
					fill: 'white',
					visible: true
				});
				infobargroup.add(ibseldata_sqft);
				
				var ibseldata_price = new Kinetic.Text(
				{
					id: 'selprice',
					name: 'selhide',
					x: 120,
					y: 520,
					text: '',
					fontSize: 12,
					fontFamily: 'Calibri',
					fontStyle: 'bold',
					fill: 'white',
					visible: true
				});
				infobargroup.add(ibseldata_price);
				
				
				
				var ibfootbg = new Kinetic.Rect(
				{
					x: 0,
					y: 675,
					width: 200,
					height: 75,
					fill: 'black',
					opacity: 0.9
				});
				infobargroup.add(ibfootbg);
				
				var ibfootbtn = new Kinetic.Group(
				{
					x: 30,
					y: 686
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
						window.location.href = '<?php echo home_url("/floor-plans/"); ?>';
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
				
				infobargroup.get('#seltitle')[0].setText(apts[index].title);
				infobargroup.get('#selmodel')[0].setText(apts[index].mid);
				infobargroup.get('#selbedroom')[0].setText(apts[index].bedrooms);
				infobargroup.get('#selbathroom')[0].setText(apts[index].bathrooms);
				infobargroup.get('#selsqft')[0].setText(apts[index].sqft);
				infobargroup.get('#selprice')[0].setText(apts[index].price);
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
				fpimgko.setPosition(xoffset, yoffset);
				fpimgko.setSize(fpimgsw, fpimgsh);
				
				headbarfloortxt.setPosition(xoffset, 35);
				headertxtlayer.draw();
				
				for(var i = 0; i < apts.length; i++)
				{
					var apt = apts[i];
					apt.kobj.setPosition((apt.x * scale) + xoffset, (apt.y * scale) + yoffset);
					apt.kobj.setRadius(6);
				}
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
				
				headbarcalltxt1.setPosition(xoffset, 30);
				headbarcalltxt2.setPosition(xoffset, 50);
				
				ResizeFloorplan();
			}
			
			$(window).resize(OnResize);
			OnResize();
		});
	</script>
<?php
}

function rapt_floor_content()
{
	$pid = $_GET['id'];
	
	if(!is_numeric($pid) || get_post_type($pid) != 'apt-floor') return 'Invalid floor ID.';
	
	ob_start();
?>
<div id="maincanvas" style="overflow: hidden; top: 0; width: 100%; height: 750px;"></div>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

?>