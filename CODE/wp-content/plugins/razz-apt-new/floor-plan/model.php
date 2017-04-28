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
	
	if(($targetmodel = $adata->_('models.^id')) !== false)
		$model = $targetmodel;
	elseif(($targetmodel = $adata->_search('models', 'name', $_GET['id'])) !== false)
		$model = $targetmodel[0];
	else
	{
		echo "Invalid Model";
		exit;
	}
	$order = array();
	foreach($models as $m)
	{
		$order[] = $m['name'];
	}
	sort($order);
	$index = array_search($model['name'], $order);
	if($index === false)
	{
		echo "Invalid Model";
		exit;
	}
	
	$modeldata = array(
        'name'          => $model['name'],
		'rent'			=> getFormattedRent($model),
		'bed'			=> $model['rooms']['bedroom'],
        'bed_disp'      => ($model['rooms']['bedroom'] == 0) ? 'Studio' : $model['rooms']['bedroom'] . ' BD',
		'bath'			=> $model['rooms']['bathroom'],
		'image'			=> $model['image'],
		'bldg'			=> $adata->_('ident'),
        'applyurl'      => $model['applyurl']
	);
	
	$units = $adata->_search('units', 'model', $model['id']);
    foreach($units as $key=>$value)
    {
        if($value['disabled']) unset($units[$key]);
    }
	$unitcount = $units ? count($units) : 0;
	
	$sqftmin = $model['sqft']['min'];
	$sqftmax = $model['sqft']['max'];
	
	if($sqftmin != $sqftmax)
	{
		$modeldata['sqft'] = "{$sqftmin} - {$sqftmax}";
		$modeldata['sqft_raw'] = round(($sqftmin + $sqftmax) / 2, 2);
	}
	else
	{
		$modeldata['sqft'] = $modeldata['sqft_raw'] = $sqftmin;
	}


    if(!empty($settings['uv_applyurl']))
    {
        $applyurl = $settings['uv_applyurl'];
        foreach ( $modeldata as $key => $value ) {
            $applyurl = str_replace( "%{$key}%", $value, $applyurl );
        }
        $modeldata['applyurl'] = $applyurl;
    }
    $modeldata['baseurl'] = (empty($settings['uv_email_url_override'])) ? home_url() : $settings['uv_email_url_override'];
	
	$msgsubject = rapt_parse_email_content($settings['uv_email_subject'], $modeldata);
	$msgbody = rapt_parse_email_content($settings['uv_email_body'], $modeldata);
	

	function rapt_parse_email_content($input, $modeldata)
	{
		$output = $input; //search, replace
		$output = str_replace('[br]', "\r\n", $output);
		$output = str_replace('[prop_name]', $modeldata['bldg']['MarketingName'], $output);
        $output = str_replace('[title]', "Model " . $modeldata['name'], $output);
		$output = str_replace('[unit_num]', $modeldata['name'], $output);
		$output = str_replace('[unit_beds]', $modeldata['bed'], $output);
		$output = str_replace('[unit_baths]', $modeldata['bath'], $output);
		$output = str_replace('[unit_sqft]', $modeldata['sqft'], $output);
        $output = str_replace('[unit_url]', "{$modeldata['baseurl']}#!/floor-plans/unit/{$modeldata['name']}", $output);
		$output = rawurlencode($output);
		return $output;
	}
	
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




$applyNowLabel = ( isset($settings['uv_applynow_label']) && $settings['uv_applynow_label'] != "" ) ? $settings['uv_applynow_label'] : "Apply Now";
	
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>Model <?php echo $model['name']; ?> - Floor Plans - <?= get_bloginfo('name'); ?></title>
		<meta property="og:title" content="Model <?php echo $model['name']; ?> - <?= get_bloginfo('name'); ?>" />
		<meta property="og:description" content="<?= $settings['widget_description'] ?>" />
		<meta property="og:image" content="<?php echo $model['image']; ?>" />
		<meta property="og:type" content="website" />
        <meta name="description" content="<?= $settings['widget_description'] ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	    <base href="<?= plugin_dir_url(__FILE__); ?>">

        <link rel="stylesheet" href="css/normalize.min.css">
		<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,700|Oswald:400,700' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="css/main.css">
        <link type="text/css" rel="stylesheet" href="css/jquery.dropdown.css" />

        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
		<style type="text/css">
            body{
                <?php echo $options_panel->getFontCSS($settings['lb_mainfont']); ?>
            }
            a{
	            outline: 0;
            }
            img { -ms-interpolation-mode: bicubic; }
            #content-wrap .model-details .model-name{
                <?php echo $options_panel->getFontCSS($settings['uvlb_header_text']); ?>
            }
			#content-wrap .model-details .model-desc{
				color: <?php echo $settings['uv_lightbox']['uvlb_subheader_fgcolor']; ?>;
			}
			#content-wrap .model-info-row .info-col p{
				background: <?php echo $settings['uv_lightbox']['uvlb_inforow_bgcolor']; ?>;
				color: <?php echo $settings['uv_lightbox']['uvlb_inforow_fgcolor']; ?>;
			}
            #fp-modal-wrap .model-moreinfo td{
                color: <?= $settings['uv_lightbox']['uvlb_table_value']; ?>;
            }
            #fp-modal-wrap .model-moreinfo td:first-child{
                color: <?= $settings['uv_lightbox']['uvlb_table_header']; ?>;
            }
            #fp-modal-wrap a{
                color: <?= $settings['uv_lightbox']['uvlb_a']; ?>;
            }
            #fp-modal-wrap a:hover{
                color: <?= $settings['uv_lightbox']['uvlb_a_hover']; ?>;
            }
			#content-wrap .model-links-wrap a{
				background: <?php echo $settings['uv_lightbox']['uvlb_link_bgcolor']; ?>;
				color: <?php echo $settings['uv_lightbox']['uvlb_link_fgcolor']; ?>;
				border: 2px solid <?php echo $settings['uv_lightbox']['uvlb_link_bordercolor']; ?>;
			}
			#content-wrap .model-links-wrap a.active{
				background: <?php echo $settings['uv_lightbox']['uvlb_link_active_bgcolor']; ?>;
				color: <?php echo $settings['uv_lightbox']['uvlb_link_active_fgcolor']; ?>;
                border: 2px solid <?php echo $settings['uv_lightbox']['uvlb_link_active_bordercolor']; ?>;
			}
			#content-wrap .model-links-wrap a:hover{
				background: <?php echo $settings['uv_lightbox']['uvlb_link_hover_bgcolor']; ?>;
				color: <?php echo $settings['uv_lightbox']['uvlb_link_hover_fgcolor']; ?>;
                border: 2px solid <?php echo $settings['uv_lightbox']['uvlb_link_hover_bordercolor']; ?>;
			}
			#content-wrap .model-info-row:after{
				background: transparent;
			}
			#content-wrap .model-preview > .model-preview-wrap{
				border: 1px solid <?php echo $settings['uv_lightbox']['uvlb_image_bordercolor']; ?>;
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
                 cursor: default;
                 text-align: center;
                 margin-bottom: 10px;
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
                color: <?=$settings['lb_footer_a']?>;
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

            .fb-like{
                padding: 10px 0 15px;
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

            .wraptocenter {
                display: table-cell;
                text-align: center;
                vertical-align: middle;
            }
            .wraptocenter * {
                vertical-align: middle;
            }
            /*\*//*/
			.wraptocenter {
				display: block;
			}
			.wraptocenter span {
				display: inline-block;
				height: 100%;
				width: 1px;
			}
			/**/
        </style>
        <!--[if lt IE 8]><style>
            .wraptocenter span {
                display: inline-block;
                height: 100%;
            }
        </style><![endif]-->
    </head>
    <body>
	    <div id="fb-root"></div>
<!--            <script>(function(d, s, id) {-->
<!--                    var js, fjs = d.getElementsByTagName(s)[0];-->
<!--                    if (d.getElementById(id)) return;-->
<!--                    js = d.createElement(s); js.id = id;-->
<!--                    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=547477008647263&version=v2.0";-->
<!--                    fjs.parentNode.insertBefore(js, fjs);-->
<!--                }(document, 'script', 'facebook-jssdk'));</script>-->
            <div id="header">
            <div>
                <ul>
                    <li><a href="#" class="" data-dropdown="#menu-dd">Floor Plans <i class="fa fa-chevron-circle-down ddb"></i></a></li>
                    <li><i class="fa fa-angle-right fa-lg"></i></li>
                    <li>
                        <a>Model <?= $model['name']; ?></a>
                        <a href="#" id="model-ddb" data-dropdown="#model-dd" class="fa fa-chevron-circle-down ddb" style="text-decoration: none;"></a>
                        <div id="model-dd" class="dropdown dropdown-tip">
                            <ul class="dropdown-menu">
                                <?php foreach($models as $m): ?>
                                <li><a href="model.php?id=<?= htmlentities($m['name']) ?>" class="goto-model" data-model="<?= htmlentities($m['name']) ?>">Model <?= htmlentities($m['name']) ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <a href="#" class="closebtn">×</a>
        <div id="fp-modal-wrap" style="padding-top: 75px;">
            <div class="wrapper">
                <div id="content-wrap">
                    <div class="primary">
                        <div class="model-info-row">
                            <div class="info-col">
                                <p><?php echo $modeldata['bed_disp']; ?> / <?php echo $modeldata['bath']; ?> BA</p>
                            </div>
                            <div class="info-col">
                                <p><?= number_format($modeldata['sqft']) ?> SQFT</p>
                            </div>
                            <div class="info-col">
                                <p class="mobile-bold"><?php echo $modeldata['rent'] ?></p>
                            </div>
                        </div>
                        <!-- //.model-info-row -->

                        <div class="model-preview">
                            <a href="?id=<?= urlencode(getPrev()) ?>" class="fp-modal-prev goto-model" data-model="<?= htmlentities(getPrev()) ?>"></a>
                            <a href="?id=<?= urlencode(getNext()) ?>" class="fp-modal-next goto-model" data-model="<?= htmlentities(getNext()) ?>"></a>
                            <div class="model-preview-wrap">
                                <div class="wraptocenter"><span></span><img src="<?= $modeldata['image'] ?>" alt=""></div>
                            </div>
                            <!-- //.wrap -->
                        </div>
                        <!-- //.model-preview -->

                    </div>
                    <!-- //.primary -->

                    <div class="sidebar">
                        <div class="model-details">
                            <h1 class="model-name">Model <?= $model['name'] ?></h1>
                            <p class="model-desc" style="display:none;">
                                <?php echo $unitcount; ?> Units Available <a href="#" id="unit-ddb" data-dropdown="#unit-dd" class="fa fa-chevron-circle-down ddb" style="text-decoration: none;"></a>
                            </p>
                            <p class="model-desc">
                                Contact Us for More details
                            </p>
                        </div>
                        <!-- //.model-details -->

                        <div class="model-links-wrap">
                            <a href="<?php echo $modeldata['applyurl']; ?>" target="_blank" style="display: none" ><?php echo $applyNowLabel; ?></a>
                            <a href="<?php echo home_url("/?action=pdf&model={$model['name']}"); ?>" class="active" target="_blank">Print PDF</a>
                            <a href="mailto:?subject=<?php echo $msgsubject; ?>&body=<?php echo $msgbody; ?>" target="_blank">Email a Friend</a>
                            <a href="<?= empty($settings['uv_contacturl']) ? home_url("/contact") : $settings['uv_contacturl']; ?>" target="_top">Contact Us</a>
                        </div>
<!--	                    <div class="fb-like" data-href="--><?//= empty($settings['widget_external_url']) ? home_url() . '/' : $settings['widget_external_url']; ?><!--#!/floor-plans/model/--><?//= $model['name']; ?><!--" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>-->
                        <!-- //.model-links-wrap -->

                        <div style="font-size: 12px">
                            Renderings are intended to be a general reference only. Unit sizes are approximations. Features, materials, finishes, dimensions, overall layout of units, and all lease terms (including but not limited to pricing and availability) may be different than shown here and all are subject to revision without prior notice.
                        </div>

                        <div id="footer">
                            <div id="msg">Powered by <a target="_blank" href="http://razzinteractive.com">Razz Interactive</a> + <a target="_blank" href="http://gotomyapartment.com">GoToMyApartment</a></div>
                        </div>
                    </div>
                    <!-- //.sidebar -->

                    <div class="clearfix"></div>
                </div>
                <!-- //#content-wrap -->
            </div>
            <!-- //.wrapper -->

            
        </div>
        <!-- //#fp-modal-wrap -->

        <div id="unit-dd" class="dropdown dropdown-tip">
            <ul class="dropdown-menu">
                <?php foreach($units as $unit): ?>
                    <li><a href="unit.php?id=<?= urlencode($unit['id']) ?>" class="goto-unit" data-unit="<?= htmlentities($unit['id']) ?>">#<?= htmlentities($unit['id']) ?></a></li>
                <?php endforeach; ?>
            </ul>
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
        <script type="text/javascript" src="../xdm/easyXDM.js"></script>
        <script type="text/javascript" src="js/xdm.js?ver=1.0.1"></script>
        <script type="text/javascript" src="js/hammer.min.js"></script>

        <script src="js/main.js"></script>
		
		<script type="text/javascript">
			jQuery(document).ready(function($)
			{
                if(rpc) rpc.setTitle('Model <?= $model['name'] ?>');
                var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test( (window.navigator.userAgent||window.navigator.vendor||window.opera) );
                if(isMobile) {
                    var body = $("body")[0];
                    Hammer(body).on('swiperight', function (e) {
                        $('.fp-modal-prev').click();
                    }).on('swipeleft', function (e) {
                        $('.fp-modal-next').click();
                    });
                }
			});
		</script>
    </body>
</html>