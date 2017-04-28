<?php
	define('WP_USE_THEMES', false);
	if(!defined('ABSPATH')) require_once('../../../../wp-blog-header.php');
	header('HTTP/1.1 200 OK', true, 200);
	$settings = get_option('razz_apt_opt');
	
	$adata = AptData::Instance();
	$floors = $adata->_('floors');
	$targetfloor = $_GET['id'];
	if(!isset($targetfloor) || !isset($floors[$targetfloor]) || ($floor = $floors[$targetfloor]) === false)
	{
		echo "Invalid Floor";
		exit;
	}
	
	$order = array_keys($floors);
	//maybe sort
	$index = array_search($floor['id'], $order);

    $models = $adata->_('models');
    foreach($models as $key=>$value)
    {
        if($value['disabled']) unset($models[$key]);
    }
	$tmpunits = $adata->_search('units', 'floor', $floor['id']);
	$units = array();
	foreach($tmpunits as $unit)
	{
		if($unit['disabled']) continue;
		$model = $models[$unit['model']];
		$aptdata = array(
			'unit'			=> $unit['id'],
			'model'			=> $model['name'],
			'modelid'		=> $model['id'],
			'floor'			=> $unit['floor'],
			'rent'			=> getFormattedRent($unit),
			'rent_raw'		=> $unit['rent']['market'],
			'deposit'		=> getFormattedDeposit($unit),
			'leaseterm'		=> $settings['uv_leasingterm'],
			'special'		=> isset($unit['special']) ? ' ' . $unit['special'] : '',
			'bed'			=> $unit['rooms']['bedroom'],
			'bath'			=> $unit['rooms']['bathroom'],
			'x'				=> $unit['loc']['x'],
			'y'				=> $unit['loc']['y'],
			'date'			=> (($timestamp = strtotime($unit['date'])) !== false) ? strtoupper(date("M j, Y", $timestamp)) : '',
			'image'			=> $model['image']
		);
		$aptdata['bed_disp'] = ($aptdata['bed'] == 0) ? 'Studio' : $aptdata['bed'] . ' Bed';
		if($settings['data_sqft_source'] == 'unit')
			$source = $unit;
		else
			$source = $model;
		
		$sqftmin = $source['sqft']['min'];
		$sqftmax = $source['sqft']['max'];
		
		if($sqftmin != $sqftmax)
		{
			$aptdata['sqft'] = "{$sqftmin} - {$sqftmax}";
			$aptdata['sqft_raw'] = round(($sqftmin + $sqftmax) / 2, 2);
		}
		else
		{
			$aptdata['sqft'] = $aptdata['sqft_raw'] = $sqftmin;
		}
		
		$units[] = $aptdata;
	}


	$extra = isset($floor['extra']) ? $floor['extra'] : false;

	
	function getPrev()
	{
		global $order, $index;
		$i = $index;
		if($i <= 0) $i = count($order)-1;
		else $i--;
		return $order[$i];
	}
	function getNext()
	{
		global $order, $index;
		$i = $index;
		if($i >= count($order)-1) $i = 0;
		else $i++;
		return $order[$i];
	}
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Floor <?php echo $floor['id']; ?> - Floor View</title>
		<meta name="description" content="<?= $settings['widget_description'] ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

		<meta property="og:title" content="Floor <?php echo $floor['id']; ?> - <?= get_bloginfo('name'); ?>" />
		<meta property="og:type" content="website" />
		<meta property="og:description" content="<?= $settings['widget_description'] ?>" />
        <meta property="og:image" content="<?= $floor['image']; ?>" />
	    <base href="<?= plugin_dir_url(__FILE__); ?>">
		
        <link rel="stylesheet" href="css/normalize.min.css">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
        <link href='//fonts.googleapis.com/css?family=Open+Sans:300,400,700|Oswald:400,700' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="css/tipped/tipped.css"/>
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/3.0.5/css/tooltipster.min.css"/>
        <link type="text/css" rel="stylesheet" href="css/jquery.dropdown.css" />
        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
		<style type="text/css">
            body{
                <?php echo $options_panel->getFontCSS($settings['lb_mainfont']); ?>
                overflow: hidden;
            }
            a{
	            outline: 0;
            }
            .closebtn{
                position: fixed;
                right: 15px;
                padding: 6px;
                color: <?= $settings['lb_header_close_color']; ?>;
                font-family: "Open Sans",sans-serif;
                font-size: 40px;
                font-weight: 100;
                line-height: 1;
                text-decoration: none;
                z-index: 1001;
            }
            .closebtn:hover{
                color: <?= $settings['lb_header_close_color_hover']; ?>;
            }
            #header{
                position: fixed;
                display: table;
                top: 0; left: 0;
                width: 100%; height: 55px;
                background: <?= $settings['lb_header_bgcolor']; ?>;
                font-size: 16px;
                z-index: 1000;
            }
            #header > div{
                display: table-cell;
                vertical-align: middle;
            }
            #header > div > ul{
                padding: 0;
                list-style-type: none;
                margin: 4px 57px;
            }
            #header > div > ul > li{
                display: inline;
                padding: 0 8px;
                color: <?= $settings['lb_header_bread_sep']; ?>;
                white-space: nowrap;
            }
            #header > div > ul > li > a{
                color: <?= $settings['lb_header_bread_link']; ?>;
            }
            #header > div > ul > li > a:hover{
                color: <?= $settings['lb_header_bread_link_hover']; ?>;
            }
            #header > div > ul > li:last-child > a{
                color: <?= $settings['lb_header_bread_active']; ?>;
            }
            #header > div > ul > li:last-child > a:first-child{
                cursor: default;
            }
            #footer{
                position: fixed;
                left: 0; bottom: 0;
                width: 100%; height: 40px;
                cursor: default;
                z-index: 100;
                text-align: center;
            }
            #footer #msg{
                display: inline-block;
                padding: 3px 10px;
                background: <?= $settings['lb_footer_bg_color']; ?>;
                text-align: center;
                <?= $options_panel->getFontCSS($settings['lb_footer_font']); ?>
                -webkit-border-radius: 5px;
                -moz-border-radius: 5px;
                border-radius: 5px;
            }
            #footer #msg a {
                color: <?= $settings['lb_footer_a']; ?>;
            }
            #footer #msg:hover{
                background: <?= $settings['lb_footer_hover_bg_color']; ?>;
                color: <?= $settings['lb_footer_hover_font']; ?>;
            }
            #footer #msg:hover a {
                color: <?= $settings['lb_footer_hover_a']; ?>;
            }
            #footer #msg a:hover {
                color: <?= $settings['lb_footer_hover_a_hover']; ?>;
            }
            #floor-ddb{
                margin-left: 5px;
                text-decoration: none;
            }
            .dropdown .dropdown-menu,
            .dropdown .dropdown-panel{
                background: <?= $settings['lb_header_bread_floors_bgcolor']; ?>;
            }
            .dropdown .dropdown-menu li > a,
            .dropdown .dropdown-menu label{
                color: <?= $settings['lb_header_bread_floors_fgcolor']; ?>;
                font-size: 14px;
            }
            .dropdown .dropdown-menu li > a:hover,
            .dropdown .dropdown-menu label:hover{
                color: <?= $settings['lb_header_bread_floors_fgcolor_hover']; ?>;
                background-color: <?= $settings['lb_header_bread_floors_bgcolor_hover']; ?>;
            }

            .showmobile{display: none !important;}
            @media screen and (max-width: 800px)
            {
                #header > div > ul{
                    margin: 4px 43px;
                }
                .hidemobile{display: none !important;}

                .showmobile{display: table-cell !important;}
            }
            @media screen and (max-width: 480px)
            {
                #header > div > ul{
                    margin: 4px 47px 4px 4px;
                }
                #header > div > ul > li{
                    padding: 0 2px;
                }
            }

            .tooltipster-content{
	            text-align: center;
	            line-height: 2;
            }

            #fp_wrapper{
				position: fixed;
	            top: 55px; left: 0; right: 0; bottom: 0;
	            text-align: center;
	            overflow-x: hidden;
	            overflow-y: scroll; /* has to be scroll, not auto */
	            -webkit-overflow-scrolling: touch;
            }

            #fp_wrapper:before{
	            content: '';
	            display: inline-block;
	            height: 100%;
	            vertical-align: middle;
            }

            #fp-cont{
	            position: relative;
	            display: inline-block;
	            vertical-align: middle;
	            max-width: 98%;
	            margin: 5px 0 50px;
            }

            #fp-cont img{
	            max-width: 100%;
            }

			.map-unit{
				position: absolute;
				background: <?= $adata->_("fv_dot_available_inner", $settings); ?>;
				border: <?= $adata->_("fv_dot_stroke", $settings); ?>px solid <?= $adata->_("fv_dot_available_outer", $settings); ?>;
				border-radius: 50%;
				-webkit-transform: translate(-50%, -50%);
				transform: translate(-50%, -50%);
			}

			.map-unit:hover{
				background-color: <?= $adata->_("fv_dot_selected_inner", $settings); ?>;
				border-color: <?= $adata->_("fv_dot_selected_outer", $settings); ?>;
			}

			.map-extra{
				position: absolute;
				background: <?= $adata->_("fv_amenity_dot_available_inner", $settings); ?>;
				border: <?= $adata->_("fv_amenity_dot_stroke", $settings); ?>px solid <?= $adata->_("fv_dot_available_outer", $settings); ?>;
				border-radius: 50%;
				-webkit-transform: translate(-50%, -50%);
				transform: translate(-50%, -50%);
			}

			.map-extra:hover{
				background-color: <?= $adata->_("fv_amenity_dot_selected_inner", $settings); ?>;
				border-color: <?= $adata->_("fv_amenity_dot_selected_outer", $settings); ?>;
			}
		</style>
    </head>
    <body>
        <div id="header">
            <div>
                <ul>
                    <li><a href="#" class="" data-dropdown="#menu-dd">Floor Plans <i class="fa fa-chevron-circle-down ddb"></i></a></li>
                    <li><i class="fa fa-angle-right fa-lg"></i></li>
                    <li>
                        <a><?= ($floor['title']) ? $floor['title'] : "Floor {$floor['id']}"; ?></a>
                        <a href="#" id="floor-ddb" data-dropdown="#floor-dd" class="fa fa-chevron-circle-down"></a>
                        <div id="floor-dd" class="dropdown dropdown-tip">
                            <ul class="dropdown-menu">
                                <?php foreach($floors as $f): if(!$f['verticies']) continue; ?>
                                    <li><a href="floor.php?id=<?= urlencode($f['id']) ?>" class="goto-floor" data-floor="<?= $f['id']; ?>"><?= ($f['title']) ? $f['title'] : "Floor {$f['id']}"; ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <a href="#" class="closebtn">×</a>

		<div id="fp_wrapper">
			<div id="fp-cont"></div>
		</div>

		 <div id="footer">
            <div id="msg">Powered by <a target="_blank" href="http://razzinteractive.com">Razz Interactive</a> + <a target="_blank" href="http://gotomyapartment.com">GoToMyApartment</a></div>
        </div>

	    <div id="menu-dd" class="dropdown dropdown-tip">
		    <ul class="dropdown-menu">
			    <?php if(!$settings['widget_hide_floors']): ?><li><a href="./floors.php" class="goto-floors hidemobile">Floors</a></li><?php endif; ?>
			    <?php if(!$settings['widget_hide_models']): ?><li><a href="./models.php" class="goto-models">Models</a></li><?php endif; ?>
			    <?php if(!$settings['widget_hide_search']): ?><li><a href="./search.php" class="goto-search"><?= $settings['txt_search'] ? $settings['txt_search'] : 'Search' ?></a></li><?php endif; ?>
		    </ul>
	    </div>


        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.0.min.js"><\/script>')</script>
        <script type="text/javascript" src="js/jquery.dropdown.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tooltipster/3.0.5/js/jquery.tooltipster.min.js"></script>
        <script type="text/javascript" src="../xdm/easyXDM.js"></script>
        <script type="text/javascript" src="js/xdm.js?ver=1.0.1"></script>
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test( (window.navigator.userAgent||window.navigator.vendor||window.opera) );
				if(!window['razz']) razz = {};
				if(!razz['apt']) razz.apt = {};
				var $fp_cont = $('#fp-cont'),
					retina = true,
					units = <?= json_encode($units); ?>,
					extras = <?= json_encode($extra); ?>,
					imgsrc = <?= json_encode($floor['image']); ?>,
					settings = {
						dots: {
							radius: <?= json_encode($adata->_("fv_dot_radius", $settings)); ?>
						},
						extra_dots: {
							radius: <?= json_encode($adata->_("fv_amenity_dot_radius", $settings)); ?>
						}
					};
				if(!isMobile || true)
				{
					$("<img/>").attr('src', imgsrc).load(function()
					{
						var img = {
							w: this.width,
							h: this.height
						};

						$(this).appendTo($fp_cont);
						$fp_cont.css({
							width: img.w / (retina ? 2 : 1)
						});



						for(var i = 0, len = units.length; i < len; i++)
						{
							var unit = units[i];
							$fp_cont.append(
								$('<a />', {
									'id': 'unit-' + unit.unit,
									'class': 'map-unit goto-unit',
									'href': '#',
									'data-unit': unit.unit
								})
								.css({
									'left': ((unit.x) / img.w * 100).toFixed(2) + '%',
									'top': ((unit.y) / img.h * 100).toFixed(2) + '%',
									'width': ((((unit.radius ? unit.radius : settings.dots.radius) * 2) / img.w) * 100) + '%',
									'height': ((((unit.radius ? unit.radius : settings.dots.radius) * 2) / img.h) * 100) + '%'
								})
								.tooltipster({
									animation: 'grow',
									content: $('<span/>').html(
										'<strong>APARTMENT #' + unit.unit + '</strong><br />' +
										'<img src="' + unit.image + '" style="max-width: 200px;" /><br />' +
                                        unit.bed_disp + ' / ' + unit.bath + ' Bath / ' + unit.sqft + ' Sqft. / ' + unit.rent
									)
								})
							);
						}

						for(var i = 0, len = extras.length; i < len; i++)
						{
							var extra = extras[i];
							$fp_cont.append(
								$('<a />', {
									'id': 'extra-' + extra.id,
									'class': 'map-extra',
									'href': '#',
									'onclick': 'return false;'
								})
								.css({
									'left': ((extra.x) / img.w * 100).toFixed(2) + '%',
									'top': ((extra.y) / img.h * 100).toFixed(2) + '%',
									'width': ((((extra.radius ? extra.radius : settings.extra_dots.radius) * 2) / img.w) * 100) + '%',
									'height': ((((extra.radius ? extra.radius : settings.extra_dots.radius) * 2) / img.h) * 100) + '%'
								})
								.tooltipster({
									animation: 'grow',
									content: $('<span/>').html(extra.html)
								})
							);
						}
					});
				}

                if(rpc) rpc.setTitle('Floor <?= $floor['id'] ?>');
			});
		</script>
    </body>
</html>