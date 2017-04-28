<?php

add_shortcode('rapt_apt_title', 'rapt_apt_title');
add_shortcode('rapt_apt_content', 'rapt_apt_content');
add_shortcode('rapt_apt_tabs', 'rapt_apt_tabs');
add_action('wp_footer', 'rapt_apt_footer', 101);
add_action('wp_enqueue_scripts', 'rapt_aptview_enqueue_scripts');

function rz_apt_aptview_getunit()
{
	global $adata;
	$unit = false;
	if(isset($_GET['unit']))
		$unit = $adata->getUnit($_GET['unit']);
	elseif(isset($_GET['id']) && ($unit = $adata->getUnitByPID($_GET['id'])) !== false)
	{
		$url = add_query_arg('unit', $unit['id'], remove_query_arg('id'));
		wp_redirect($url, 301);
		exit;
	}
	
	if($unit === false || empty($unit['date']) || $unit['disabled']) return false;
	return $unit;
}

function rapt_apt_title()
{
	$unit = rz_apt_aptview_getunit();
	if($unit === false) return '';
	return $unit['id'];
}

function rapt_apt_footer()
{
	$unit = rz_apt_aptview_getunit();
	if($unit === false) return;

}

function rapt_apt_content()
{
	$unit = rz_apt_aptview_getunit();
	if($unit === false) return 'Apartment not found or is no longer available.';
	$settings = get_option('razz_apt_opt');
	$adata = AptData::Instance();
	$model = $adata->getModel($unit['model']);
	$aptdata = array();
	$aptdata['pid'] = $unit['pid'];
	$aptdata['apt'] = $unit['id'];
	$aptdata['modelid'] = $model['name'];
	$aptdata['floor'] = $unit['floor'];
	$aptdata['bed'] = $unit['rooms']['bedroom'];
	if($aptdata['bed'] == 0) $aptdata['bed'] = 'Studio';
	$aptdata['bath'] = $unit['rooms']['bathroom'];
	$aptdata['adate'] = $unit['date'];
	$aptdata['rent'] = getFormattedRent($unit);
	$aptdata['deposit'] = getFormattedDeposit($unit);
	$aptdata['fpid'] = $unit['model'];
	$aptdata['img'] = $model['image'];
	$aptdata['bldg'] = $adata->getIdent();
	
	if($settings['data_sqft_source'] == 'unit')
		$source = $unit;
	else
		$source = $model;
	
	$sqftmin = $source['sqft']['min'];
	$sqftmax = $source['sqft']['max'];
	
	if($sqftmin != $sqftmax)
		$aptdata['sqft'] = "{$sqftmin} - {$sqftmax}";
	else
		$aptdata['sqft'] = $sqftmin;
	
	$applyurl = $settings['uv_applyurl'];
	foreach($aptdata as $key=>$value)
	{
		$applyurl = str_replace("%{$key}%", $value, $applyurl);
	}
	$aptdata['applyurl'] = $applyurl;
	
	ob_start();
?>
<style>
.headqmain
{

}

#headqswrapper
{
	display: none;
	width: 100%;
	text-align: center;
	height: 100px;
}

#headqs
{
	display: inline-block;
	text-align: center;
	color: white;
	width: 100%;
	height: 100px;
	background-color: rgba(0, 0, 0, 0.8);
}

#mmwrapper
{
	width: 100%;
	float: left;
}

#mmcontent
{
	margin-right: 200px;
	text-align: center;
}

#mmbarwrapper
{
	text-align: center;
}

#mmbar
{
	float: left;
	width: 200px;
	height: 600px;
	margin-left: -200px;
}

#mmbtns > ul
{
	list-style-type: none;
	padding-top: 22px;
	margin: 0;
}

#mmbtns > ul > li > a
{
	display: inline-block;
	width: 140px;
	height: 33px;
	line-height: 33px;
	background-color: #626061;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	margin: 4px 0;
	color: #f7f7f7;
	font-size: 12px;
}

#mmbtns > ul > li > a:hover
{
	background-color: #525051;
}

#mmdata
{
	text-align: center;
	margin-top: 25px;
}

#mmdtable
{
	display: inline-block;
	width: 140px;
}

#mmdtable > tbody > tr > td
{
	text-align: left;
	font-size: 11px;
	line-height: 15px;
	border: none;
	padding: 0;
}

.mmdtitle
{
	width: 85px;
	color: #626061;
}

.mmdcontent
{
	color: #f7f7f7;
}

#backbtn
{
	display: inline-block;
	width: 140px;
	height: 38px;
	line-height: 12px;
	padding-top: 15px;
	margin: 18px 0;
	background-color: #626061;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	border-radius: 3px;
	margin: 18px 0;
	color: #f7f7f7;
	font-size: 12px;
}

.stabs
{
	display: none;
	text-align: center;
	padding-top: 20px;
}

.stabs > ul > li > a
{
	width: 100%;
	height: 38px;
	line-height: 38px;
	display: inline-block;
	color: white;
}

@media only screen and (max-width: 1181px)
{
	iframe
	{
		width: 780px;
	}
}

@media only screen and (max-width: 934px)
{
	.parallax section
	{
		min-height: 100px;
	}

	.headqmain
	{
		display: none;
	}
	
	#headqswrapper
	{
		display: inline-block;
	}
	
	#mmcontent
	{
		margin-right: 0;
	}
	
	#mmbar
	{
		margin-left: 0;
		float: none;
		display: inline-block;
	}
	
	.tabs
	{
		display: none;
	}
	
	.stabs
	{
		display: block;
	}
	
}
</style>
<div id="mmwrapper">
	<div id="mmcontent">
		<?php /*<img src="<?php	$fpurl = wp_get_attachment_image_src(get_post_thumbnail_id($mid), 'full'); echo $fpurl[0]; ?>" />*/ ?>
		<img src="<?php echo $aptdata['img']; ?>" style="padding: 20px 0; max-height: 600px;" />
	</div>
</div>
<div id="mmbarwrapper">
	<div id="mmbar">
		<div style="width: 200px; height: 100px; background-color: #161415;">
			<!--<img src="<?php echo plugins_url('img/Location_IconB.png', __FILE__); ?>" style="width: 14px; height: 23px; margin-top: 11px;" />-->
			<i class="fa fa-map-marker fa-lg" style="color: #fff; margin-top: 17px;"></i>
			<p style="padding-top: 6px; line-height: 14px; color: white; margin: 0; font-size: 13px;"><strong><?php echo strtoupper($aptdata['bldg']['MarketingName']); ?></strong><br /><?php echo strtoupper($aptdata['bldg']['Address']['Address']); ?><br /><?php echo strtoupper($aptdata['bldg']['Address']['City']); ?>, <?php echo strtoupper($aptdata['bldg']['Address']['State']); ?> <?php echo $aptdata['bldg']['Address']['Zip']; ?></p>
		</div>
		<div id="mmdatawrapper" style="width: 200px; height: 410px; background-color: #262223;">
			<div id="mmbtns">
				<ul>
					<li><a href="<?php echo $aptdata['applyurl']; ?>" target="_blank">APPLY ONLINE</a></li>
					<li><a href="<?php echo "?action=pdf&unit={$unit['id']}"; ?>" target="_blank">PRINT PDF</a></li>
					<li><a href="<?php echo home_url("/contact"); ?>">CONTACT US</a></li>
<?php
	$msgsubject = rapt_parse_email_content($settings['uv_email_subject'], $aptdata);
	$msgbody = rapt_parse_email_content($settings['uv_email_body'], $aptdata);
?>
					<li><a href="#" onclick="window.open('mailto:?subject=<?php echo $msgsubject; ?>&body=<?php echo $msgbody; ?>', '_blank');">EMAIL A FRIEND</a></li>
				</ul>
			</div>
			<div id="mmdata">
				<table id="mmdtable">
					<tbody>
						<tr>
							<td class="mmdtitle">APT:</td>
							<td class="mmdcontent"><?php echo $aptdata['apt']; ?></td>
						</tr>
						<tr>
							<td class="mmdtitle">APT TYPE:</td>
							<td class="mmdcontent"><?php echo $aptdata['modelid']; ?></td>
						</tr>
						<tr>
							<td class="mmdtitle">FLOOR:</td>
							<td class="mmdcontent"><?php echo $aptdata['floor']; ?></td>
						</tr>
						<tr>
							<td class="mmdtitle">BEDROOMS:</td>
							<td class="mmdcontent"><?php echo $aptdata['bed']; ?></td>
						</tr>
						<tr>
							<td class="mmdtitle">BATHROOMS:</td>
							<td class="mmdcontent"><?php echo $aptdata['bath']; ?></td>
						</tr>
						<tr>
							<td class="mmdtitle">SQ. FEET:</td>
							<td class="mmdcontent"><?php echo $aptdata['sqft']; ?></td>
						</tr>
						<tr>
							<td class="mmdtitle">PRICE:</td>
							<td class="mmdcontent"><?php echo $aptdata['rent']; ?></td>
						</tr>
						<tr>
							<td class="mmdtitle">DEPOSIT:</td>
							<td class="mmdcontent"><?php echo $aptdata['deposit']; ?></td>
						</tr>
						<tr>
							<td class="mmdtitle">LEASE TERM:</td>
							<td class="mmdcontent"><?php echo $settings['uv_leasingterm']; ?></td>
						</tr>
						<tr>
							<td class="mmdtitle">MOVE-IN:</td>
							<td class="mmdcontent"><?php echo $aptdata['adate']; ?></td>
						</tr>
						
					</tbody>
				</table>
			</div>
		</div>
		<div style="height: 90px; background-color: #161415;">
			<a href="<?php echo ($_GET['rf'] == 'floor') ? "../floor-view/?floor={$aptdata['floor']}" : "../floor-plans/"; ?>" id="backbtn">GO BACK TO SELECT<br />ANOTHER APT</a>
		</div>
	</div>
</div>
<script type="text/javascript">
	jQuery(function($)
	{
		$('section').css('padding', 0);
	});
</script>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

function rapt_parse_email_content($input, $aptdata)
{
	$output = $input; //search, replace
	$output = str_replace('[br]', "\r\n", $output);
	$output = str_replace('[prop_name]', $aptdata['bldg']['MarketingName'], $output);
	$output = str_replace('[unit_num]', $aptdata['apt'], $output);
	$output = str_replace('[unit_beds]', $aptdata['bed'], $output);
	$output = str_replace('[unit_baths]', $aptdata['bath'], $output);
	$output = str_replace('[unit_sqft]', $aptdata['sqft'], $output);
	$output = str_replace('[unit_url]', home_url("/apartment/?unit=" . $aptdata['apt']), $output);
	$output = rawurlencode($output);
	return $output;
}

function rapt_apt_tabs()
{
	ob_start();
?>
<div class="stabs" style="font-family: Oswald, sans-serif;">
	<ul>
		<li><a href="<?php echo home_url("/gallery/"); ?>" style="background-color: #9E340D;">IMAGE GALLERY</a></li>
		<li><a href="<?php echo home_url("/video-tour/"); ?>" style="background-color: #879037;">VIDEO TOUR</a></li>
		<li><a href="<?php echo home_url("/amenities/"); ?>" style="background-color: #2B687D;">BUILDING AMENITIES</a></li>
		<li><a href="<?php echo home_url("/neighborhood/"); ?>" style="background-color: #BD9F63;">NEIGHBORHOOD</a></li>
	</ul>
</div>
<div class="tabs" style="clear: both; min-height: 1000px; padding-top: 20px;">
	<ul id="mtabs" class="tabs-nav">
		<li><a href="#tab1" style="background-color: #9E340D; font-family: Oswald, sans-serif;">IMAGE GALLERY</a></li>
		<li><a href="#tab2" style="background-color: #879037; font-family: Oswald, sans-serif;">VIDEO TOUR</a></li>
		<li><a href="#tab3" style="background-color: #2B687D; font-family: Oswald, sans-serif;">BUILDING AMENITIES</a></li>
		<li><a href="#tab4" style="background-color: #BD9F63; font-family: Oswald, sans-serif;">NEIGHBORHOOD</a></li>
	</ul>
	<div class="tabs-container">
		<div id="tab1" class="tab-content" style="background-color: #9E340D;">
			<?php
				$pgallery = get_post(1394);
				echo do_shortcode($pgallery->post_content);
			?>
		</div>
		<div id="tab2" class="tab-content" style="background-color: #879037;">
			<?php
				$pgallery = get_post(414);
				echo do_shortcode($pgallery->post_content);
			?>
		</div>
		<div id="tab3" class="tab-content" style="background-color: #2B687D;">
			<?php
				$pgallery = get_post(1311);
				echo do_shortcode($pgallery->post_content);
			?>
		</div>
		<div id="tab4" class="tab-content" style="background-color: #BD9F63;">
			<?php
				$pgallery = get_post(1398);
				echo do_shortcode($pgallery->post_content);
			?>
		</div>
	</div>
</div>
<script type="text/javascript">
	jQuery(function($)
	{
	
		var win = $(window),
			hd = $('.header_inner'),
			hh = ((hd.parent().css('position') == 'fixed') ? hd.height()+12 : 0),
			to = -hh<?php echo (is_admin_bar_showing() ? " - 28" : ""); ?>;
		
		
		$('#mtabs li a').click(function()
		{
			$.smoothScroll(
			{
				speed: 500,
				offset: to,
				scrollTarget: '#mtabs'
			});
		});
	});
</script>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

function rapt_aptview_enqueue_scripts()
{
	wp_register_script('smoothscroll', plugins_url('js/jquery.smooth-scroll.min.js', __FILE__), array('jquery'), false, true);
	wp_enqueue_script('smoothscroll');
}

?>