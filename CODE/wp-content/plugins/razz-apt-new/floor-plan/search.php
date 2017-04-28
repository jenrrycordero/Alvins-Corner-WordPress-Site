<?php
define('WP_USE_THEMES', false);
if(!defined('ABSPATH')) require_once('../../../../wp-blog-header.php');
header('HTTP/1.1 200 OK', true, 200);
$settings = get_option('razz_apt_opt');

$adata = AptData::Instance();
$beds = $adata->_('rooms.bedroom');
$baths = $adata->_('rooms.bathroom');

$vars = array(
    'studio_size' => $settings['qs_filter_display_font_studio'],
    'filter'	=> array('bed'=>$beds, 'bath'=>$baths),
    'units'		=> array()
);

$units = $adata->_('units');

$filter = $_GET['filter'] ? $_GET['filter'] : '';
$filter_value = $_GET['filter_value'] ? $_GET['filter_value'] : '';

foreach($units as $unit)
{
    if($unit['disabled']) continue;
    $model = $adata->_('models.' . $unit['model']);
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
        'date'			=> (($timestamp = strtotime($unit['date'])) !== false) ? strtoupper(date("M j, Y", $timestamp)) : '',
        'image'			=> $model['image']
    );


    if($aptdata['bedroom'] == 0) $aptdata['bedroom'] = 'Studio';
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

	if($filter && $aptdata[$filter] != $filter_value) continue;


    ob_start();
    ?>
    <td><a href="unit.php?id=<?= urlencode($unit['id']) ?>" class="goto-unit" data-unit="<?= htmlentities($unit['id']) ?>"><div class="qsviewdetails" style="background: <?php echo $settings['qs_viewdetails_bg']; ?>;"><div class="qsviewdetails_plus" style="background: <?php echo $settings['qs_viewdetails_bgplus']; ?>; color: <?php echo $settings['qs_viewdetails_fg']; ?>; float: left;"><span>+</span></div><span>VIEW DETAILS</span></div></a></td>
    <td><a href="unit.php?id=<?= urlencode($unit['id']) ?>" class="goto-unit" data-unit="<?= htmlentities($unit['id']) ?>"><div class="qsviewdetails_plus" style="background: <?php echo $settings['qs_viewdetails_bgplus']; ?>; color: <?php echo $settings['qs_viewdetails_fg']; ?>;"><span>+</span></div></a></td>
    <?php
    $aptdata['buttons'] = ob_get_clean();

    $vars['units'][] = $aptdata;
}
$models = $adata->_('models');
foreach($models as $key=>$value)
{
	if($value['disabled']) unset($models[$key]);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta property="og:title" content="<?= $settings['txt_search'] ? $settings['txt_search'] : 'Search' ?> - <?= get_bloginfo('name'); ?>" />
    <meta property="og:description" content="<?= $settings['widget_description'] ?>" />
    <meta property="og:type" content="website" />
    <meta name="description" content="<?= $settings['widget_description'] ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <base href="<?= plugin_dir_url(__FILE__); ?>">
    <title><?= $settings['txt_search'] ? $settings['txt_search'] : 'Search' ?></title>
    <link rel="stylesheet" href="css/normalize.min.css">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link type="text/css" rel="stylesheet" href="css/jquery.dropdown.css" />
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/qs.css" rel="stylesheet" />
    <link href="../css/ft/footable.core.css" rel="stylesheet" />
    <link href="../css/ft/footable.standalone.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Lato:400|Open+Sans:300|Raleway:100|Oswald:400,700' rel='stylesheet' type='text/css'>

    <style>
        body{
            <?php echo $options_panel->getFontCSS($settings['lb_mainfont']); ?>
        }
        a{
	        outline: 0;
        }
        #qshead{
            background-color: <?php echo $settings['qs_headerbar_bgcolor']; ?>;
        }
        #qshead > p{
        <?php echo $options_panel->getFontCSS($settings['qs_headerbar_font']); ?>
            margin: 0;
        }
        #qsrighthead{
            background-color: <?php echo $settings['qs_subheaderbar_bgcolor']; ?>;
        }
        #qsrighthead > p{
        <?php echo $options_panel->getFontCSS($settings['qs_subheaderbar_font']); ?>
        }

        #ib_infobar{
            top: <?php echo $settings['qs_ibldg_infobar_offset']; ?>px;
        }
        #ib_infobar_sec1{
            background: <?php echo $settings['qs_ibldg_infobar_bgcolor']; ?>;
            color: <?php echo $settings['qs_ibldg_infobar_fgcolor']; ?>;
        }

        #qsfilters
        {
            display: inline-block;
            width: 100%;
            height: 31px;
            text-align: center;
        }
        #qsfilters a
        {
            display: inline-block;
            width: 200px;
            height: 31px;
            background: <?php echo $settings['qs_filter_custom_bg']; ?>;
        <?php echo $options_panel->getFontCSS($settings['qs_filter_custom_font']); ?>
            line-height: 2;
            margin: 2px;
        }
        #qsfilters a.on
        {
            background: <?php echo $settings['qs_filter_custom_bg_on']; ?>;
        }


        #qsrightbody{
            background-color: <?php echo $settings['qs_table_body_bg']; ?> !important;
            position: relative;
            overflow: hidden;
        }
        #qsbar{
            padding-top:55px;
        }
        #qsbar a{
            text-decoration: none;
        }
        #qsbar, #qsleft{
            background-color: <?php echo $settings['qs_filter_bg']; ?> !important;
        }
        #qsleft{
        <?php echo $options_panel->getFontCSS($settings['qs_filter_font']); ?>
        }
        .qsfilter_disp_circle{
            background: <?php echo $settings['qs_filter_display_bg']; ?>;
        <?php echo $options_panel->getFontCSS($settings['qs_filter_display_font']); ?>
        }
        .qsfilter_btn_circle{
        <?php echo $options_panel->getFontCSS($settings['qs_filter_btn_font']); ?>
        }
        .qsfilter_btn_circle.btnup{
            background: <?php echo $settings['qs_filter_btn_plus']; ?>;
        }
        .qsfilter_btn_circle.btnup:hover{
            background: <?php echo $settings['qs_filter_btn_plus_hover']; ?>;
        <?php echo $options_panel->getFontCSS($settings['qs_filter_btn_font']); ?>
        }
        .qsfilter_btn_circle.btndn{
            background: <?php echo $settings['qs_filter_btn_minus']; ?>;
        }
        .qsfilter_btn_circle.btndn:hover{
            background: <?php echo $settings['qs_filter_btn_minus_hover']; ?>;
        <?php echo $options_panel->getFontCSS($settings['qs_filter_btn_font']); ?>
        }
        .qsfilter_btn_circle.disabled{
            background: <?php echo $settings['qs_filter_btn_disabled']; ?> !important;
            cursor: default;
        }


        .footable > thead > tr > th,
        .footable > thead > tr > td
        {
            background-color: <?php echo $settings['qs_table_header_bg']; ?> !important;
        <?php echo $options_panel->getFontCSS($settings['qs_table_header_font'], true); ?>
        }
        .footable > tbody > tr:hover{
            background-color: <?php echo $settings['qs_table_body_bg_hover']; ?> !important;
        }
        .footable{
        <?php echo $options_panel->getFontCSS($settings['qs_table_body_font'], true); ?>
        }
        .footable-page > a {
            background-color: <?php echo $settings['qs_table_page_bg']; ?> !important;
        <?php echo $options_panel->getFontCSS($settings['qs_table_page_font'], true); ?>
        }
        .footable-page-arrow > a {
            background-color: <?php echo $settings['qs_table_page_bg']; ?> !important;
        <?php echo $options_panel->getFontCSS($settings['qs_table_page_font'], true); ?>
        }
        .footable-page > a:hover {
            background-color: <?php echo $settings['qs_table_page_bg_hover']; ?> !important;
        <?php echo $options_panel->getFontCSS($settings['qs_table_page_font'], true); ?>
        }
        .footable-page.active > a {
            background-color: <?php echo $settings['qs_table_page_bg_active']; ?> !important;
        <?php echo $options_panel->getFontCSS($settings['qs_table_page_font'], true); ?>
        }
        .footable-page-arrow.disabled > a {
            background-color: <?php echo $settings['qs_table_page_bg_disabled']; ?> !important;
        <?php echo $options_panel->getFontCSS($settings['qs_table_page_font'], true); ?>
        }

        #fp-modal-wrap a{
            color: <?= $settings['lb_info_a']; ?>;
        }
        #fp-modal-wrap a:hover{
            color: <?= $settings['lb_info_a_hover']; ?>;
        }

        #content-wrap .model-details .model-name{
            color: <?php echo $settings['uv_lightbox']['uvlb_header_fgcolor']; ?>;
        }
        #content-wrap .model-details .model-desc{
            color: <?php echo $settings['uv_lightbox']['uvlb_subheader_fgcolor']; ?>;
        }
        #content-wrap .model-info-row .info-col p{
            background: <?php echo $settings['uv_lightbox']['uvlb_inforow_bgcolor']; ?>;
            color: <?php echo $settings['uv_lightbox']['uvlb_inforow_fgcolor']; ?>;
        }
        #content-wrap .model-links-wrap a{
            background: <?php echo $settings['uv_lightbox']['uvlb_link_bgcolor']; ?>;
            color: <?php echo $settings['uv_lightbox']['uvlb_link_fgcolor']; ?>;
            border: 2px solid <?php echo $settings['uv_lightbox']['uvlb_link_bordercolor']; ?>;
        }
        #content-wrap .model-links-wrap a.active{
            background: <?php echo $settings['uv_lightbox']['uvlb_link_active_bgcolor']; ?>;
            color: <?php echo $settings['uv_lightbox']['uvlb_link_active_fgcolor']; ?>;
        }
        #content-wrap .model-links-wrap a:hover{
            background: <?php echo $settings['uv_lightbox']['uvlb_link_hover_bgcolor']; ?>;
            color: <?php echo $settings['uv_lightbox']['uvlb_link_hover_fgcolor']; ?>;
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
            cursor: default;
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
<div id="header">
    <div>
        <ul>
            <li>
                <a href="#" class="" data-dropdown="#menu-dd">Floor Plans <i class="fa fa-chevron-circle-down ddb"></i></a>
            </li>
            <li><i class="fa fa-angle-right fa-lg"></i></li>
            <li><a<?php if($filter): ?> href="./search.php" class="goto-search"<?php endif; ?>><?= $settings['txt_search'] ? $settings['txt_search'] : 'Search' ?></a></li>
	        <?php if($filter): ?>
			    <li><i class="fa fa-angle-right fa-lg"></i></li>
			    <li><a><?= ucwords(esc_html($filter)) ?> <?= ucwords(esc_html($filter_value)) ?></a></li>
	        <?php endif; ?>
        </ul>
    </div>
</div>
<a href="#" class="closebtn">×</a>
<div id="qsbar">
    <div id="qsheadwrapper">
        <div id="qshead">
            <p><i class="fa fa-search" style="margin-right: 11px; font-size: 11px; line-height: 16px; vertical-align: middle;"></i>QUICK SEARCH</p>
        </div>
    </div>
    <div id="qsbody">
        <div id="qsleftwrapper">
            <div id="qsleft">
                <div id="qscnt">
                    <div id="qscntbed">
                        BEDS
                        <div id="qsfilter_disp_bed" class="qsfilter_disp_circle">ANY</div>
                        <center><a href="#" class="qsfilter_btn_circle btnup" data-type="bed" data-inc="1"></a> <a href="#" class="qsfilter_btn_circle btndn disabled" data-type="bed" data-inc="-1"></a></center>
                    </div>
                    <div id="qscntbath">
                        BATHS
                        <div id="qsfilter_disp_bath" class="qsfilter_disp_circle">ANY</div>
                        <center><a href="#" class="qsfilter_btn_circle btnup" data-type="bath" data-inc="1"></a> <a href="#" class="qsfilter_btn_circle btndn disabled" data-type="bath" data-inc="-1"></a></center>
                    </div>
                </div>
                <div id="qsfilters">

                </div>
            </div>
        </div>
        <div id="qsright">
            <div id="qsrighthead">
                <p>YOUR SEARCH RESULTS:</p>
            </div>
            <div id="qsrightbody">
                <table id="qsdata" class="footable table" data-page-navigation=".pagination" data-page-size="10">
                    <thead>
                    <tr>
                        <th data-toggle="true" data-sort-initial="true">APT#</th>
                        <th data-type="numeric">BEDROOMS</th>
                        <th data-type="numeric">BATHS</th>
                        <th data-hide="small,xsmall" data-type="numeric">SQ. FT.</th>
                        <th data-hide="medium,phone,small,xsmall" data-type="numeric">MONTH RENT</th>
                        <th data-hide="tablet,medium,phone,small,xsmall">AVAILABLE</th>
                        <th data-hide="phone,small,xsmall" data-sort-ignore="true" data-ignore="true"></th>
                        <th data-hide="default,tablet,medium" data-sort-ignore="true" data-ignore="true"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr><td colspan="7"><img src='<?= $settings['img_loader']['src'] ?>' style="margin-right: 10px;" /> Loading Apartments...</td></tr>
                    </tbody>
                    <tfoot <?= !count($vars['units']) ? 'style="display: block;"':''; ?>>
                    <tr><td colspan="8">No apartments found that match your criteria. <?php if($filter && !count($vars['units'])): ?><a href="./search.php" class="goto-search">View All<?php endif; ?></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="menu-dd" class="dropdown dropdown-tip">
    <ul class="dropdown-menu">
        <?php if(!$settings['widget_hide_floors']): ?><li><a href="./floors.php" class="goto-floors hidemobile">Floors</a></li><?php endif; ?>
	    <?php if(!$settings['widget_hide_models']): ?><li><a href="./models.php" class="goto-models">Models</a></li><?php endif; ?>
    </ul>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.dropdown.min.js"></script>
<script type="text/javascript" src="../xdm/easyXDM.js"></script>
<script type="text/javascript" src="js/xdm.js?ver=1.0.1"></script>

<script src="../js/ft/footable.js"></script>
<script src="../js/ft/footable.sort.js"></script>
<script type="text/javascript">var qsvars = <?= json_encode($vars); ?>;</script>
<script src="../js/jquery.quicksearch.js"></script>

<script type="text/javascript">
    jQuery(function($)
    {
        if(rpc) rpc.setTitle('Search');
        $('#qsbar').quicksearch({
            topOffset: 55
        });
    });
</script>
</body>
</html>