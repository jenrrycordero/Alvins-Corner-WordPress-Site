<?php
add_action('admin_menu', 'rapt_rentcafe_admin_menu');
add_action('admin_init', 'rapt_rentcafe_reg_settings');
add_action('rapt_rentcafe_do_import', 'rapt_rentcafe_do_import');
add_shortcode('rapt_amenities', 'rapt_amenities');
add_shortcode('rapt_pets', 'rapt_pets');
add_shortcode('rapt_apt_rc_phone', 'rapt_apt_rc_phone');
add_shortcode('rapt_apt_rc_address', 'rapt_apt_rc_address');
add_shortcode('rapt_apt_rc_gmap', 'rapt_apt_rc_gmap');
add_shortcode('rapt_apt_rc_walkscore', 'rapt_apt_rc_walkscore');
add_shortcode('rapt_apt_rc_city', 'rapt_apt_rc_city');
add_shortcode('rapt_apt_rc_state', 'rapt_apt_rc_state');
add_shortcode('rapt_apt_rc_zip', 'rapt_apt_rc_zip');

function rapt_apt_rc_gmap($atts = array())
{
	global $adata;
	$settings = get_option('razz_apt_opt');
	$settings['gmap_area_types'] = "bank,bar,cafe,movie_theater,restaurant";
		
	try
	{
		//$xml = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_ident', true));
		$addr = $adata->_('ident.Address');
		$params = shortcode_atts(array(
			'title'			=> $adata->_('ident.MarketingName'),
			'address'		=> "{$addr['Address']}<br />{$addr['City']}, {$addr['State']} {$addr['Zip']}",
			'lat'			=> $addr['lat'],
			'lon'			=> $addr['lon'],
			'zoom'			=> '17', //TODO: add setting
			'area'			=> 0,
			'showtransit'	=> 0,
			'height'		=> '400px',
			'width'			=> '100%'
		), $atts);
		
		$mapsapi = "https://maps.googleapis.com/maps/api/js?v=3.15&sensor=false";
		if($params['area'])
			$mapsapi .= "&libraries=places";
		
		ob_start();
?>
<style>
#razz_gmap{
	position: relative;
	overflow: hidden;
	height: <?php echo $params['height']; ?>;
	width: <?php echo $params['width']; ?>;
}
#razz_gmap_wrapper{
	float: left;
	width: 100%;
}
#razz_gmap_container{
	position: relative;
	margin-left: 250px;
	z-index: 1;
}
#razz_gmap_map{
	width: 100%;
	height: <?php echo $params['height']; ?>;
}
#razz_gmap_content{
	position: relative;
	float: left;
	width: 250px;
	height: 100%;
	margin-left: -100%;
	background: #F2F2F2;
}
#razz_gmap_content_container{
	position: relative;
	left: 0;
	width: 200%;
	height: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	padding-bottom: 50px;
	line-height: 0;
	-webkit-transition: left 0.7s ease;
			transition: left 0.7s ease;
}
#razz_gmap_content.open #razz_gmap_content_container{
	left: -100%;
}
#razz_gmap_content_container > div{
	display: inline-block;
	float: left;
	width: 50%;
	height: 100%;
	line-height: 1;
}
ul#razz_gmap_content_categories{
	list-style-type: none;
	margin-left: 0;
}
ul#razz_gmap_content_categories li{
	cursor: pointer;
}
ul#razz_gmap_content_categories li:hover{
	background: #ddd;
}
#razz_gmap_content_footer{
	position: absolute;
	width: 100%;
	height: 50px;
	bottom: 0;
}
#razz_gmap_loadmore{
	display: inline-block;
	width: 100%;
	line-height: 50px;
	text-align: center;
	background: #88927c; /*TODO: take from settings*/
	color: #fff;
}
#razz_gmap_loadmore:hover{
	background: #6F7863; /*TODO: take from settings*/
}
#razz_gmap_loadmore.disabled{
	background: #D9DDD5; /*TODO: take from settings*/
	cursor: default;
}
.list_circle{
	display: inline-block;
	width: 8px;
	height: 8px;
	border-radius: 50%;
	border: 1px solid #000;
	margin: 0 10px;
}
.list_circle.off{
	background: transparent !important;
}
#razz_gmap_map img{
	max-width:none!important;
	background:none!important;
}
@media screen and (max-width: 767px){
	#razz_gmap_wrapper{
		float: none;
	}
	#razz_gmap_container{
		margin-left: 0;
	}
	#razz_gmap_map{
		width: 100%;
		height: 200px;
	}
	#razz_gmap_content{
		width: 100%;
		height: 200px;
		margin-left: 0;
	}
}
</style>
<script type="text/javascript" src="<?php echo $mapsapi; ?>"></script>
<div id="razz_gmap">
	<?php if($params['area']): ?>
	<div id="razz_gmap_wrapper">
		<div id="razz_gmap_container">
			<div id="razz_gmap_map"></div>
		</div>
	</div>
	<div id="razz_gmap_content">
		<div id="razz_gmap_content_container">
			<div id="razz_gmap_content_main">
				<ul id="razz_gmap_content_categories"></ul>
			</div>
			<div id="razz_gmap_content_details">Test2</div>
		</div>
		<div id="razz_gmap_content_footer"><a href="#" id="razz_gmap_loadmore" class="disabled">Load More</a></div>
	</div>
	<?php else: ?>
	<div id="razz_gmap_map" style="width: 100%; height: 100%;"></div>
	<?php endif; ?>
	
</div>
<script type="text/javascript">
jQuery(function($)
{
	function init_gmap(){
		var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test( (window.navigator.userAgent||window.navigator.vendor||window.opera) );
		if(google.maps.razz === undefined) google.maps.razz = {};
		area_types = <?php echo json_encode(array_map('trim', explode(',', $settings['gmap_area_types']))); ?>;
		area_colors = ["#e62937", "#00e430", "#701f85", "#0052ac", "#00752c", "#ffa100"];
		places = {
			bank: {
				name: "Banks",
				items: {}
			},
			bar: {
				name: "Bars",
				items: {}
			},
			cafe: {
				name: "Cafes",
				items: {}
			},
			movie_theater: {
				name: "Movie Theaters",
				items: {}
			},
			pharmacy: {
				name: "Pharmacies",
				items: {}
			},
			restaurant: {
				name: "Restaurants",
				items: {}
			}
		};
		var i = 0;
		$.each(area_types, function(key, value)
		{
			places[value].active = true;
			places[value].color = area_colors[i];
			i++;
		});
		
		
		pos = new google.maps.LatLng(<?php echo $params['lat']; ?>, <?php echo $params['lon']; ?>);
		var myStyles = [
			{
				stylers: [
					{hue: '#b2c0a3'},
					{gamma: 0.5},
					{weight: 0.5}
				]
			},
			{
				featureType: 'water',
				stylers: [
					{ color: '#003A63' }
				]
			}
		];
		var myOptions = {
			scrollwheel: false,
			zoom:<?php echo $params['zoom']; ?>,
			center: pos,
			draggable: !isMobile,
			//styles: myStyles,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		map = new google.maps.Map(document.getElementById("razz_gmap_map"), myOptions);
		marker = new google.maps.Marker(
		{
			map: map,
			position: pos,
			title: '<?php echo str_replace("'", "\\'", $params['title']); ?>'
		});
		infowindow = new google.maps.InfoWindow(
		{
			content: ''
		});
		infowindow.open(map, marker);
		google.maps.event.addListener(marker, "click", function(){
			infowindow.setContent('<b><?php echo str_replace("'", "\\'", $params['title']); ?></b><br/><?php echo $params['address']; ?>');
			infowindow.open(map, marker);
		});
		jQuery(window).on('resize', function()
		{
			map.setCenter(pos);
		});
		
		<?php if($params['showtransit']): ?>
		var transitl = new google.maps.TransitLayer();
		transitl.setMap(map);
		<?php endif; ?>
		
		<?php if($params['area']): ?>
		//performSearch();
		
		google.maps.event.addListener(map, 'bounds_changed', function()
		{
			var $btn = $('#razz_gmap_loadmore');
			if($btn.hasClass('disabled'))
			{
				$btn.removeClass('disabled');
				$btn.off('click');
				$btn.one('click', function(e)
				{
					e.preventDefault();
					$btn.addClass('disabled');
					performSearch();
				});
			}
		});
		google.maps.event.addListenerOnce(map, 'bounds_changed', performSearch);
		//google.maps.event.addListener(map, 'zoom_changed', performSearch);
		<?php endif; ?>
	}
	
	<?php if($params['area']): ?>
	var $gmap_cmain = $('#razz_gmap_content_main'),
		$gmap_cdetails = $('#razz_gmap_content_main');
	function performSearch()
	{
		if(google.maps.razz.ps === undefined)
			google.maps.razz.ps = new google.maps.places.PlacesService(map);
			
		var request = {
			bounds: map.getBounds(),
			//location: pos,
			//radius: 500,
			//rankBy: 1,
			types: area_types
		};
		
		google.maps.razz.ps.nearbySearch(request, function(results, status, page)
		{
			if(status != google.maps.places.PlacesServiceStatus.OK)
			{
				console.error(status);
				return;
			}
			for(var i = 0, res; res = results[i]; i++)
			{
				for(var j = 0; j < res.types.length; j++)
				{
					if(!places[res.types[j]]) continue;
					if(places[res.types[j]].items[res.id]) continue;
					res.marker = createMarker(res);
					res.marker.item = res;
					places[res.types[j]].items[res.id] = res;
				}
			}
			updateList();
			if(page.hasNextPage)
			{
				var $btn = $('#razz_gmap_loadmore');
				$btn.removeClass('disabled');
				$btn.off('click');
				$btn.one('click', function(e)
				{
					e.preventDefault();
					$btn.addClass('disabled');
					page.nextPage();
				});
			}
		});
	}
	
	$('#razz_gmap_content_categories').on('mouseenter', 'li', function(e)
	{
		var $this = $(this),
			cat = $this.data('cat');
		bounceCategory(cat, true);
	});
	$('#razz_gmap_content_categories').on('mouseout', 'li', function(e)
	{
		var $this = $(this),
			cat = $this.data('cat');
		bounceCategory(cat, false);
	});
	
	$('#razz_gmap_content_categories').on('click', '.list_circle', function(e)
	{
		e.preventDefault();
		var $this = $(this),
			$li = $this.closest('li'),
			cat = $li.data('cat');
		$this.toggleClass('off');
		if($this.hasClass('off'))
			hideCategory(cat);
		else
			showCategory(cat);
	});
	
	function bounceCategory(cat, bounce)
	{
		var anim = bounce ? google.maps.Animation.BOUNCE : null;
		$.each(places[cat].items, function(k, v)
		{
			v.marker.setAnimation(anim);
		});
	}
	
	function hideCategory(cat)
	{
		$.each(places[cat].items, function(k, v)
		{
			v.marker.setMap(null);
		});
	}
	
	function showCategory(cat)
	{
		$.each(places[cat].items, function(k, v)
		{
			v.marker.setMap(map);
		});
	}
	
	function updateList()
	{
		var $ul = $('#razz_gmap_content_categories');
		$ul.html('');
		
		$.each(places, function(k, v)
		{
			var size = Object.size(v.items);
			if(!v.active || size < 1) return true;
			$ul.append($('<li/>').attr('data-cat', k).append($('<div/>').addClass('list_circle').css('background', v.color)).append(v.name + ' ('+ size +')'));
		});
		
	}
	
	function createMarker(place)
	{
		var color = 'red';
		for(var i = 0, type; type = area_types[i]; i++)
		{
			var a = -1;
			if((a = place.types.indexOf(type)) > -1)
			{
				color = area_colors[i];
				break;
			}
		}
		var marker = new google.maps.Marker(
		{
			map: map,
			animation: google.maps.Animation.DROP,
			position: place.geometry.location,
			title: place.name,
			icon: {
				path: google.maps.SymbolPath.CIRCLE,
				fillColor: color,
				fillOpacity: 0.8,
				scale: 4,
				strokeWeight: 1
			}
		});
		
		google.maps.event.addListener(marker, 'click', function() {
			google.maps.razz.ps.getDetails(place, function(res, status) {
				if(status != google.maps.places.PlacesServiceStatus.OK)
				{
					console.error(status);
					return;
				}
				infowindow.setContent('<b>'+res.name+'</b><br/>'+res.formatted_address);
				infowindow.open(map, marker);
			});
		});
		return marker;
	}
	<?php endif; ?>
	google.maps.event.addDomListener(window, 'load', init_gmap);
	
	Object.size = function(obj) {
		var size = 0, key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) size++;
		}
		return size;
	};
});
</script>
<?php
		return ob_get_clean();
	}
	catch(Exception $ex)
	{
		return "";
	}
}
function rapt_apt_rc_walkscore($atts = array())
{
	try
	{
		$xml = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_ident', true));
		$params = shortcode_atts(array(
			'address'	=> "{$xml->Address->Address1} {$xml->Address->City}, {$xml->Address->State} {$xml->Address->Zip}"
		), $atts);
		ob_start();
?>
<script type='text/javascript'>
var ws_wsid = '74a4499639c0493ea284f91794ced635';
var ws_address = '<?php echo $params['address']; ?>';
var ws_width = '950';
var ws_height = '500';
var ws_layout = 'horizontal';
var ws_commute = 'true';
var ws_transit_score = 'true';
var ws_map_modules = 'all';
var ws_hide_scores_below = 90;
</script>
<style type='text/css'>#ws-walkscore-tile{position:relative;text-align:left}#ws-walkscore-tile *{float:none;}#ws-footer a,#ws-footer a:link{font:11px/14px Verdana,Arial,Helvetica,sans-serif;margin-right:6px;white-space:nowrap;padding:0;color:#000;font-weight:bold;text-decoration:none}#ws-footer a:hover{color:#777;text-decoration:none}#ws-footer a:active{color:#b14900}</style><div id='ws-walkscore-tile'><div id='ws-footer' style='position:absolute;top:482px;left:8px;width:938px'><form id='ws-form'><a id='ws-a' href='http://www.walkscore.com/' target='_blank'>What's Your Walk Score?</a><input type='text' id='ws-street' style='position:absolute;top:0px;left:170px;width:736px' /><input type='image' id='ws-go' src='http://cdn2.walk.sc/images/tile/go-button.gif' height='15' width='22' border='0' alt='get my Walk Score' style='position:absolute;top:0px;right:0px' /></form></div></div>
<script type='text/javascript' src='http://www.walkscore.com/tile/show-walkscore-tile.php'></script>
<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	catch(Exception $ex)
	{
		return "";
	}
}

function rapt_apt_rc_address()
{
	try
	{
		$xml = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_ident', true));
		return $xml->Address->Address1;
	}
	catch(Exception $ex)
	{
		return "";
	}
}

function rapt_apt_rc_city()
{
	try
	{
		$xml = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_ident', true));
		return $xml->Address->City;
	}
	catch(Exception $ex)
	{
		return "";
	}
}

function rapt_apt_rc_state()
{
	try
	{
		$xml = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_ident', true));
		return $xml->Address->State;
	}
	catch(Exception $ex)
	{
		return "";
	}
}

function rapt_apt_rc_zip()
{
	try
	{
		$xml = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_ident', true));
		return $xml->Address->Zip;
	}
	catch(Exception $ex)
	{
		return "";
	}
}

function rapt_apt_rc_phone($atts = array())
{
	try
	{
		$params = shortcode_atts(array(
			'format'	=> "(xxx) yyy-zzzz"
		), $atts);
		$format = str_replace(array('xxx', 'yyy', 'zzzz'), array('$1', '$2', '$3'), $params['format']);
		
		$xml = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_ident', true));
		$phone = $xml->Phone->Number;
		$phone = preg_replace("/[^\d]/", "", $phone);
		$phone = preg_replace("/^1?(\d{3})(\d{3})(\d{4})$/", $format, $phone);
		return $phone;
	}
	catch(Exception $ex)
	{
		return "";
	}
}

function CapitalSpace($input)
{
	$count = strlen($input);
	$output = "";
	$lastchr = "";
	for($i = 0; $i < $count; $i++)
	{
		$chr = mb_substr($input, $i, 1, "UTF-8");
		if((mb_strtolower($chr, "UTF-8") != $chr) || (is_numeric($chr) && !is_numeric($lastchr)))
			$output .= " ";
		$output .= $chr;
		$lastchr = $chr;
	}
	return trim($output);
}

function rapt_amenities($atts, $content = "[content]")
{
	$params = shortcode_atts(array(
		'type' => 'community,apartment',
		'divide' => '0',
		'section' => '0'
	), $atts);
	
	$content = do_shortcode(trim($content));
	
	$xml = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_amenities', true));
	
	$amenities = array();
	
	$types = explode(",", $params['type']);
	
	if(in_array('community', $types))
	{
		foreach($xml->Community->children() as $key => $val)
		{
			if($val == "true")
			{
				
				$amenities[] = getAmenityName($key);
			}
		}
		
		foreach($xml->General as $general)
		{
			if($general->AdditionalAmenity == "Community Amenity")
			{
				$amenities[] = $general->AmenityName;
			}
		}
	}
	
	if(in_array('apartment', $types))
	{
		foreach($xml->Floorplan->children() as $key => $val)
		{
			if($val == "true")
			{
				$amenities[] = getAmenityName($key);
			}
		}
		
		foreach($xml->General as $general)
		{
			if($general->AdditionalAmenity == "Floorplan Amenity")
			{
				$amenities[] = $general->AmenityName;
			}
		}
	}
	
	sort($amenities, SORT_STRING);
	
	$bounds = getBounds(sizeof($amenities), $params['divide'], $params['section']);
	
	for($i = $bounds['start']; $i <= $bounds['end']; $i++)
	{
		$code .= str_replace('[content]', $amenities[$i], $content);
	}
	
	return $code;
}

function rapt_pets($atts, $content)
{
	$params = shortcode_atts(array(
		'divide' => '0',
		'section' => '0'
	), $atts);
	
	$content = trim($content);
	
	$pets = array();
	try
	{
		$xmlpolicy = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_policy', true));
	}
	catch(Exception $ex)
	{
		return "";
	}
	
	foreach($xmlpolicy->Pet as $pet)
	{
		$type = $pet->attributes()->type;
		$allowed = ($pet->attributes()->allowed == "true");
		
		$msg = $content;
		$msg = str_replace('[type]', $type, $msg);
		$msg = str_replace('[allowed]', $allowed, $msg);
		$msg = str_replace('[max]', $pet->MaxCount, $msg);
		$msg = str_replace('[comment]', $pet->Comment, $msg);
		$msg = str_replace('[deposit]', '$' . $pet->Deposit, $msg);
		$msg = str_replace('[fee]', '$' . $pet->Fee, $msg);
		$msg = str_replace('[fee_min]', '$' . $pet->FeeRange->attributes()->min, $msg);
		$msg = str_replace('[fee_max]', '$' . $pet->FeeRange->attributes()->max, $msg);
		$msg = str_replace('[rent]', '$' . $pet->Rent, $msg);
		$msg = str_replace('[weight]', $pet->Weight, $msg);
		$msg = str_replace('[restrictions]', $pet->Restrictions, $msg);
		$msg = str_replace('[petcare]', $pet->PetCare, $msg);
		
		$pets[] = $msg;
	}
	
	sort($pets, SORT_STRING);
	
	$bounds = getBounds(sizeof($pets), $params['divide'], $params['section']);
	
	for($i = $bounds['start']; $i <= $bounds['end']; $i++)
	{
		$code .= $pets[$i];
	}
	
	return $code;
}

function getBounds($total, $divide, $section)
{
	$result = array();
	
	if($divide == 0 || $section == 0)
	{
		$result['start'] = 0;
		$result['end'] = $total - 1;
	}
	else
	{
		$v = floor($total / $divide);
		$r = $total % $divide;
		
		$result['start'] = ($section - 1) * $v + min($r, $section - 1);
		$result['end'] = ($section * $v + min($r, $section)) - 1;
	}
	return $result;
}

function getAmenityName($id)
{
	switch($id)
	{
		case "ClubHouse":
		{
			return "Clubhouse";
			break;
		}
		case "CoverPark":
		{
			return "Covered Parking";
			break;
		}
		case "HighSpeed":
		{
			return "High Speed Internet";
			break;
		}
		case "Laundry":
		{
			return "Laundry Facilities";
			break;
		}
		case "PlayGround":
		{
			return "Playground";
			break;
		}
		case "Spa":
		{
			return "Spa/Hot Tub";
			break;
		}
		case "StorageSpace":
		{
			return "Extra Storage";
			break;
		}
		case "Transportation":
		{
			return "Public Transportation";
			break;
		}
		case "Availability24Hours":
		{
			return "Availability 24 Hours";
			break;
		}
		case "OnSiteMaintenance":
		{
			return "On-Site Maintenance";
			break;
		}
		case "OnSiteManagement":
		{
			return "On-Site Management";
			break;
		}
		case "Racquetball":
		{
			return "Racquetball Court";
			break;
		}
		case "RecRoom":
		{
			return "Recreation Room";
			break;
		}
		case "TVLounge":
		{
			return "TV Lounge";
			break;
		}
		case "AdditionalStorage":
		{
			return "Extra Storage";
			break;
		}
		case "CableSat":
		{
			return "Cable Ready";
			break;
		}
		case "DishWasher":
		{
			return "Dishwasher";
			break;
		}
		case "WheelChair":
		{
			return "Wheelchair Access";
			break;
		}
		case "WD_Hookup":
		{
			return "W/D Hookup";
			break;
		}
		case "Alarm":
		{
			return "Security Alarm";
			break;
		}
		case "ControlledAccess":
		{
			return "Controlled Access/Gated";
			break;
		}
		default:
		{
			return CapitalSpace($id);
			break;
		}
	}
}

function rapt_rentcafe_admin_menu()
{
	add_management_page
	(
		"RentCafe Import",
		"RentCafe Import",
		"manage_options",
		"rapt_rentcafe_import",
		"rapt_rentcafe_import_display"
	);
	
	add_menu_page
	(
		"RentCafe",
		"RentCafe",
		"manage_options",
		"rapt_rentcafe_settings",
		null
	);
	
	add_submenu_page
	(
		"rapt_rentcafe_settings",
		"RentCafe Settings",
		"Settings",
		"manage_options",
		"rapt_rentcafe_settings",
		"rapt_rentcafe_settings_display"
	);
	
	add_submenu_page
	(
		"rapt_rentcafe_settings",
		"RentCafe Imported Data",
		"Imported Data",
		"manage_options",
		"rapt_rentcafe_data",
		"rapt_rentcafe_data_display"
	);
}

function rapt_rentcafe_data_display()
{
?>
<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2>RentCafe Imported Data</h2><br /><br />
<?php
	echo '<strong>Management:</strong><br />';
	try
	{
		$xmlmanage = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_management', true));
?>
	<?php echo $xmlmanage->Name; ?><br />
	<?php echo $xmlmanage->Address->Address1; ?><br />
	<?php echo $xmlmanage->Address->City; ?>, <?php echo $xmlmanage->Address->State; ?> <?php echo $xmlmanage->Address->Zip; ?><br />
	<?php echo $xmlmanage->WebSite; ?><br />
<?php
	}
	catch(Exception $ex)
	{
		echo "Could not fetch Management Data";
	}
	echo "<br />";
	
	echo '<strong>Identity:</strong><br />';
	try
	{
		$xmlident = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_ident', true));
?>
	<?php echo $xmlident->MarketingName; ?><br />
	<?php echo $xmlident->Address->Address1; ?><br />
	<?php echo $xmlident->Address->City; ?>, <?php echo $xmlident->Address->State; ?> <?php echo $xmlident->Address->Zip; ?><br />
	<?php echo $xmlident->WebSite; ?><br />
	<?php echo $xmlident->Phone->Number; ?><br />
	<?php echo $xmlident->Email; ?><br />
<?php
	}
	catch(Exception $ex)
	{
		echo "Could not fetch Identity Data";
	}
	echo "<br />";
	echo '<strong>PropertyID:</strong>' . get_post_meta(1, '_rapt_rentcafe_data_propid', true) . '<br /><br />';
	echo '<strong>Available Beds:</strong>' . get_post_meta(1, '_rapt_beds', true) . '<br />';
	echo '<strong>Available Baths:</strong>' . get_post_meta(1, '_rapt_baths', true) . '<br /><br />';
	
	
	echo '<strong>Policy:</strong><br />';
	try
	{
		$xmlpolicy = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_policy', true));
		foreach($xmlpolicy->Pet as $pet)
		{
			$type = $pet->attributes()->type;
			$allowed = ($pet->attributes()->allowed == "true");
			
			if(!$allowed)
			{
				echo "$type <strong>not</strong> allowed.<br />";
			}
			else
			{
				echo "Maximum <strong>$pet->MaxCount $type(s)</strong>. \$$pet->Deposit deposit, \$$pet->Rent additional rent each.<br />";
			}
		}
	}
	catch(Exception $ex)
	{
		echo "Could not fetch Policy Data";
	}
	echo "<br />";	
	
	
	echo '<strong>Amenities:</strong><br />';
	try
	{
		$xmlamenities = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_amenities', true));
		foreach($xmlamenities->Community->children() as $key => $val)
		{
			$has = ($val == "true");
			
			if($has)
			{
				echo '<p style="color: green; line-height: 0;">';
			}
			else
			{
				echo '<p style="color: red; line-height: 0;">';
			}
			echo getAmenityName($key) . '</p>';
		}
		
		foreach($xmlamenities->General as $general)
		{
			$has = ($general->AdditionalAmenity == "Community Amenity");
			
			if($has)
			{
				echo "<p style=\"color: green; line-height: 0;\">$general->AmenityName</p>";
			}
		}
		
		foreach($xmlamenities->Community->children() as $key => $val)
		{
			$has = ($val == "true");
			
			if($has)
			{
				echo '<p style="color: green; line-height: 0;">';
			}
			else
			{
				echo '<p style="color: red; line-height: 0;">';
			}
			echo getAmenityName($key) . '</p>';
		}
		
		foreach($xmlamenities->General as $general)
		{
			$has = ($general->AdditionalAmenity == "Community Amenity");
			
			if($has)
			{
				echo "<p style=\"color: green; line-height: 0;\">$general->AmenityName</p>";
			}
		}
		foreach($xmlamenities->Floorplan->children() as $key => $val)
		{
			$has = ($val == "true");
			
			if($has)
			{
				echo '<p style="color: green; line-height: 0;">';
			}
			else
			{
				echo '<p style="color: red; line-height: 0;">';
			}
			echo getAmenityName($key) . '</p>';
		}
		
		foreach($xmlamenities->General as $general)
		{
			$has = ($general->AdditionalAmenity == "Floorplan Amenity");
			
			if($has)
			{
				echo "<p style=\"color: green; line-height: 0;\">$general->AmenityName</p>";
			}
		}
	}
	catch(Exception $ex)
	{
		echo "Could not fetch Amenities Data";
	}
	echo "<br />";

?>
<div class="dumps">
	<h1>Object Dumps</h1>
	<div>
	<h2>Management</h2>
	<pre style="display: none;">
<?php print_r($xmlmanage); ?>
	</pre>
	</div>
	<div>
	<h2>Identity</h2>
	<pre style="display: none;">
<?php print_r($xmlident); ?>
	</pre>
	</div>
	<div>
	<h2>Policy</h2>
	<pre style="display: none;">
<?php print_r($xmlpolicy); ?>
	</pre>
	</div>
	<div>
	<h2>Amenities</h2>
	<pre style="display: none;">
<?php print_r($xmlamenities); ?>
	</pre>
	</div>
</div>

</div>
<script type="text/javascript">
	jQuery(function($)
	{
		$('.dumps').on('click', 'h2', function(e)
		{
			$parent = $(this).closest('div');
			$parent.find('pre').slideToggle();
		});
	});
</script>
<?php
}

function rapt_rentcafe_reg_settings()
{
	if(get_option('rapt_rentcafe_options') == false)
	{
		add_option('rapt_rentcafe_options');
	}
	
	add_settings_section
	(
		'rapt_rentcafe_options_ident',
		'Identification',
		'rapt_rentcafe_options_ident_display',
		'rapt_rentcafe_options'
	);
	
	add_settings_field
	(
		'rapt_rentcafe_options_ident_companycode',
		'Company Code',
		'rapt_rentcafe_options_ident_companycode_display',
		'rapt_rentcafe_options',
		'rapt_rentcafe_options_ident'
	);
	
	add_settings_field
	(
		'rapt_rentcafe_options_ident_propertycode',
		'Property Code',
		'rapt_rentcafe_options_ident_propertycode_display',
		'rapt_rentcafe_options',
		'rapt_rentcafe_options_ident'
	);
	
	add_settings_section
	(
		'rapt_rentcafe_options_cron',
		'Schedule',
		'rapt_rentcafe_options_cron_display',
		'rapt_rentcafe_options'
	);
	
	add_settings_field
	(
		'rapt_rentcafe_options_cron_timestamp',
		'First Run Timestamp (GMT)',
		'rapt_rentcafe_options_cron_timestamp_display',
		'rapt_rentcafe_options',
		'rapt_rentcafe_options_cron'
	);
	add_settings_field
	(
		'rapt_rentcafe_options_cron_interval',
		'Interval',
		'rapt_rentcafe_options_cron_interval_display',
		'rapt_rentcafe_options',
		'rapt_rentcafe_options_cron'
	);
	
	
	register_setting('rapt_rentcafe_options', 'rapt_rentcafe_options', 'rapt_rentcafe_options_sanitize');
}

function rapt_rentcafe_options_sanitize($input)
{
	$cleaninput['rapt_rentcafe_options_ident_companycode'] = trim($input['rapt_rentcafe_options_ident_companycode']);
	$cleaninput['rapt_rentcafe_options_ident_propertycode'] = trim($input['rapt_rentcafe_options_ident_propertycode']);
	
	if(is_numeric($input['rapt_rentcafe_options_cron_interval']) && $input['rapt_rentcafe_options_cron_interval'] >= 0 && $input['rapt_rentcafe_options_cron_interval'] <= 2)
	{
		$cleaninput['rapt_rentcafe_options_cron_interval'] = $input['rapt_rentcafe_options_cron_interval'];
	}
	else
	{
		$cleaninput['rapt_rentcafe_options_cron_interval'] = 0;
	}
	
	$next = wp_next_scheduled('rapt_rentcafe_do_import');
	if($next)
	{
		wp_unschedule_event($next, 'rapt_rentcafe_do_import');
	}
	
	if(is_numeric($input['rapt_rentcafe_options_cron_timestamp']) && ((int)$input['rapt_rentcafe_options_cron_timestamp'] == $input['rapt_rentcafe_options_cron_timestamp']))
	{
		$cleaninput['rapt_rentcafe_options_cron_timestamp'] = $input['rapt_rentcafe_options_cron_timestamp'];
		
		if($cleaninput['rapt_rentcafe_options_cron_timestamp'] != 0)
		{
			$interval = "";
			switch($cleaninput['rapt_rentcafe_options_cron_interval'])
			{
				case 1:
				{
					$interval = "twicedaily";
					break;
				}
				case 2:
				{
					$interval = "hourly";
					break;
				}
				default:
				{
					$interval = "daily";
					break;
				}
			}
			wp_schedule_event($cleaninput['rapt_rentcafe_options_cron_timestamp'], $interval, 'rapt_rentcafe_do_import');
		}
	}
	else
	{
		$cleaninput['rapt_rentcafe_options_cron_timestamp'] = 0;
	}
	
	return $cleaninput;
}

function rapt_rentcafe_settings_display()
{
?>
<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2>RentCafe Settings</h2>
	<?php settings_errors(); ?>
	
	<form method="post" action="options.php">
		<?php settings_fields( 'rapt_rentcafe_options' ); ?>
		<?php do_settings_sections( 'rapt_rentcafe_options' ); ?>
		<?php submit_button(); ?>
	</form>
</div>
<?php
}

function rapt_rentcafe_options_cron_display()
{
	echo '<p>RentCafe scheduled import settings.</p>';
}

function rapt_rentcafe_options_cron_timestamp_display($args)
{
	$options = get_option('rapt_rentcafe_options');
?>
<input type="text" id="rapt_rentcafe_options_cron_timestamp" name="rapt_rentcafe_options[rapt_rentcafe_options_cron_timestamp]" value="<?php echo $options['rapt_rentcafe_options_cron_timestamp']; ?>">
<span id="tsout"></span>
<br />
<a href="http://www.timestampgenerator.com/" target="_blank">[Timestamp Generator]</a>
<script type="text/javascript">
	jQuery(function($)
	{
		var tsin = $('#rapt_rentcafe_options_cron_timestamp');
		var tsout = $('#tsout');
		tsin.keyup(function()
		{
			var value = tsin.val();
			var date = new Date(value * 1000);
			if(value == "" || value == 0)
			{
				tsout.text('Scheduled Import Disabled');
			}
			else if(!$.isNumeric(date.getTime()))
			{
				tsout.text('Invalid Timestamp');
			}
			else
			{
				tsout.text(date.toLocaleDateString() + " " + date.toTimeString());
				//tsout.text(date.toUTCString());
			}
		});
		tsin.trigger('keyup');
	});
</script>
<?php
}

function rapt_rentcafe_options_cron_interval_display($args)
{
	$options = get_option('rapt_rentcafe_options');
	$sel = $options['rapt_rentcafe_options_cron_interval'];

	if($sel == "")
		$sel = 0;
	
	echo '<input type="radio" id="rapt_rentcafe_options_cron_interval_0" name="rapt_rentcafe_options[rapt_rentcafe_options_cron_interval]" value="0"' . checked(0, $sel, false) . '>';
	echo '<label for="rapt_rentcafe_options_cron_interval_0"> Daily</label><br />';
	echo '<input type="radio" id="rapt_rentcafe_options_cron_interval_1" name="rapt_rentcafe_options[rapt_rentcafe_options_cron_interval]" value="1"' . checked(1, $sel, false) . '>';
	echo '<label for="rapt_rentcafe_options_cron_interval_1"> Twice Daily</label><br />';
	echo '<input type="radio" id="rapt_rentcafe_options_cron_interval_2" name="rapt_rentcafe_options[rapt_rentcafe_options_cron_interval]" value="2"' . checked(2, $sel, false) . '>';
	echo '<label for="rapt_rentcafe_options_cron_interval_2"> Hourly</label>';
}

function rapt_rentcafe_options_ident_display()
{
	echo '<p>Configure RentCafe integration settings.</p>';
}

function rapt_rentcafe_options_ident_companycode_display($args)
{
	$options = get_option('rapt_rentcafe_options');
	echo '<input type="text" id="rapt_rentcafe_options_ident_companycode" name="rapt_rentcafe_options[rapt_rentcafe_options_ident_companycode]" value="' . $options['rapt_rentcafe_options_ident_companycode'] . '">';
}

function rapt_rentcafe_options_ident_propertycode_display($args)
{
	$options = get_option('rapt_rentcafe_options');
	echo '<input type="text" id="rapt_rentcafe_options_ident_propertycode" name="rapt_rentcafe_options[rapt_rentcafe_options_ident_propertycode]" value="' . $options['rapt_rentcafe_options_ident_propertycode'] . '">';
}

function rapt_rentcafe_do_import()
{
	return; //Disalbe Importing on old system
	$options = get_option('rapt_rentcafe_options');
	$starttime = microtime(true);
	try
	{
		if($options['rapt_rentcafe_options_ident_companycode'] == "" || $options['rapt_rentcafe_options_ident_propertycode'] == "")
		{
			throw new Exception('Must provide Company and Property code in settings.');
		}
		
		$ccode = $options['rapt_rentcafe_options_ident_companycode'];
		$pcode = $options['rapt_rentcafe_options_ident_propertycode'];
		
		$data = file_get_contents("https://www.rentcafe.com/feeds/{$ccode}.xml");
		if(!$data)
		{
			throw new Exception('Invalid Company Code.');
		}
		
		$xml = simplexml_load_string($data);
		if($xml === false)
		{
			throw new Exception('Invalid XML.');
		}
		
		update_post_meta(1, '_rapt_rentcafe_data_management', $xml->Management->asXML());
		
		$property = null;
		if(count($xml->Property) > 1)
		{
			foreach($xml->Property as $prop)
			{
				if($prop->Identification->PrimaryID == $pcode)
				{
					$property = $prop;
				}
			}
		}
		else
		{
			if($xml->Property->Identification->PrimaryID == $pcode)
			{
				$property = $xml->Property;
			}
		}
		
		if(!$property)
		{
			throw new Exception('Invalid Property Code.');
		}
		
		$data2 = file_get_contents("https://www.rentcafe.com/rentcafeapi.aspx?requestType=apartmentavailability&companyCode=$ccode&propertyCode=$pcode");
		$json = json_decode($data2);
		//echo "<pre>Policy: {$property->asXML()}</pre>";
		update_post_meta(1, '_rapt_rentcafe_data_ident', $property->Identification->asXML());
		update_post_meta(1, '_rapt_rentcafe_data_policy', $property->Policy->asXML());
		update_post_meta(1, '_rapt_rentcafe_data_amenities', $property->Amenities->asXML());
		
		$beds = array(-1);
		$baths = array(-1);
		$floorplans = $property->Floorplan;

		$args = array(
			'post_type' 	=> 'apt-model',
			'post_status'	=> array
							(
								'publish',
								'future',
								'draft',
								'pending'
							),
			'orderby'		=> 'title',
			'order'			=> 'ASC',
			'posts_per_page'=> -1
		);
		//$value = get_post_meta($post->ID, '_rapt_apt_model_id', true);
		$ifloorplans = new WP_Query($args);
		$fpadd = 0;
		$fpupdate = 0;
		foreach($floorplans as $fp)
		{
			$found = false;
			foreach($ifloorplans->posts as $ifp)
			{
				if($ifp->post_title == $fp->Name)
				{
					$found = true;
					$terms = array();
					foreach($fp->Room as $room)
					{
						update_post_meta($ifp->ID, '_rapt_model_'.$room->attributes()->type, sanitize_text_field($room->Count));
						$terms[] = $room->Count . ' ' . ucwords($room->attributes()->type);
					}
					update_post_meta($ifp->ID, '_rapt_model_sqft_min', sanitize_text_field($fp->SquareFeet->attributes()->min));
					update_post_meta($ifp->ID, '_rapt_model_sqft_max', sanitize_text_field($fp->SquareFeet->attributes()->max));
					update_post_meta($ifp->ID, '_rapt_model_price_min', sanitize_text_field($fp->EffectiveRent->attributes()->min));
					update_post_meta($ifp->ID, '_rapt_model_price_max', sanitize_text_field($fp->EffectiveRent->attributes()->max));
					update_post_meta($ifp->ID, '_rapt_model_fpid', sanitize_text_field($fp->attributes()->id));
					
					$path = $xml->xpath('/PhysicalProperty/Property/File[@id="' . $fp->attributes()->id . '"]/Src');
					update_post_meta($ifp->ID, '_rapt_model_imageurl', sanitize_text_field($path[0][0]));
					
					wp_set_object_terms($ifp->ID, $terms, 'model-tags', true);
					
					$fpupdate++;
					break;
				}
			}
			if(!$found)
			{
				$post = array(
					'post_type'			=>	'apt-model',
					'post_title'		=>	$fp->Name,
					'post_status'		=>	'publish'
				);
				
				$newid = wp_insert_post($post);
				if($newid == 0)
				{
					throw new Exception('There was an error creating a new model.');
				}
				
				$terms = array();
				foreach($fp->Room as $room)
				{
					update_post_meta($newid, '_rapt_model_'.$room->attributes()->type, sanitize_text_field($room->Count));
					$terms[] = $room->Count . ' ' . ucwords($room->attributes()->type);
				}
				update_post_meta($newid, '_rapt_model_sqft_min', sanitize_text_field($fp->SquareFeet->attributes()->min));
				update_post_meta($newid, '_rapt_model_sqft_max', sanitize_text_field($fp->SquareFeet->attributes()->max));
				update_post_meta($newid, '_rapt_model_price_min', sanitize_text_field($fp->EffectiveRent->attributes()->min));
				update_post_meta($newid, '_rapt_model_price_max', sanitize_text_field($fp->EffectiveRent->attributes()->max));
				update_post_meta($newid, '_rapt_model_fpid', sanitize_text_field($fp->attributes()->id));
				
				$path = $xml->xpath('/PhysicalProperty/Property/File[@id="' . $fp->attributes()->id . '"]/Src');
				update_post_meta($newid, '_rapt_model_imageurl', sanitize_text_field($path[0][0]));
				
				wp_set_object_terms($newid->ID, $terms, 'model-tags', true);
				
				$fpadd++;
			}
		}
		
		$args = array(
			'post_type' 	=> 'apt-floor',
			'post_status'	=> array
							(
								'publish',
								'future',
								'draft',
								'pending'
							),
			'orderby'		=> 'title',
			'order'			=> 'ASC',
			'posts_per_page'=> -1
		);
		
		$ifloors = new WP_Query($args);
		$floormap = array(0 => 0);
		foreach($ifloors->posts as $ifloor)
		{
			//echo get_post_meta($ifloor->ID, '_rapt_floor_num', true) . " ";
			$floormap[get_post_meta($ifloor->ID, '_rapt_floor_num', true)] = $ifloor->ID;
		}
		
		$units = $property->ILS_Unit;

		$args = array(
			'post_type' 	=> 'apt',
			'post_status'	=> array
							(
								'publish',
								'future',
								'draft',
								'pending'
							),
			'orderby'		=> 'title',
			'order'			=> 'ASC',
			'posts_per_page'=> -1
		);
		
		$iunits = new WP_Query($args);
		
		$uadd = 0;
		$uupdate = 0;
		$ucount = 0;
		foreach($iunits->posts as $iunit)
		{
			update_post_meta($iunit->ID, '_rapt_apt_available', "");
			update_post_meta($iunit->ID, '_rapt_apt_availabledate', "");
		}
		
		$propid = 0;
		foreach($units as $unit)
		{
			$jindex = -1;
			$found = false;
			foreach($iunits->posts as $iunit) //Maybe loop through internal units first and remove them from xml, then loop through xml to add new ones.
			{
				if($iunit->post_title == $unit->UnitID)
				{
					$found = true;
					foreach($json as $key => $jsoni)
					{
						if($jsoni->ApartmentName == $unit->UnitID)
						{
							$jindex = $key;
							break;
						}
					}
					
					update_post_meta($iunit->ID, '_rapt_apt_model_id', GetModelIDByName($ifloorplans, $unit->FloorplanName));
					
					$beds[] = (float)sanitize_text_field($unit->UnitBedrooms);
					$baths[] = (float)sanitize_text_field($unit->UnitBathrooms);
					
					$settings = get_option('razz_apt_opt');
					if(!empty($settings['rentcafe_parsing_floor']))
					{
						$patterns = explode('::', $settings['rentcafe_parsing_floor']);
						foreach($patterns as $pattern)
						{
							if(preg_match("/{$pattern}/", $unit->UnitID, $matches))
							{
								$fid = $matches[1];
								break;
							}
						}
					}
					elseif(preg_match('/^T\d$/', $unit->UnitID))
						$fid = 1;
					else
						$fid = substr($unit->UnitID, -3, 1);
					/*
					if(preg_match('/^\d-/', $unit->UnitID))
						$fid = substr($unit->UnitID, 2, 1);
					else
						$fid = substr($unit->UnitID, 0, 1);
					*/
					
					$fid = (array_key_exists($fid, $floormap)) ? $fid : 0;
					//echo "$floormap[$fid] ";
					update_post_meta($iunit->ID, '_rapt_apt_floor', sanitize_text_field($floormap[$fid]));
					update_post_meta($iunit->ID, '_rapt_apt_price', sanitize_text_field($unit->UnitRent));
					update_post_meta($iunit->ID, '_rapt_apt_availabledate', sanitize_text_field($unit->DateAvailable));
					update_post_meta($iunit->ID, '_rapt_apt_sqft_min', sanitize_text_field($unit->MinSquareFeet));
					update_post_meta($iunit->ID, '_rapt_apt_sqft_max', sanitize_text_field($unit->MaxSquareFeet));
					if($jindex > -1)
					{
						update_post_meta($iunit->ID, '_rapt_apt_aptid', sanitize_text_field($json[$jindex]->ApartmentId)); //TODO: Add to backend
						//update_post_meta($iunit->ID, '_rapt_apt_availabledate', sanitize_text_field($json[$jindex]->AvailableDate)); //TODO: Add to backend
						update_post_meta($iunit->ID, '_rapt_apt_amenities', sanitize_text_field($json[$jindex]->Amenities)); //TODO: Add to backend
					}
					//update_post_meta($iunit->ID, '_rapt_apt_deposit', sanitize_text_field(0);
					//update_post_meta($iunit->ID, '_rapt_apt_leaseterm', sanitize_text_field());
					//update_post_meta($iunit->ID, '_rapt_apt_pets', sanitize_text_field());
					//update_post_meta($iunit->ID, '_rapt_apt_occupancy', sanitize_text_field());
					update_post_meta($iunit->ID, '_rapt_apt_available', "yes");
					
					if($propid == 0)
					{
						$propid = $unit->PropertyPrimaryID;
					}
					
					$uupdate++;
					break;
				}
			}
			if(!$found)
			{
				$post = array(
					'post_type'			=>	'apt',
					'post_title'		=>	$unit->UnitID
				);
				
				$newid = wp_insert_post($post);
				if($newid == 0)
				{
					throw new Exception('There was an error creating a new model.');
				}
				
				update_post_meta($newid, '_rapt_apt_model_id', GetModelIDByName($ifloorplans, $unit->FloorplanName));
				
				
				$beds[] = (float)sanitize_text_field($unit->UnitBedrooms);
				$baths[] = (float)sanitize_text_field($unit->UnitBathrooms);
				
				//$fid = substr($unit->UnitID, 0, 1);
				$settings = get_option('razz_apt_opt');
				if(!empty($settings['rentcafe_parsing_floor']))
				{
					$patterns = explode('::', $settings['rentcafe_parsing_floor']);
					foreach($patterns as $pattern)
					{
						if(preg_match("/{$pattern}/", $unit->UnitID, $matches))
						{
							$fid = $matches[1];
							break;
						}
					}
				}
				elseif(preg_match('/^T\d$/', $unit->UnitID))
					$fid = 1;
				else
					$fid = substr($unit->UnitID, -3, 1);
				
				$fid = (array_key_exists($fid, $floormap)) ? $fid : 0;
				update_post_meta($newid, '_rapt_apt_floor', $floormap[$fid]);
				update_post_meta($newid, '_rapt_apt_price', sanitize_text_field($unit->UnitRent));
				update_post_meta($newid, '_rapt_apt_availabledate', sanitize_text_field($unit->DateAvailable));
				if($jindex > -1)
				{
					update_post_meta($newid, '_rapt_apt_aptid', sanitize_text_field($json[$jindex]->ApartmentId)); //TODO: Add to backend
					//update_post_meta($newid, '_rapt_apt_availabledate', sanitize_text_field($json[$jindex]->AvailableDate)); //TODO: Add to backend
					update_post_meta($newid, '_rapt_apt_amenities', sanitize_text_field($json[$jindex]->Amenities)); //TODO: Add to backend
				}
				update_post_meta($newid, '_rapt_apt_available', "yes");
				
				update_post_meta($newid, '_rapt_apt_deposit', sanitize_text_field(0));
				update_post_meta($newid, '_rapt_apt_leaseterm', sanitize_text_field(""));
				update_post_meta($newid, '_rapt_apt_pets', sanitize_text_field(""));
				update_post_meta($newid, '_rapt_apt_occupancy', sanitize_text_field(""));
				update_post_meta($newid, '_rapt_unit_locx', sanitize_text_field(0));
				update_post_meta($newid, '_rapt_unit_locy', sanitize_text_field(0));
				
				$uadd++;
			}
			$ucount++;
		}
		
		$beds = array_unique($beds);
		$baths = array_unique($baths);
		sort($beds, SORT_NUMERIC);
		sort($baths, SORT_NUMERIC);
		
		update_post_meta(1, '_rapt_beds', json_encode($beds));
		update_post_meta(1, '_rapt_baths', json_encode($baths));
		update_post_meta(1, '_rapt_rentcafe_data_propid', sanitize_text_field($propid));
		$exectime = round(microtime(true) - $starttime, 2);
		
		echo "<div class=\"updated\"><strong>Success:</strong><br />Updated General Data<br />Floorplans Added: $fpadd<br />Floorplans Updated: $fpupdate<br />Units Added: $uadd<br />Units Updated: $uupdate<br />Completed in {$exectime} seconds.</div>";
		file_put_contents(plugin_dir_path(__FILE__) . 'logs/rcimport_' . time() . '.txt', "Success:\nUpdated General Data\nFloorplans Added: $fpadd\nFloorplans Updated: $fpupdate\nUnits Added: $uadd\nUnits Updated: $uupdate\nCompleted in {$exectime} seconds.\n\n{$data}");
	}
	catch(Exception $ex)
	{
		echo '<div class="error"><strong>Error:</strong> ' . $ex->getMessage() . "<br /><br />{$data}</div>";
		file_put_contents(plugin_dir_path(__FILE__) . 'logs/rcimport_' . time() . '_error.txt', "Error:\n" . $ex->getMessage() . "\n\n{$data}");
	}
}

function rapt_rentcafe_import_display()
{
	if(isset($_POST['rapt_rentcafe_import']) && wp_verify_nonce($_POST['rapt_rentcafe_import'], plugin_basename( __FILE__ )))
	{
		rapt_rentcafe_do_import();
	}
	
	$options = get_option('rapt_rentcafe_options');
?>
<div id="icon-tools" class="icon32"></div>
<h2>RentCafe Import</h2>
<form method="post" target="_self">
<?php wp_nonce_field(plugin_basename( __FILE__ ), 'rapt_rentcafe_import'); ?>
<p><strong>Company Code: </strong><?php echo $options['rapt_rentcafe_options_ident_companycode']; ?></p>
<p><strong>Property Code: </strong><?php echo $options['rapt_rentcafe_options_ident_propertycode']; ?></p>
<br /><br />
<input type="submit" value="Begin Import" class="button-primary" />
</form>
<?php
}


function GetModelIDByName($ifloorplans, $name)
{
	foreach($ifloorplans->posts as $ifp)
	{
		if($ifp->post_title == $name)
		{
			return $ifp->ID;
		}
	}
	return 0;
}
?>