<?php
	define('WP_USE_THEMES', false);
	if(!defined('ABSPATH')) require_once('../../../../wp-blog-header.php');
	header('HTTP/1.1 200 OK', true, 200);
	$settings = get_option('razz_apt_opt');
	
	$adata = AptData::Instance();

    $models = $adata->_('models');
    foreach($models as $key=>$value)
    {
        if($value['disabled']) unset($models[$key]);
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
        <title>Floor Plans</title>
        <meta name="description" content="<?= $settings['widget_description'] ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<meta property="og:title" content="Floor Plans - <?= get_bloginfo('name'); ?>" />
		<meta property="og:type" content="website" />
		<meta property="og:description" content="<?= $settings['widget_description'] ?>" />
        <meta property="og:image" content="<?php echo esc_attr($adata->_("qs_ibldg_img.src", $settings)); ?>" />
	    <base href="<?= plugin_dir_url(__FILE__); ?>">
		
        <link rel="stylesheet" href="css/normalize.min.css">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
        <link href='//fonts.googleapis.com/css?family=Open+Sans:300,400,700|Oswald:400,700' rel='stylesheet' type='text/css'>
        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        <link type="text/css" rel="stylesheet" href="css/jquery.dropdown.css" />
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
            .ddb{
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
        </style>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div id="header">
            <div>
                <ul>
                    <li>
                        <a href="#" class="" data-dropdown="#menu-dd">Floor Plans <i class="fa fa-chevron-circle-down ddb"></i></a>
                    </li>
                </ul>
            </div>
        </div>
        <a href="#" class="closebtn">×</a>

        <div id="fp-wrapper" style="display: none; overflow: hidden;">
            <div id="fp-cont" style="position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); -webkit-transform: translate(-50%, -50%); transform: translate(-50%, -50%); padding-top: 56px;"></div>
        </div>
		<map id="bldgmap" name="bldgmap"></map>
		<div id="floorhover" style="display: none; position: absolute; background: rgba(0,0,0,0.8); padding: 10px; color: #fff;"></div>


	    <div id="menu-dd" class="dropdown dropdown-tip">
		    <ul class="dropdown-menu">
			    <?php if(!$settings['widget_hide_models']): ?><li><a href="./models.php" class="goto-models">Models</a></li><?php endif; ?>
			    <?php if(!$settings['widget_hide_search']): ?><li><a href="./search.php" class="goto-search"><?= $settings['txt_search'] ? $settings['txt_search'] : 'Search' ?></a></li><?php endif; ?>
		    </ul>
	    </div>

        <div id="footer">
            <div id="msg">Powered by <a target="_blank" href="http://razzinteractive.com">Razz Interactive</a> + <a target="_blank" href="http://gotomyapartment.com">GoToMyApartment</a></div>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.0.min.js"><\/script>')</script>
        <script type="text/javascript" src="js/tipped.js"></script>
        <script type="text/javascript" src="js/imagesloaded.pkgd.min.js"></script>
        <script type="text/javascript" src="//cdn.jsdelivr.net/jquery.imagemapster/1.2.10/jquery.imagemapster.min.js"></script>
        <script type="text/javascript" src="../xdm/easyXDM.js"></script>
    <script type="text/javascript" src="js/xdm.js?ver=1.0.1"></script>
        <script type="text/javascript" src="js/jquery.dropdown.min.js"></script>

		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
				var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test( (window.navigator.userAgent||window.navigator.vendor||window.opera) );
				if(!window['razz']) razz = {};
				if(!razz['apt']) razz.apt = {};
                if(rpc) rpc.setTitle('Floor View');
				razz.apt.floors = <?php echo json_encode($adata->_("floors")); ?>;
				var $map = $('#bldgmap'),
					imgsrc = <?php echo json_encode($adata->_("qs_ibldg_img.src", $settings)); ?>,
					retina = <?php echo json_encode($adata->_("ibldg_img_retina", $settings)); ?>,
                    settings = {
                        floor: {
                            stroke: 0,
                            color_hover: {
                                inner: getColor(<?php echo json_encode($adata->_("qs_ibldg_floorhovercolor", $settings)); ?>),
                                outter: getColor(<?php echo json_encode($adata->_("qs_ibldg_floorhovercolor", $settings)); ?>)
                            }
                        }
                    };
				if(!isMobile)
				{
					for (var k in razz.apt.floors) {
                        if (!razz.apt.floors.hasOwnProperty(k)) continue;
                        var floor = razz.apt.floors[k];
                        if (!floor.verticies.length) continue;
                        var vert_groups = floor.verticies.split('|');
                        var group_str = 'floor-' + floor.id + '-0';

                        for (var j = 1; j < vert_groups.length; j++)
                            group_str += ',floor-' + floor.id + '-' + j;


                        for (var j = 0; j < vert_groups.length; j++)
                        {
                            var verticies = $.parseJSON(vert_groups[j]),
                                coords = '';
                            for (var i = 0; i < verticies.length; i++)
                            {
                                var vertex = verticies[i];
                                if (coords != '') coords += ',';
                                coords += vertex[0] + ',' + vertex[1];
                            }
                            $map.append($('<area />', {
                                'id': 'floor-' + floor.id + '-' + j,
                                'data-id': floor.id,
                                'data-key': 'floor-' + floor.id,
                                'onclick': "return mouseClickFloor(this);",
                                'onmousemove': 'mouseOverFloor(this);',
                                'onmouseover': 'mouseOverFloor(this);',
                                'onmouseout': 'mouseOutFloor(this);',
                                'shape': 'poly',
                                'rel': group_str,
                                'class': 'forcegroup',
                                'href': 'floor.php?id=' + floor.id,
                                'coords': coords
                            }));
                        }
					}
					
					var img = new Image();
					img.onload = function() {
						var $img = $('<img />', {
							'src': imgsrc,
							'class': '',
							'style': 'max-width: none;',
							'width': retina ? this.naturalWidth/2 : this.naturalWidth,
							'height': retina ? this.naturalHeight/2 : this.naturalHeight,
							'usemap': '#bldgmap'
						});
						
						$('#fp-cont').html($img);
                        $img.mapster({
                            mapKey: 'data-key',
                            render_highlight: {
                                fillColor: settings.floor.color_hover.inner.hex_raw,
                                fillOpacity: settings.floor.color_hover.inner.a
                            }
                        });
                        //$img.mapster('highlight', true);
                        var $w = $(window);
                        $w.resize(function()
                        {
                            $img.mapster('resize', $w.width()*0.9);
                            if($img.height() > $w.height()*0.7)
                                $img.mapster('resize', undefined, $w.height()*0.7);
                        });
                        $w.trigger('resize');
                        $('#fp-wrapper').show();
					};
					img.src = imgsrc;
					
					var mousepos = {x: 0, y: 0};
					$(window).mousemove(function(e)
					{
						mousepos.x = e.pageX + 30;
						mousepos.y = e.pageY - $('#fp-wrapper').offset().top - 10;
					});
					
					window.mouseClickFloor = function(area)
					{
                        if(!rpc) return true;
						var $this = $(area);
						var floor = razz.apt.floors[$this.data('id')];
						<?php if($settings['ibldg_link'] == 'search'): ?>
						rpc.setHash("!/floor-plans/search/floor/" + floor.id);
						<?php else: ?>
						rpc.setHash("!/floor-plans/floor/" + floor.id);
						<?php endif; ?>
						return false;
					};
					window.mouseOverFloor = function(area)
					{
						var $this = $(area),
							floor = razz.apt.floors[$this.data('id')],
							html = floor.title ? floor.title : 'FLOOR ' + floor.id;

						$('#floorhover').css({
							top: mousepos.y,
							left: mousepos.x
						}).html(html).show();
					};
					window.mouseOutFloor = function(area)
					{
						$('#floorhover').hide();
					};
				}
				
				function getColor(v)
				{
					var r = 0, g = 0, b = 0, a = 1;

					var expr1 = /([0-9]+%?)[^0-9A-F]+([0-9]+%?)[^0-9A-F]+([0-9]+%?)(?:[^\d.]+([\d.]+))?/i;
					var expr2 = /([0-9A-F][0-9A-F])([0-9A-F][0-9A-F])([0-9A-F]?[0-9A-F]?)/i;
					var expr3 = /([0-9A-F])([0-9A-F]?)([0-9A-F]?)/i;
					if (expr1.exec(v))
					{
						r = RegExp.$1;
						g = RegExp.$2;
						b = RegExp.$3;
						if (RegExp.$4) if(RegExp.$4.length > 0) a = 1 * RegExp.$4;
						if (r.indexOf("%") >= 0) r = Math.round(parseInt(r) * 2.55);
						if (g.indexOf("%") >= 0) g = Math.round(parseInt(g) * 2.55);
						if (b.indexOf("%") >= 0) b = Math.round(parseInt(b) * 2.55);
					}
					else if (expr2.exec(v))
					{
						r = parseInt((RegExp.$1 + "00").substr(0, 2), 16);
						g = parseInt((RegExp.$2 + "00").substr(0, 2), 16);
						b = parseInt((RegExp.$3 + "00").substr(0, 2), 16);
					}
					else if (expr3.exec(v))
					{
						r = parseInt("0" + RegExp.$1 + RegExp.$1, 16);
						g = parseInt("0" + RegExp.$2 + RegExp.$2, 16);
						b = parseInt("0" + RegExp.$3 + RegExp.$3, 16);
					}

					r = Math.min(Math.max(0, r), 255);
					g = Math.min(Math.max(0, g), 255);
					b = Math.min(Math.max(0, b), 255);
					while (a > 1) a /= 100;
                    var hex = (r < 16 ? "0" : "") + r.toString(16) + (g < 16 ? "0" : "") + g.toString(16) + (b < 16 ? "0" : "") + b.toString(16);
					
					return {
						r: r,
						g: g,
						b: b,
						a: a,
                        hex_raw: hex,
						hex: "#" + hex
					};
				}

                rpc.setTitle('Floor Plans');
			});
		</script>
    </body>
</html>