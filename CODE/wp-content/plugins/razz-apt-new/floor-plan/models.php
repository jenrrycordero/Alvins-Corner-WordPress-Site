<?php
define('WP_USE_THEMES', false);
if(!defined('ABSPATH')) require_once('../../../../wp-blog-header.php');
header('HTTP/1.1 200 OK', true, 200);
$settings = get_option('razz_apt_opt');

$adata = AptData::Instance();

$models = $adata->_('models');
foreach($models as $key=>$value)
{
	if($value['disabled'])
	{
		unset($models[$key]);
		continue;
	}
	$models[$key]['bed_disp'] = ($value['rooms']['bedroom'] !== 0) ? $value['rooms']['bedroom'] . 'Bed' : 'Studio';
	$sqftmin = $value['sqft']['min'];
	$sqftmax = $value['sqft']['max'];

	if($sqftmin != $sqftmax)
	{
		$models[$key]['sqft_disp'] = "{$sqftmin} - {$sqftmax}";
	}
	else
	{
		$models[$key]['sqft_disp'] = $sqftmin;
	}
}

$tracked_terms = explode(' ', $settings['lb_models_terms']);
$terms = json_decode(json_encode(get_terms('model-tags')), true);
foreach($terms as $key=>$term)
{
	if(!in_array($term['slug'], $tracked_terms))
	{
		unset($terms[$key]);
	}
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

		div{
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}

		#wrapper-container{
			position: fixed;
			top: 143px;
			left: 0;
			right: 0;
			bottom: 0;
			overflow-x: hidden;
			overflow-y: scroll; /* has to be scroll, not auto */
			-webkit-overflow-scrolling: touch;
		}

		#wrapper{
			margin: 80px;
		}

		.item{
			width: 20%;
			position: absolute;
		}

		.item-contents{
			background: <?= $settings['lb_models_item_bgcolor'] ?>;
			margin: 5px;
			cursor: pointer;
			box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
		}

		.item-contents:hover{
			opacity: 0.9;
		}

		.item img{
			max-width: 100%;
		}

		.item h3{
			display: inline-block;
			margin: 0;
		}

		.item .details-header {
			height: 40px;
			background: <?= $settings['lb_models_details_header_bgcolor'] ?>;
		<?= $options_panel->getFontCSS($settings['lb_models_details_header_font']); ?>
		}

		.item .details-header > div{
			position: relative;
			width: 100%;
			float: left;
			top: 50%;
			padding: 1px 5px;
			text-align: center;
			-webkit-transform: translateY(-50%);
			-moz-transform: translateY(-50%);
			-ms-transform: translateY(-50%);
			-o-transform: translateY(-50%);
			transform: translateY(-50%);
		}

		.item .details-footer {
			background: <?= $settings['lb_models_details_footer_bgcolor'] ?>;
		<?= $options_panel->getFontCSS($settings['lb_models_details_footer_font']); ?>
			height: 40px;
		}

		.item .details-footer > div{
			position: relative;
			width: 50% !important;
			float: left;
			top: 50%;
			padding: 1px 5px;
			text-align: center;
			-webkit-transform: translateY(-50%);
			-moz-transform: translateY(-50%);
			-ms-transform: translateY(-50%);
			-o-transform: translateY(-50%);
			transform: translateY(-50%);
		}

		@media screen and (max-width: 1400px)
		{
			.item{
				width: 25%;
			}
		}
		@media screen and (max-width: 1140px)
		{
			#wrapper{
				margin: 60px;
			}
			.item{
				width: 33.33%;
			}
		}
		@media screen and (max-width: 820px)
		{
			.item{
				width: 50%;
			}
		}
		@media screen and (max-width: 680px)
		{
			#wrapper{
				margin: 20px;
			}
		}
		@media screen and (max-width: 530px)
		{
			#wrapper{
				margin: 40px;
			}
			.item{
				width: 100%;
			}
		}
		@media screen and (max-width: 380px)
		{
			#wrapper{
				margin: 20px;
			}
		}

		#filter-wrapper{
			position: fixed;
			top: 55px;
			left: 0;
			width: 100%;
			height: 88px;
			background: <?= $settings['lb_models_filters_bgcolor'] ?>;
			box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
			text-align: center;
			z-index: 20;
		}

		#filter-wrapper .inner{
			position: relative;
			top: 50%;
			-webkit-transform: translateY(-50%);
			-moz-transform: translateY(-50%);
			-ms-transform: translateY(-50%);
			-o-transform: translateY(-50%);
			transform: translateY(-50%);
		}

		#filter-wrapper .inner a{
			white-space: nowrap;
		<?= $options_panel->getFontCSS($settings['lb_models_filters_font']); ?>
			text-decoration: none;
			margin: 5px 10px;
		}

		#filter-wrapper .inner a.selected{
			text-decoration: underline;
		}

		#filter-wrapper .inner a:hover{
			opacity: 0.8;
		}

		.nowrap{
			white-space: nowrap;
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

<!-- Content -->
<!-- Filter Bar -->
<div id="filter-wrapper">
	<div class="inner">
		<a href="#" data-filter="*" class="selected">All</a>
		<?php foreach($terms as $term): ?>
		<a href="#" data-filter=".<?= $term['slug'] ?>"><?= $term['name'] ?></a>
		<?php endforeach; ?>
	</div>
</div>

<!-- Isotope -->
<div id="wrapper-container">
	<div id="wrapper">
		<?php foreach($models as $model): ?>
			<div class="item <?= implode(' ', array_map(function($term){return strtolower(str_replace(' ', '-', $term));}, $model['terms'])) ?>">
				<div class="item-contents goto-model" data-model="<?= $model['name'] ?>">
					<div class="details-header">
						<div><h3><?= $model['name'] ?></h3> <?= $model['bed_disp'] ?>/<?= $model['rooms']['bathroom'] ?>Bath</div>
					</div>
					<img src="<?= $model['image']; ?>" alt=""/>
					<div class="details-footer">
						<div><?php  echo getFormattedRent($model) ?></div>
						<div><?= $model['sqft_disp'] ?> <span class="nowrap">SQ FT</span></div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>



<div id="menu-dd" class="dropdown dropdown-tip">
	<ul class="dropdown-menu">
		<?php if(!$settings['widget_hide_floors']): ?><li><a href="./floors.php" class="goto-floors hidemobile">Floors</a></li><?php endif; ?>
		<?php if(!$settings['widget_hide_search']): ?><li><a href="./search.php" class="goto-search"><?= $settings['txt_search'] ? $settings['txt_search'] : 'Search' ?></a></li><?php endif; ?>
	</ul>
</div>

<div id="footer">
	<div id="msg">Powered by <a target="_blank" href="http://razzinteractive.com">Razz Interactive</a> + <a target="_blank" href="http://gotomyapartment.com">GoToMyApartment</a></div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.0.min.js"><\/script>')</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/2.0.0/isotope.pkgd.min.js"></script>
<script type="text/javascript" src="../xdm/easyXDM.js"></script>
<script type="text/javascript" src="js/xdm.js?ver=1.0.1"></script>
<script type="text/javascript" src="js/jquery.dropdown.min.js"></script>

<script type="text/javascript">
	jQuery(document).ready(function($)
	{
		if(rpc) rpc.setTitle('Models');
		var $container = $('#wrapper'),
			$filters = $('#filter-wrapper');

		$(window).load(function(){
			$container.isotope({
				itemSelector: '.item',
				layoutMode: <?= json_encode($settings['lb_models_layout_mode']) ?>
			});

		});

		$filters.find('a').filter(function(){return !$($(this).data('filter')).length;}).remove();

		$filters.on('click', 'a', function(e)
		{
			e.preventDefault();
			$filters.find('a').removeClass('selected');
			$(this).addClass('selected');
			$container.isotope({
				filter: $(this).data('filter')
			});
		});

	});
</script>
</body>
</html>