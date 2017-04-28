<?php
/*
Plugin Name: Razz - Apartment Tool v2.5
Plugin URI: http://www.razzinteractive.com
Description: Apartment Tool
Version: 2.5
Author: Matthew Keys - Razz Interactive
Author URI: http://www.razzinteractive.com
License: 
*/
require_once(plugin_dir_path(__FILE__) . 'importer/aptdata.php');
require_once(plugin_dir_path(__FILE__) . 'settings.php');
//add_shortcode('rapt_building', 'rapt_sc_showbuilding');


$settings = get_option('razz_apt_opt');

add_action('admin_menu', 'rz_apt_admin_menu');

if(strpos($_SERVER["REQUEST_URI"], '/apartment/') !== false)
{
	include 'razz-aptview.php';
}
if($settings['uv_lightbox']['enabled'])
{
	require_once 'lbmanager.php';
	$lbm = new LBManager();
}

require_once 'aptwidget.php';
$aw = new AptWidget();

require_once 'quicksearch.php';
$qs = new QuickSearch();

require_once 'interactivebuilding.php';
$ib = new InteractiveBuilding();

require_once 'floorview.php';
$fv = new FloorView();

require_once 'modelsview.php';
$mv = new ModelsView();

require_once 'aptpdf.php';
$pdf = new AptPDF();
/*
require_once 'plotimporter.php';
$pi = new PlotImporter();
*/
/*
require_once 'importer.php';
$imp = new Importer();
*/
require_once 'razz-rentcafe-admin.php';
add_action('init', 'rapt_create_post_type');
add_action('wp_head', 'rapt_wp_head');
add_action('add_meta_boxes', 'rapt_add_meta_box');
add_action('save_post', 'rapt_save_meta_box');
add_action('wp_ajax_rapt_get_fp', 'rapt_get_fp');
add_action('wp_ajax_rz_apt_import', 'rz_apt_import_callback');
add_action('wp_ajax_rz_apt_wipe', 'rz_apt_data_wipe_callback');
add_action('wp_ajax_rz_apt_upgrade', 'rz_apt_upgrade_callback');

add_action('rz_apt_do_import', 'rz_apt_do_import');
add_action('admin_page_class_after_save', 'rz_apt_import_cron_setup');

add_action('wp_enqueue_scripts', 'rapt_enqueue_scripts');

//cols
add_filter('manage_edit-apt_columns', 'rapt_col_apt');
add_action('manage_apt_posts_custom_column', 'rapt_col_apt_data');
add_filter('manage_edit-apt_sortable_columns', 'rapt_col_apt_sort');

add_filter('manage_edit-apt-floor_columns', 'rapt_col_apt_floor');
add_action('manage_apt-floor_posts_custom_column', 'rapt_col_apt_floor_data');
add_filter('manage_edit-apt-floor_sortable_columns', 'rapt_col_apt_floor_sort');

add_filter('manage_edit-apt-model_columns', 'rapt_col_apt_model');
add_action('manage_apt-model_posts_custom_column', 'rapt_col_apt_model_data');
add_filter('manage_edit-apt-model_sortable_columns', 'rapt_col_apt_model_sort');

add_action('load-edit.php', 'rapt_edit_load');

function rapt_wp_head()
{
	$settings = get_option('razz_apt_opt');
?>
	<style>
		.mfp-bg{
			background: <?php echo $settings['lb_bgcolor']; ?>;
		}
		#fp-modal-wrap .fp-modal-close{
			color: <?php echo $settings['lb_fgcolor']; ?>;
		}
		.mfp-arrow-right:after, .mfp-arrow-right .mfp-a{
			border-right-color: <?php echo $settings['lb_arrow_color']; ?>;
		}
		.mfp-arrow-right:before, .mfp-arrow-right .mfp-b{
			border-left-color: <?php echo $settings['lb_arrow_bordercolor']; ?>;
		}
		.mfp-arrow-left:after, .mfp-arrow-left .mfp-a{
			border-left-color: <?php echo $settings['lb_arrow_color']; ?>;
		}
		.mfp-arrow-left:before, .mfp-arrow-left .mfp-b{
			border-right-color: <?php echo $settings['lb_arrow_bordercolor']; ?>;
		}
		.bldg-iframe .mfp-content{
			height: 642px;
		}
        .mfp-iframe-holder {
            padding: 0;
        }
	</style>
<?php
}

function rz_apt_admin_menu()
{
	add_menu_page
	(
		"Apartment Tool",
		"Apartment Tool",
		"manage_options",
		"rz_apt_menu_import",
		null
	);
	/*
	add_submenu_page
	(
		"rz_apt_settings",
		"Apartment Tool Settings",
		"Settings",
		"manage_options",
		"rz_apt_settings",
		"rz_apt_settings_display"
	);*/
	
	add_submenu_page
	(
		"rz_apt_menu_import",
		"Apartment Tool Import",
		"Import",
		"manage_options",
		"rz_apt_menu_import",
		"rz_apt_menu_import_display"
	);
	
	$page_datatags = add_submenu_page
	(
		"rz_apt_menu_import",
		"Apartment Tool Data Tags",
		"Data Tags",
		"manage_options",
		"rz_apt_menu_datatags",
		"rz_apt_menu_datatags_display"
	);
	add_action('load-' . $page_datatags, 'rz_apt_menu_datatags_load');
	add_action('admin_footer-'. $page_datatags, 'rz_apt_menu_datatags_script');
	
	add_submenu_page
	(
		"rz_apt_menu_import",
		"Apartment Tool Data",
		"Imported Data",
		"manage_options",
		"rz_apt_menu_data",
		"rz_apt_menu_data_display"
	);
	
	//Only run this if old settings exist
	add_submenu_page
	(
		"rz_apt_menu_import",
		"Apartment Tool Upgrade",
		"Old Data",
		"manage_options",
		"rz_apt_menu_upgrade",
		"rz_apt_menu_upgrade_display"
	);
}

function rz_apt_menu_enqueue()
{
	//wp_enqueue_style('font-awesome');
	//wp_enqueue_style('ztree');
	//wp_enqueue_script('ztree');
	wp_enqueue_script('ztree', '//cdn.jsdelivr.net/jquery.ztree/3.5.16/js/jquery.ztree.all.min.js', array('jquery'), false, true);
	wp_enqueue_style('ztree', plugins_url('css/zTreeStyle.css', __FILE__));
	//wp_register_script('ztree', '//cdn.jsdelivr.net/jquery.ztree/3.5.16/js/jquery.ztree.all.min.js', array('jquery'), false, true);
	//wp_register_style('ztree', '//cdn.jsdelivr.net/jquery.ztree/3.5.16/css/zTreeStyle/zTreeStyle.css');
}

function rz_apt_import_cron_setup()
{
	$settings = get_option('razz_apt_opt');
	$next = wp_next_scheduled('rz_apt_do_import');
	if($next)
	{
		wp_unschedule_event($next, 'rz_apt_do_import');
	}
	
	$timestamp = $settings['import_schedule_timestamp'];
	$formint = $settings['import_schedule_interval'];
	
	if(!is_numeric($timestamp) || $timestamp <= 0) return;
	
	switch($formint)
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
	wp_schedule_event($timestamp, $interval, 'rz_apt_do_import');
}

function rz_apt_import_callback()
{
	check_ajax_referer('razz_imp0rt', 'sec');
	echo json_encode(rz_apt_do_import());
	die();
}

function rz_apt_do_import()
{
	$adata = AptData::Instance();
	$settings = get_option('razz_apt_opt');
	$result = array();
	$adata->clearRooms();
	//$adata->wipeData();
	if($settings['import_rc']['enabled'])
	{
		$output = array();
		$output['name'] = "RentCafe";
		$rc = new RentcafeImporter();
		if(($res = $rc->fetchData()) === true)
		{
			$rc->storeData();
			$output['res'] = 'ok';
			$output['msg'] = 'Import Successful';
		}
		elseif(is_array($res) && $res['res'] == 'err')
		{
			$output['res'] = 'err';
			$output['msg'] = $res['msg'];
		}
		$result[] = $output;
	}
	
	if($settings['import_ps']['enabled'])
	{
		$output = array();
		$output['name'] = "Property Solutions";
		$ps = new PropertySolutionsImporter();
		if(($res = $ps->fetchData()) === true)
		{
			$ps->storeData();
			$output['res'] = 'ok';
			$output['msg'] = 'Import Successful';
		}
		elseif(is_array($res) && $res['res'] == 'err')
		{
			$output['res'] = 'err';
			$output['msg'] = $res['msg'];
		}
		$result[] = $output;
	}
	
	if($settings['import_os']['enabled'])
	{
		$output = array();
		$output['name'] = "On-Site";
		$os = new OnSiteImporter();
		if(($res = $os->fetchData()) === true)
		{
			$os->storeData();
			$output['res'] = 'ok';
			$output['msg'] = 'Import Successful';
		}
		elseif(is_array($res) && $res['res'] == 'err')
		{
			$output['res'] = 'err';
			$output['msg'] = $res['msg'];
		}
		$result[] = $output;
	}

	$adata->disableUnused();
	
	if($settings['import_json']['enabled'])
	{
		$output = array();
		$output['name'] = "Manual JSON";
		$json = new JSONImporter();
		if(($res = $json->fetchData()) === true)
		{
			$json->storeData();
			$output['res'] = 'ok';
			$output['msg'] = 'Import Successful';
		}
		elseif(is_array($res) && $res['res'] == 'err')
		{
			$output['res'] = 'err';
			$output['msg'] = $res['msg'];
		}
		$result[] = $output;
	}

	$adata->store();
	file_put_contents(plugin_dir_path(__FILE__) . 'logs/apt_import_' . time() . '.txt', print_r($result, true));
	return $result;
}

function rz_apt_menu_import_display()
{
	rz_apt_menu_enqueue();
	?>
		<div class="wrap">
			<div id="icon-plugins" class="icon32"></div>
			<h2>Import</h2>
			<p>Next scheduled import will run in <strong><?php echo (($next = wp_next_scheduled('rz_apt_do_import')) !== false) ? human_time_diff($next) : "Never"; ?></strong>.<br />Click Import to begin the apartment tool import process now.</p>
			<div id="rz_status"></div>
			<a href="#" id="rz_import" class="button button-primary">Import</a>
		</div>
	<?php
	
	add_action( 'admin_footer', function(){
	?>
		<script type="text/javascript" >
			jQuery(document).ready(function($) {
				$('#rz_import').on('click', function(e)
				{
					e.preventDefault();
					var $btn = $(this);
					$btn.prop('disabled', true);
					$('#rz_status').html('<p>Importing...</p>');
					$.getJSON(ajaxurl, {'action': 'rz_apt_import', 'sec': '<?php echo wp_create_nonce("razz_imp0rt"); ?>'}, function(res) {
						console.log(res);
						if(!res.length)
						{
							$('#rz_status').html($('<div/>', {
								'class': 'error',
								'html': $('<p/>').html('<strong>Error:</strong> No active import providers. Set one up in settings.')
							}));
							return;
						}
						
						$('#rz_status').html('');
						
						for(var i = 0; i < res.length; i++)
						{
							var result = res[i];
							$('#rz_status').append($('<div/>', {
								'class': (result.res == 'ok') ? 'updated' : 'error',
								'html': $('<p/>').html('<strong>'+result.name+'</strong><br />'+result.msg)
							}));
						}
						$btn.prop('disabled', false);
					});
				});
			});
		</script>
	<?php
	});
}


function rz_apt_upgrade_callback()
{
	check_ajax_referer('razz_upgr4d3', 'sec');
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$res = array('res'=>'ok');
	$models = new WP_Query(array(
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
	));
	
	foreach($models->posts as $model)
	{
		$pid = $model->ID;
		$oldimgurl = get_post_meta($pid, '_rapt_model_imageurl', true);
		if(!is_string($oldimgurl) || strpos($oldimgurl, 'rentcafe.com') === false) continue;
		$dl = download_url($oldimgurl);
		preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $oldimgurl, $matches);
		$filearray = array(
			'name'		=> basename($matches[0]),
			'tmp_name'	=> $dl
		);
		$res[$oldimgurl] = $filearray;
		if(is_wp_error($dl))
		{
			@unlink($filearray['tmp_name']);
			continue;
		}
		
		$res[$oldimgurl] = true;
		$attach_id = media_handle_sideload($filearray, $pid);
		
		if(is_wp_error($attach_id))
		{
			@unlink($filearray['tmp_name']);
			continue;
		}
		$newimgurl = wp_get_attachment_url($attach_id);
		update_post_meta($pid, '_rapt_model_imageurl', sanitize_text_field($newimgurl));
	}
	echo json_encode($res);
	die();
}


function rz_apt_menu_upgrade_display()
{
	$adata = AptData::Instance();
	$settings = get_option('razz_apt_opt');
	
	try
	{
		$xmlident = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_ident', true));
		$xmlident = json_decode(json_encode((array)$xmlident));
		$newident = array(
			'MarketingName'	=> $xmlident->MarketingName,
			'Address' => array(
				'lat' => $xmlident->Latitude,
				'lon' => $xmlident->Longitude,
				'Address' => $xmlident->Address->Address1,
				'State' => $xmlident->Address->State,
				'City' => $xmlident->Address->City,
				'Zip' => $xmlident->Address->Zip
			),
			'Phone' => array(
				'primary' => $xmlident->Phone->Number,
				'fax' => $xmlident->Fax->Number
			),
			'Email'	=> $xmlident->Email
		);
	}
	catch(Exception $ex)
	{}
	try
	{
		$xmlamenities = new SimpleXMLElement(get_post_meta(1, '_rapt_rentcafe_data_amenities', true));
		$newamenities = json_decode(json_encode((array)$xmlamenities), true);
	}
	catch(Exception $ex)
	{}

	$floors = new WP_Query(array(
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
	));
	
	$newfloors = array();
	foreach($floors->posts as $floor)
	{
		$pid = $floor->ID;
		$id = get_post_meta($pid, '_rapt_floor_num', true);
		$newfloor = array(
			'id' => $id,
			'verticies' => get_post_meta($pid, '_rapt_floor_vertices', true),
			'image' => wp_get_attachment_url(get_post_thumbnail_id($pid))
		);
		$newfloors[$id] = $newfloor;
	}
	
	
	$models = new WP_Query(array(
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
	));
	$importimages = false;
	$newmodels = array();
	foreach($models->posts as $model)
	{
		$pid = $model->ID;
		$id = get_post_meta($pid, '_rapt_model_fpid', true);
		if(empty($id))
		{
			$id = rand(100000, 999999);
			update_post_meta($pid, '_rapt_model_fpid', $id);
		}
		$image = get_post_meta($pid, '_rapt_model_imageurl', true);
		if(is_string($image) && strpos($image, 'rentcafe.com') !== false) $importimages = true;
		
		$newmodel = array(
			'id' => $id,
			'name' => get_the_title($pid),
			'image' => $image,
			'pdf' => get_post_meta($pid, '_rapt_model_pdfurl', true)
		);
		$newmodels[$id] = $newmodel;
	}
	$units = new WP_Query(array(
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
	));
	
	$newunits = array();
	foreach($units->posts as $unit)
	{
		$pid = $unit->ID;
		$id = get_the_title($pid);
		$mid = get_post_meta($pid, '_rapt_apt_model_id', true);
		if($mid == 0) continue;
		$mid = get_post_meta($mid, '_rapt_model_fpid', true);
		$newunit = array(
			'id' => $id,
			'loc' => array(
				'x' => get_post_meta($pid, '_rapt_unit_locx', true),
				'y' => get_post_meta($pid, '_rapt_unit_locy', true)
			)
		);
		$newunits[$id] = $newunit;
	}
	$data = array(
		'ident' => $newident,
		'floors' => $newfloors,
		'models' => $newmodels,
		'units' => $newunits,
		'amenities' => $newamenities
	);
?>
<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2>Old Rentcafe Import Data</h2>
	<?php if($importimages): ?>
		<a href="#" id="rz_upgrade" class="button button-primary">Localize RentCafe Images</a>
	<?php endif; ?>
	<div id="rz_status"></div>
	<textarea style="width: 100%; min-height: 500px;"><?= prettyPrint(json_encode($data)); ?></textarea>
</div>
<?php

	add_action( 'admin_footer', function(){
	?>
		<script type="text/javascript" >
			jQuery(document).ready(function($) {
				$('#rz_upgrade').on('click', function(e)
				{
					e.preventDefault();
					var $btn = $(this);
					$btn.prop('disabled', true);
					$('#rz_status').html('<p>Downloading and Importing RentCafe Images...</p>');
					$.getJSON(ajaxurl, {'action': 'rz_apt_upgrade', 'sec': '<?php echo wp_create_nonce("razz_upgr4d3"); ?>'}, function(res) {
						document.location.reload();
					});
				});
			});
		</script>
	<?php
	});
}

function rz_apt_settings_display()
{
	rz_apt_menu_enqueue();

}


function rz_apt_menu_datatags_script()
{
	$adata = AptData::Instance();
	?>
	<script type="text/javascript">
		jQuery(function($)
		{
			var setting = {
				view: {
					addHoverDom: addHoverDom,
					removeHoverDom: removeHoverDom,
					selectedMulti: false
				},
				edit: {
					enable: ignoreRoot,
					editNameSelectAll: true,
					showRemoveBtn: true,
					showRenameBtn: true,
					drag: {
						isCopy: true,
						isMove: true,
						prev: true,
						inner: true,
						next: true
					}
				},
				data: {
				},
				callback: {
					beforeDrag: ignoreRoot,
					beforeRemove: beforeRemove,
					beforeRename: beforeRename,
					onRemove: onRemove,
					onRename: onRename
				}
			};
			var zNodes = <?php echo json_encode($adata->_('tags')); ?>;
			var log, className = "dark";
			function ignoreRoot(treeID, treeNodes)
			{
				for(var i = 0; i < treeNodes.length; i++)
				{
					var node = treeNodes[i];
					if(node.getParentNode() == null) return false;
				}
				return true;
			}
			function beforeDrag(treeId, treeNodes) {
				for(var i = 0; i < treeNodes.length; i++)
				{
					var node = treeNodes[i];
					if(node.getParentNode() == null) return false;
				}
				return true;
			}
			function beforeEditName(treeId, treeNode) {
				className = (className === "dark" ? "":"dark");
				showLog("[ "+getTime()+" beforeEditName ]&nbsp;&nbsp;&nbsp;&nbsp; " + treeNode.name);
				var zTree = $.fn.zTree.getZTreeObj("tagtree");
				zTree.selectNode(treeNode);
				return confirm("Start node '" + treeNode.name + "' editorial status?");
			}
			function beforeRemove(treeId, treeNode) {
				className = (className === "dark" ? "":"dark");
				showLog("[ "+getTime()+" beforeRemove ]&nbsp;&nbsp;&nbsp;&nbsp; " + treeNode.name);
				var zTree = $.fn.zTree.getZTreeObj("tagtree");
				zTree.selectNode(treeNode);
				return confirm("Confirm delete node '" + treeNode.name + "' it?");
			}
			function onRemove(e, treeId, treeNode) {
				showLog("[ "+getTime()+" onRemove ]&nbsp;&nbsp;&nbsp;&nbsp; " + treeNode.name);
			}
			function beforeRename(treeId, treeNode, newName, isCancel) {
				className = (className === "dark" ? "":"dark");
				showLog((isCancel ? "<span style='color:red'>":"") + "[ "+getTime()+" beforeRename ]&nbsp;&nbsp;&nbsp;&nbsp; " + treeNode.name + (isCancel ? "</span>":""));

				if (newName.length == 0) {
					alert("Name can not be empty.");
					var zTree = $.fn.zTree.getZTreeObj("tagtree");
					setTimeout(function(){zTree.editName(treeNode)}, 10);
					return false;
				}
				else if(newName.match(/[^a-z0-9_]/g) != null)
				{
					alert("Name must only contain lowercase letters, numbers, or underscore.");
					var zTree = $.fn.zTree.getZTreeObj("tagtree");
					setTimeout(function(){zTree.editName(treeNode)}, 10);
					return false;
				}
				return true;
			}
			function onRename(e, treeId, treeNode, isCancel) {
				showLog((isCancel ? "<span style='color:red'>":"") + "[ "+getTime()+" onRename ]&nbsp;&nbsp;&nbsp;&nbsp; " + treeNode.name + (isCancel ? "</span>":""));
			}
			function showLog(str) {
				if (!log) log = $("#log");
				log.append("<li class='"+className+"'>"+str+"</li>");
				if(log.children("li").length > 8) {
					log.get(0).removeChild(log.children("li")[0]);
				}
			}
			function getTime() {
				var now= new Date(),
				h=now.getHours(),
				m=now.getMinutes(),
				s=now.getSeconds(),
				ms=now.getMilliseconds();
				return (h+":"+m+":"+s+ " " +ms);
			}

			var newCount = 1;
			function addHoverDom(treeId, treeNode) {
				var sObj = $("#" + treeNode.tId + "_span");
				if (treeNode.editNameFlag || $("#addBtn_"+treeNode.tId).length>0) return;
				var addStr = "<span class='button add' id='addBtn_" + treeNode.tId
					+ "' title='add node' onfocus='this.blur();'></span>";
				sObj.after(addStr);
				var btn = $("#addBtn_"+treeNode.tId);
				if (btn) btn.bind("click", function(){
					var zTree = $.fn.zTree.getZTreeObj("tagtree");
					zTree.addNodes(treeNode, {id:(100 + newCount), pId:treeNode.id, name:"new node" + (newCount++)});
					return false;
				});
			};
			function removeHoverDom(treeId, treeNode) {
				$("#addBtn_"+treeNode.tId).unbind().remove();
			};
			function selectAll() {
				var zTree = $.fn.zTree.getZTreeObj("tagtree");
				zTree.setting.edit.editNameSelectAll =  $("#selectAll").attr("checked");
			}
			
			$(document).ready(function($){
				$.fn.zTree.init($("#tagtree"), setting, zNodes);
				$("#selectAll").bind("click", selectAll);
			});
		});
	</script>
	<?php
}

function rz_apt_menu_datatags_load()
{
	add_action('admin_enqueue_scripts', 'rz_apt_menu_enqueue');
	//add_action('wp_footer', 'rz_apt_menu_datatags_script');
}

function rz_apt_menu_datatags_display()
{
	$adata = AptData::Instance();
?>
<style>
	.ztree .button{
		-webkit-box-shadow: none;
		box-shadow: none;
	}
</style>
<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2>Data Tags</h2><br /><br />
	<ul id="tagtree" class="ztree"></ul>
</div>
<?php
}


function rz_apt_data_wipe_callback()
{
	check_ajax_referer('razz_w1p33', 'sec');
	$adata = AptData::Instance();
	$adata->wipeData($_REQUEST['wipeposts']);
	$adata->store();
	die();
}

function rz_apt_menu_data_display()
{
	rz_apt_menu_enqueue();
	$data = AptData::Instance();
?>
<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2>Imported Data</h2><form id="rz_wipe"><input type="submit" class="button button-small" value="Wipe Data" /> <input type="checkbox" name="wipeposts"> Wipe Posts<br /><br />
	<pre><?= prettyPrint(json_encode($data->getRawData())); ?></pre>
</div>
<?php


	add_action( 'admin_footer', function(){
	?>
		<script type="text/javascript" >
			jQuery(document).ready(function($) {
				$('#rz_wipe').on('submit', function(e)
				{
					e.preventDefault();
					if(!confirm('This will wipe the entire apartment database. Are you sure you wish to continue?')) return;
					var $form = $(this);
					var $btn = $form.find('input.button');
					$btn.prop('disabled', true);
					$.getJSON(ajaxurl, {'action': 'rz_apt_wipe', 'wipeposts': $form.find('input[name="wipeposts"]').prop('checked'),'sec': '<?php echo wp_create_nonce("razz_w1p33"); ?>'}, function(res) {
						document.location.reload();
					});
				});
			});
		</script>
	<?php
	});
}

function rapt_edit_load()
{
	add_filter('request', 'rapt_sort_apt_all');
}

function rapt_sort_apt_all($vars)
{
	if(!isset($vars['post_type']) || !isset($vars['orderby'])) return $vars;
	
	switch($vars['post_type'])
	{
		case 'apt':
		{
			switch($vars['orderby'])
			{
				case 'price':
				{
					$vars = array_merge
					(
						$vars,
						array
						(
							'meta_key'	=> '_rapt_apt_price',
							'orderby'	=> 'meta_value_num'
						)
					);
					break;
				}
				case 'mdate':
				{
					$vars = array_merge
					(
						$vars,
						array
						(
							'meta_key'	=> '_rapt_apt_availabledate',
							'orderby'	=> 'meta_value'
						)
					);
					break;
				}
				default:
				{
					break;
				}
			}
			break;
		}
		case 'apt-floor':
		{
			switch($vars['orderby'])
			{
				case 'fn':
				{
					$vars = array_merge
					(
						$vars,
						array
						(
							'meta_key'	=> '_rapt_floor_num',
							'orderby'	=> 'meta_value_num'
						)
					);
					break;
				}
				case 'do':
				{
					$vars = array_merge
					(
						$vars,
						array
						(
							'meta_key'	=> '_rapt_floor_order',
							'orderby'	=> 'meta_value_num'
						)
					);
					break;
				}
				default:
				{
					break;
				}
			}
			break;
		}
		case 'apt-model':
		{
			switch($vars['orderby'])
			{
				case 'bed':
				{
					$vars = array_merge
					(
						$vars,
						array
						(
							'meta_key'	=> '_rapt_model_bedroom',
							'orderby'	=> 'meta_value_num'
						)
					);
					break;
				}
				case 'bath':
				{
					$vars = array_merge
					(
						$vars,
						array
						(
							'meta_key'	=> '_rapt_model_bathroom',
							'orderby'	=> 'meta_value_num'
						)
					);
					break;
				}
				case 'sqft':
				{
					$vars = array_merge
					(
						$vars,
						array
						(
							'meta_key'	=> '_rapt_model_sqft_min',
							'orderby'	=> 'meta_value_num'
						)
					);
					break;
				}
				default:
				{
					break;
				}
			}
			break;
		}
		default:
		{
			break;
		}
	}
	
	return $vars;
}

function rapt_sc_quicksearch($atts)
{
	return rapt_sc_fw_quicksearch($atts);
}

function rapt_col_apt($cols)
{
	$cols = array
	(
		'cb'		=> '<input type="checkbox" />',
		'title'		=> 'Apartment Number',
		'model'		=> 'Model',
		'price'		=> 'Price',
		'mdate'		=> 'Move-In Date',
		'date'		=> 'Date'
	);
	return $cols;
}

function rapt_col_apt_sort($cols)
{
	$cols['price'] = 'price';
	$cols['mdate'] = 'mdate';
	return $cols;
}

function rapt_col_apt_data($col)
{
	global $post;
	$adata = AptData::Instance();
	$unit = $adata->getUnitByPID($post->ID);
	$model = $adata->getModel($unit['model']);
	switch($col)
	{
		case 'model':
		{
			echo $model['name'];
			break;
		}
		case 'floor':
		{
			echo $unit['floor'];
			break;
		}
		case 'price':
		{
			echo getFormattedRent($unit);
			break;
		}
		case 'mdate':
		{
			echo ($unit['date'] !== false) ? $unit['date'] : "Occupied";
			break;
		}
		default:
		{
			break;
		}
	}
}

function rapt_col_apt_floor_sort($cols)
{
	$cols['fn'] = 'fn';
	$cols['do'] = 'do';
	return $cols;
}

function rapt_col_apt_floor($cols)
{
	$cols = array
	(
		'cb'		=> '<input type="checkbox" />',
		'title'		=> 'Floor Name',
		'fn'		=> 'Floor Number',
		'do'		=> 'Display Order',
		'date'		=> 'Date'
	);
	return $cols;
}

function rapt_col_apt_floor_data($col)
{
	global $post;
	$pid = $post->ID;
	
	switch($col)
	{
		case 'fn':
		{
			$data = get_post_meta($pid, '_rapt_floor_num', true);
			if(empty($data))
				echo '0';
			else
				echo $data;
			break;
		}
		case 'do':
		{
			$data = get_post_meta($pid, '_rapt_floor_order', true);
			if(empty($data))
				echo '0';
			else
				echo $data;
			break;
		}
		default:
		{
			break;
		}
	}
}

function rapt_col_apt_model_sort($cols)
{
	$cols['bed'] = 'bed';
	$cols['bath'] = 'bath';
	$cols['sqft'] = 'sqft';
	return $cols;
}

function rapt_col_apt_model($cols)
{
	$cols = array
	(
		'cb'		=> '<input type="checkbox" />',
		'title'		=> 'Model Name',
		'bed'		=> 'Bedroom(s)',
		'bath'		=> 'Bathroom(s)',
		'sqft'		=> 'Sq. Ft.',
		'date'		=> 'Date'
	);
	return $cols;
}

function rapt_col_apt_model_data($col)
{
	global $post;
	$pid = $post->ID;
	
	switch($col)
	{
		case 'bed':
		{
			$data = get_post_meta($pid, '_rapt_model_bedroom', true);
			if(empty($data))
				echo '0';
			else
				echo $data;
			break;
		}
		case 'bath':
		{
			$data = get_post_meta($pid, '_rapt_model_bathroom', true);
			if(empty($data))
				echo '0';
			else
				echo $data;
			break;
		}
		case 'sqft':
		{
			$data = get_post_meta($pid, '_rapt_model_sqft_min', true);
			
			if(empty($data))
				echo '0';
			else
				echo $data;
			break;
		}
		default:
		{
			break;
		}
	}
}

function rapt_create_post_type()
{
	$labels = array(
		'name' => ('Building Floors'),
		'singular_name' => ('Building Floor'),
		'menu_name' => ('Building Floors'),
		'add_new_item' => ('Add New Floor'),
		'edit_item' => ('Edit Floor'),
		'new_item' => ('New Floor'),
		'view_item' => ('View Floor'),
		'items_archive' => ('Floor Archive'),
		'search_items' => ('Search Floors'),
		'not_found' => ('No floors found'),
		'not_found_in_trash' => ('No floors found in trash')
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'supports' => array('title', 'thumbnail')
	);

	register_post_type('apt-floor', $args);
	
	$labels = array(
		'name' => ('Apartment Models'),
		'singular_name' => ('Apartment Model'),
		'menu_name' => ('Apartment Models'),
		'add_new_item' => ('Add New Model'),
		'edit_item' => ('Edit Model'),
		'new_item' => ('New Model'),
		'view_item' => ('View Model'),
		'items_archive' => ('Model Archive'),
		'search_items' => ('Search Apartment Models'),
		'not_found' => ('No models found'),
		'not_found_in_trash' => ('No models found in trash')
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'supports' => array('title', 'categories')
	);

	register_post_type('apt-model', $args);
	
	register_taxonomy(
		'model-tags',
		'apt-model',
		array(
			'label' => __( 'Model Tags' ),
			'rewrite' => array( 'slug' => 'model-tag' ),
			'capabilities' => array(
				'assign_terms' => 'publish_posts',
				'edit_terms' => 'publish_posts'
			)
		)
	);

	$labels = array(
		'name' => ('Apartments'),
		'singular_name' => ('Apartment'),
		'menu_name' => ('Apartments'),
		'add_new_item' => ('Add New Apartment'),
		'edit_item' => ('Edit Apartment'),
		'new_item' => ('New Apartment'),
		'view_item' => ('View Apartment'),
		'items_archive' => ('Apartment Archive'),
		'search_items' => ('Search Apartments'),
		'not_found' => ('No apartments found'),
		'not_found_in_trash' => ('No apartments found in trash')
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'supports' => array('title')
	);

	register_post_type('apt', $args);
}

function rapt_add_meta_box()
{
	add_meta_box('apt-floor-options', 'Building Floor Info', rapt_display_floor_meta_box, 'apt-floor', 'normal', 'high');
	add_meta_box('apt-model-options', 'Apartment Model Info', rapt_display_model_meta_box, 'apt-model', 'normal', 'high');
	add_meta_box('apt-options', 'Apartment Info', rapt_display_apt_meta_box, 'apt', 'normal', 'high');
}

function rapt_save_meta_box($postid)
{
	if(!current_user_can('edit_page', $postid)) return;
	
	if($_POST['post_type'] == 'apt-floor')
		rapt_save_floor_meta_box($postid);
	elseif($_POST['post_type'] == 'apt-model')
		rapt_save_model_meta_box($postid);
	elseif($_POST['post_type'] == 'apt')
		rapt_save_apt_meta_box($postid);
}

function rapt_save_floor_meta_box($postid)
{
	if(!isset($_POST['rapt_floormeta']) || !wp_verify_nonce($_POST['rapt_floormeta'], plugin_basename( __FILE__ ))) return;
	
	update_post_meta($postid, '_rapt_floor_order', sanitize_text_field($_POST['rapt_floor_order']));
	update_post_meta($postid, '_rapt_floor_num', sanitize_text_field($_POST['rapt_floor_num']));
	update_post_meta($postid, '_rapt_floor_vertices', sanitize_text_field($_POST['rapt_floor_vertices']));
}

function rapt_display_floor_meta_box($post)
{
	wp_nonce_field(plugin_basename( __FILE__ ), 'rapt_floormeta');
?>
<table class="form-table">
	<tr>
		<th style="width:25%">
			<label for="rapt_floor_order">
				<strong>Display Order</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					Order to display this floor when listing floors.
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_floor_order" name="rapt_floor_order" value="<?php echo get_post_meta($post->ID, '_rapt_floor_order', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_floor_num">
				<strong>Floor Number</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					Number of this floor. Ex. The second floor would be <em>2</em>.
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_floor_num" name="rapt_floor_num" value="<?php echo get_post_meta($post->ID, '_rapt_floor_num', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_floor_vertices">
				<strong>Vertices</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					List of vertices for floor highlighting on main building image. (JSON array of x,y coordinate arrays defining the polygon)
				</span>
			</label>
		</th>
		<td>
			<input type="text" style="width: 75%" id="rapt_floor_vertices" name="rapt_floor_vertices" value="<?php echo get_post_meta($post->ID, '_rapt_floor_vertices', true); ?>">
		</td>
	</tr>
</table>
<?php
}


function rapt_save_model_meta_box($postid)
{
	if(!isset($_POST['rapt_modelmeta']) || !wp_verify_nonce($_POST['rapt_modelmeta'], plugin_basename( __FILE__ ))) return;
	
	update_post_meta($postid, '_rapt_model_bedroom', sanitize_text_field($_POST['rapt_model_bedroom']));
	update_post_meta($postid, '_rapt_model_bathroom', sanitize_text_field($_POST['rapt_model_bathroom']));
	update_post_meta($postid, '_rapt_model_sqft_min', sanitize_text_field($_POST['rapt_model_sqft_min']));
	update_post_meta($postid, '_rapt_model_sqft_max', sanitize_text_field($_POST['rapt_model_sqft_max']));
	update_post_meta($postid, '_rapt_model_price_min', sanitize_text_field($_POST['rapt_model_price_min']));
	update_post_meta($postid, '_rapt_model_price_max', sanitize_text_field($_POST['rapt_model_price_max']));
	update_post_meta($postid, '_rapt_model_imageurl', sanitize_text_field($_POST['rapt_model_imageurl']));
	update_post_meta($postid, '_rapt_model_pdfurl', sanitize_text_field($_POST['rapt_model_pdfurl']));
}

function rapt_display_model_meta_box($post)
{
	wp_nonce_field(plugin_basename( __FILE__ ), 'rapt_modelmeta');
?>
<table class="form-table">
	<tr>
		<th style="width:25%">
			<label for="rapt_model_bedroom">
				<strong>Bedrooms</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					Amount of bedrooms in model.
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_model_bedroom" name="rapt_model_bedroom" value="<?php echo get_post_meta($post->ID, '_rapt_model_bedroom', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_model_bathroom">
				<strong>Bathrooms</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					Amount of bathrooms in model.
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_model_bathroom" name="rapt_model_bathroom" value="<?php echo get_post_meta($post->ID, '_rapt_model_bathroom', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_model_sqft_min">
				<strong>Square Feet (Min)</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					Minimum square feet of model.
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_model_sqft_min" name="rapt_model_sqft_min" value="<?php echo get_post_meta($post->ID, '_rapt_model_sqft_min', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_model_sqft_max">
				<strong>Square Feet (Max)</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					Maximum square feet of model.
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_model_sqft_max" name="rapt_model_sqft_max" value="<?php echo get_post_meta($post->ID, '_rapt_model_sqft_max', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_model_price_min">
				<strong>Rent (Min)</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_model_price_min" name="rapt_model_price_min" value="<?php echo get_post_meta($post->ID, '_rapt_model_price_min', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_model_price_max">
				<strong>Rent (Max)</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_model_price_max" name="rapt_model_price_max" value="<?php echo get_post_meta($post->ID, '_rapt_model_price_max', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_model_imageurl">
				<strong>Image URL</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					URL to the image of the floorplan.
				</span>
			</label>
		</th>
		<td>
			<input type="text" style="width: 75%" id="rapt_model_imageurl" name="rapt_model_imageurl" value="<?php echo get_post_meta($post->ID, '_rapt_model_imageurl', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_model_pdfurl">
				<strong>PDF URL</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					URL to the PDF of the floorplan.
				</span>
			</label>
		</th>
		<td>
			<input type="text" style="width: 75%" id="rapt_model_pdfurl" name="rapt_model_pdfurl" value="<?php echo get_post_meta($post->ID, '_rapt_model_pdfurl', true); ?>">
		</td>
	</tr>
</table>
<?php
}


function rapt_save_apt_meta_box($postid)
{
	if(!isset($_POST['rapt_aptmeta']) || !wp_verify_nonce($_POST['rapt_aptmeta'], plugin_basename( __FILE__ ))) return;
	
	update_post_meta($postid, '_rapt_apt_model_id', sanitize_text_field($_POST['rapt_apt_model_id']));
	update_post_meta($postid, '_rapt_apt_floor', sanitize_text_field($_POST['rapt_apt_floor']));
	update_post_meta($postid, '_rapt_unit_locx', sanitize_text_field($_POST['rapt_unit_locx']));
	update_post_meta($postid, '_rapt_unit_locy', sanitize_text_field($_POST['rapt_unit_locy']));
	update_post_meta($postid, '_rapt_apt_price', sanitize_text_field($_POST['rapt_apt_price']));
	update_post_meta($postid, '_rapt_apt_deposit', sanitize_text_field($_POST['rapt_apt_deposit']));
	update_post_meta($postid, '_rapt_apt_leaseterm', sanitize_text_field($_POST['rapt_apt_leaseterm']));
	update_post_meta($postid, '_rapt_apt_pets', sanitize_text_field($_POST['rapt_apt_pets']));
	update_post_meta($postid, '_rapt_apt_occupancy', sanitize_text_field($_POST['rapt_apt_occupancy']));
	update_post_meta($postid, '_rapt_apt_aptid', sanitize_text_field($_POST['rapt_apt_aptid']));
	update_post_meta($postid, '_rapt_apt_availabledate', sanitize_text_field($_POST['rapt_apt_availabledate']));
	update_post_meta($postid, '_rapt_apt_amenities', sanitize_text_field($_POST['rapt_apt_amenities']));
	update_post_meta($postid, '_rapt_apt_available', sanitize_text_field($_POST['rapt_apt_available']));
}

function rapt_display_apt_meta_box($post)
{
	wp_nonce_field(plugin_basename( __FILE__ ), 'rapt_aptmeta');
	
?>
<table class="form-table">
	<tr>
		<th style="width:25%">
			<label for="rapt_apt_model_id">
				<strong>Model</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					Model of this apartment.
				</span>
			</label>
		</th>
		<td>
			<select id="rapt_apt_model_id" name="rapt_apt_model_id">
			<?php
				$args = array(
					'post_type' 	=> 'apt-model',
					'post_status'	=> 'publish',
					'orderby'		=> 'title',
					'order'			=> 'ASC',
					'posts_per_page'=> -1
				);
				$value = get_post_meta($post->ID, '_rapt_apt_model_id', true);
				$loop = new WP_Query($args);
				while($loop->have_posts()) : $loop->the_post();
					$lid = get_the_ID();
					if($value != "" && $value == $lid)
						the_title('<option value="'.$lid.'" selected>', '</option>');
					else
						the_title('<option value="'.$lid.'">', '</option>');
				endwhile;
			?>
			</select>
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_apt_floor">
				<strong>Floor</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					Floor this apartment resides.
				</span>
			</label>
		</th>
		<td>
			<select id="rapt_apt_floor" name="rapt_apt_floor">
			<?php
				$args = array(
					'post_type' 	=> 'apt-floor',
					'post_status'	=> 'publish',
					'orderby'		=> 'meta_value_num',
					'meta_key'		=> '_rapt_floor_order',
					'order'			=> 'ASC',
					'posts_per_page'=> -1
				);
				$value = get_post_meta($post->ID, '_rapt_apt_floor', true);
				$loop = new WP_Query($args);
				while($loop->have_posts()) : $loop->the_post();
					$lid = get_the_ID();
					if($value != "" && $value == $lid)
						the_title('<option value="'.$lid.'" selected>', '</option>');
					else
						the_title('<option value="'.$lid.'">', '</option>');
				endwhile;
			?>
			</select>
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_unit_locx">
				<strong>Unit Location</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					Coordinates of unit on floorplan. Click on floorplan to set.
				</span>
			</label>
		</th>
		<td>
			<?php
				$locx = get_post_meta($post->ID, '_rapt_unit_locx', true);
				$locy = get_post_meta($post->ID, '_rapt_unit_locy', true);
				if($locx == "")
					$locx = 0;
				if($locy == "")
					$locy = 0;
			?>
			X: <input type="text" id="rapt_unit_locx" name="rapt_unit_locx" value="<?php echo $locx; ?>"><br />
			Y: <input type="text" id="rapt_unit_locy" name="rapt_unit_locy" value="<?php echo $locy; ?>"><br />
			<a href="#" id="fptoggle">[ Show Floorplan ]</a><br />
			<div id="fpimg" style="display: none;"></div>
			<script src="<?php echo plugins_url('js/kinetic-v4.5.4.min.js', __FILE__); ?>"></script>
			<script type="text/javascript">
				jQuery(function($)
				{
					var fpimg = $('#fpimg');
					var fptoggle = $('#fptoggle');
					fptoggle.click(function(e)
					{
							e.preventDefault();
							
							fpimg.toggle();
							if(fpimg.is(":visible"))
							{
								fptoggle.text('[ Hide Floorplan ]');
							}
							else
							{
								fptoggle.text('[ Show Floorplan ]');
							}
					});
					
					function UpdateFloorplan()
					{
						fpimg.html('');
						$.ajax(
						{
							type: 'POST',
							data: {
								action: 'rapt_get_fp',
								id: $('#rapt_apt_floor').val()
							},
							url: '<?php echo admin_url('admin-ajax.php'); ?>',
							cache: false,
							timeout: 8000,
							error: function(res)
							{
								fpimg.html('Error loading floorplan');
							},
							success: function(res)
							{
								if(res != "")
								{
									var fpimgobj = new Image();
									fpimgobj.onload = function()
									{
										var fpimgw = fpimgobj.width;
										var fpimgh = fpimgobj.height;
										var scale = 500 / fpimgw;
										var fpimgsw = fpimgw * scale;
										var fpimgsh = fpimgh * scale;
									
										var stage = new Kinetic.Stage({
											container: 'fpimg',
											width: fpimgsw,
											height: fpimgsh
										});
										
										var fpimglayer = new Kinetic.Layer();
										stage.add(fpimglayer);
										
										var fpimgko = new Kinetic.Image(
										{
											x: 0,
											y: 0,
											image: fpimgobj,
											width: fpimgsw,
											height: fpimgsh
										});
										fpimglayer.add(fpimgko);
										
										
										var marker = new Kinetic.Circle(
										{
											x: ($('#rapt_unit_locx').val() * scale),
											y: ($('#rapt_unit_locy').val() * scale),
											radius: 6,
											fill: '#696B2C',
											stroke: '#BBC263',
											strokeWidth: 3
										});
										fpimglayer.add(marker);
										
										fpimglayer.draw();
										
										var mdown = false;
										stage.on('mousedown', function(e)
										{
											if(e.which == 1)
											{
												mdown = true;
												var mousepos = stage.getMousePosition();
												var sx = mousepos.x / scale;
												var sy = mousepos.y / scale;
												$('#rapt_unit_locx').val(sx.toFixed());
												$('#rapt_unit_locy').val(sy.toFixed());
												marker.setPosition(mousepos.x, mousepos.y);
												fpimglayer.draw();
											}
										});
										
										stage.on('mouseup', function(e)
										{
											mdown = false;
										});
										
										stage.on('mousemove', function()
										{
											if(mdown)
											{
												var mousepos = stage.getMousePosition();
												var sx = mousepos.x / scale;
												var sy = mousepos.y / scale;
												$('#rapt_unit_locx').val(sx.toFixed());
												$('#rapt_unit_locy').val(sy.toFixed());
												marker.setPosition(mousepos.x, mousepos.y);
												marker.setOpacity(1);
												fpimglayer.draw();
											}
										});
									}
									fpimgobj.src=res;
								}
								else
								{
									fpimg.html('No floorplan found for this floor.');
								}
							}
						});
					}
					
					$('#rapt_apt_floor').change(UpdateFloorplan);
					
					UpdateFloorplan();
				});
			</script>
		</td>
	</tr>
		<tr>
		<th style="width:25%">
			<label for="rapt_apt_price">
				<strong>Price</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_apt_price" name="rapt_apt_price" value="<?php echo get_post_meta($post->ID, '_rapt_apt_price', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_apt_deposit">
				<strong>Deposit</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_apt_deposit" name="rapt_apt_deposit" value="<?php echo get_post_meta($post->ID, '_rapt_apt_deposit', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_apt_leaseterm">
				<strong>Lease Term</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_apt_leaseterm" name="rapt_apt_leaseterm" value="<?php echo get_post_meta($post->ID, '_rapt_apt_leaseterm', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_apt_pets">
				<strong>Pets</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_apt_pets" name="rapt_apt_pets" value="<?php echo get_post_meta($post->ID, '_rapt_apt_pets', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_apt_occupancy">
				<strong>Max Occupancy</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_apt_occupancy" name="rapt_apt_occupancy" value="<?php echo get_post_meta($post->ID, '_rapt_apt_occupancy', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_apt_amenities">
				<strong>Amenities</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					Amenities spesific to this unit.
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_apt_amenities" name="rapt_apt_amenities" value="<?php echo get_post_meta($post->ID, '_rapt_apt_amenities', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_apt_aptid">
				<strong>Apartment ID</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					ID used for RentCafe integration.
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_apt_aptid" name="rapt_apt_aptid" value="<?php echo get_post_meta($post->ID, '_rapt_apt_aptid', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_apt_availabledate">
				<strong>Move-In Date</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					Format: MM/DD/YYYY
				</span>
			</label>
		</th>
		<td>
			<input type="text" id="rapt_apt_availabledate" name="rapt_apt_availabledate" value="<?php echo get_post_meta($post->ID, '_rapt_apt_availabledate', true); ?>">
		</td>
	</tr>
	<tr>
		<th style="width:25%">
			<label for="rapt_apt_available">
				<strong>Available</strong>
				<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
					Is this unit available?
				</span>
			</label>
		</th>
		<td>
			<input type="checkbox" id="rapt_apt_available" name="rapt_apt_available" value="yes"<?php if(get_post_meta($post->ID, '_rapt_apt_available', true) == 'yes') echo ' checked'; ?>>
		</td>
	</tr>
</table>
<?php
}

function rapt_get_fp()
{
	$pid = $_POST['id'];
	if(!is_numeric($pid)) die();
	
	$fpurl = wp_get_attachment_image_src(get_post_thumbnail_id($pid), 'full');
	echo $fpurl[0];
	
	die();
}


function rapt_sc_showbuilding($atts)
{
	$attribs = shortcode_atts(array(
		'b' => 1,
	), $atts);
	
	//echo $attribs['b'];

	ob_start();
?>
<div id="maincanvas" style="overflow: hidden; top: 0; left: 0; width: 0; height: 0;"></div>
<?php
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}


function rapt_footer()
{
?>
<script src="<?php echo plugins_url('js/kinetic-v4.5.4.min.js', __FILE__); ?>"></script>
	<script type="text/javascript">
		jQuery(function($)
		{
			(function(a){(jQuery.browser=jQuery.browser||{}).mobile=/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od|ad)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))})(navigator.userAgent||navigator.vendor||window.opera);
			if($.browser.mobile) return;
			$('#maincanvas').css(
			{
				'width': '100%',
				'height': '600px'
			});
			var dims = [];
			dims.floor = [];
<?php
	$args = array(
		'post_type' 	=> 'apt-floor',
		'post_status'	=> 'publish',
		'orderby'		=> 'meta_value_num',
		'meta_key'		=> '_rapt_floor_order',
		'order'			=> 'ASC',
		'posts_per_page'=> -1
	);
	$loop = new WP_Query($args);
	$i = 0;
	while($loop->have_posts()) : $loop->the_post();
		$pid = get_the_ID();
		$vertices = json_decode(get_post_meta($pid, '_rapt_floor_vertices', true));
		$vstring = "[";
		foreach($vertices as $pair)
		{
			$vstring .= "$pair[0], $pair[1], ";
		}
		$vstring = substr_replace($vstring, "", -2) . "]";
		echo "\t\t\tdims.floor[$i] = [];\n";
		echo "\t\t\tdims.floor[$i].id = '$pid';\n";
		//echo "\t\t\tdims.floor[$i].fnum = '".get_post_meta($pid, '_rapt_floor_num', true)."';\n";
		echo "\t\t\tdims.floor[$i].name = '".get_the_title()."';\n";
		echo "\t\t\tdims.floor[$i].origpoly = $vstring;\n";
		$i++;
	endwhile;

?>

			dims.BGLoaded = function()
			{
				return ('bg' in dims);
			};
			
			dims.ReCalculate = function()
			{
				dims.screen = [];
				dims.screen.width = $('#maincanvas').width();
				dims.screen.height = $('#maincanvas').height();
				
				if(dims.screen.height < 600)
				{
					dims.screen.height = 600;
				}
				
				if(dims.BGLoaded())
				{
					dims.bg.cur = [];
					dims.bg.cur.scale = Math.max(dims.screen.width / dims.bg.orig.width, dims.screen.height / dims.bg.orig.height);
					dims.bg.cur.width = dims.bg.orig.width * dims.bg.cur.scale;
					dims.bg.cur.height = dims.bg.orig.height * dims.bg.cur.scale;
					dims.bg.cur.x = dims.screen.width/2 - dims.bg.cur.width/2;
					dims.bg.cur.y = dims.screen.height/2 - dims.bg.cur.height/2;
					
					for(var i = 0; i < dims.floor.length; i++)
					{
						dims.floor[i].poly = [];
						for(var j = 0; j < dims.floor[i].origpoly.length; j++)
						{
							if(j % 2 == 0)
							{
								var offset = dims.bg.cur.x;
							}
							else
							{
								var offset = dims.bg.cur.y;
							}
							dims.floor[i].poly[j] = dims.floor[i].origpoly[j] * dims.bg.cur.scale + offset;
						}
					}
				}
				
				dims.info = [];
				dims.info.y = 0;
				if(dims.screen.width <= 200)
					dims.info.x = 0;
				else if(dims.screen.width <= 600)
					dims.info.x = dims.screen.width/2 - 100;
				else if(dims.screen.width <= 920)
					dims.info.x = dims.screen.width - 250;
				else if(dims.screen.width <= 1200)
					dims.info.x = dims.screen.width - 300;
				else
					dims.info.x = dims.screen.width - 400;
			};
			
			dims.ReCalculate();
			
			var stage = new Kinetic.Stage({
				container: 'maincanvas',
				width: dims.screen.width,
				height: dims.screen.height
			});
			
			var layers = [];
			layers.bg = new Kinetic.Layer();
			layers.poly = new Kinetic.Layer();
			layers.info = new Kinetic.Layer();
			stage.add(layers.bg);
			
			var infobar = new Kinetic.Group();
			
			
			var bgimgobj = new Image();
			bgimgobj.onload = function()
			{
				dims.bg = [];
				dims.bg.orig = [];
				dims.bg.orig.width = bgimgobj.width;
				dims.bg.orig.height = bgimgobj.height;
				dims.ReCalculate();
				
				var bgimg = new Kinetic.Image(
				{
					id: 'bgimg',
					x: dims.bg.cur.x,
					y: dims.bg.cur.y,
					image: bgimgobj,
					width: dims.bg.cur.width,
					height: dims.bg.cur.height
				});
				layers.bg.add(bgimg);
				layers.bg.draw();
				InitPolys();
				InitInfoBox();
			};
			bgimgobj.src = '<?php echo plugins_url('img/Alta_Rendering_tall2.png', __FILE__); ?>';

			function InitInfoBox()
			{
				layers.info.add(infobar);
				infobar.setPosition(dims.info.x, 0);
				
				var infobarheadbg = new Kinetic.Rect(
				{
					x: 0,
					y: 0,
					width: 200,
					height: 130,
					fill: '#868C36'
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
					fill: 'white'
				});
				infobar.add(infobarheadtxt);
				
				var infobarfsbg = new Kinetic.Rect(
				{
					x: 0,
					y: 130,
					width: 200,
					height: 320,
					fill: 'black',
					opacity: 0.8
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
					fill: 'white'
				});
				infobar.add(infobarfstxt);
				
				for(var i = 0; i < dims.floor.length; i++)
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
						fill: 'black',
						opacity: 0.5
					});
					button.add(buttonbg);
					
					var buttontxt = new Kinetic.Text(
					{
						x: 60,
						y: 208 + (i * 44),
						text: dims.floor[i].name,
						fontSize: 15,
						fontFamily: 'Calibri',
						fill: 'white'
					});
					button.add(buttontxt);

					buttonbg.tween = new Kinetic.Tween(
					{
						node: buttonbg,
						easing: Kinetic.Easings.EaseOut,
						opacity: 1.0,
						fillR: 44,
						fillG: 105,
						fillB: 124,
						duration: 0.7
					});
					
					button.on('mouseover', function(i)
					{
						return (function()
						{
							layers.info.get('#fbtn' + i)[0].tween.play();
							layers.poly.get('#fpoly' + i)[0].tween.play();
						});
					}(i));
					
					button.on('mouseout', function(i)
					{
						return (function()
						{
							layers.info.get('#fbtn' + i)[0].tween.reverse();
							layers.poly.get('#fpoly' + i)[0].tween.reverse();
						});
					}(i));
					
					button.on('mousedown', function(i)
					{
						return (function(e)
						{
							if(e.which == 1)
							{
								window.location.href = '../floor-view/?id=' + dims.floor[i].id;
							}
						});
					}(i));
				}

				stage.add(layers.info);
			}
			
			function InitPolys()
			{
				for(var i = 0; i < dims.floor.length; i++)
				{
					dims.floor[i].polyobj = new Kinetic.Polygon(
					{
						id: 'fpoly' + i,
						points: dims.floor[i].poly,
						fill: '#CDAF28',
						opacity: 0
					});
					layers.poly.add(dims.floor[i].polyobj);
					
					dims.floor[i].polyobj.tween = new Kinetic.Tween(
					{
						node: dims.floor[i].polyobj,
						easing: Kinetic.Easings.EaseOut,
						opacity: 0.3,
						duration: 0.7
					});
					dims.floor[i].polyobj.on('mouseover touchstart', function(i)
					{
						return (function()
						{
							this.tween.play();
							layers.info.get('#fbtn' + i)[0].tween.play();
						});
					}(i));
					dims.floor[i].polyobj.on('mouseout touchend', function(i)
					{
						return (function()
						{
							this.tween.reverse();
							layers.info.get('#fbtn' + i)[0].tween.reverse();
						});
					}(i));
					dims.floor[i].polyobj.on('mousedown', function(i)
					{
						return (function(e)
						{
							if(e.which == 1)
							{
								window.location.href = '../floor-view/?id=' + dims.floor[i].id;
							}
						});
					}(i));
					
				}
				
				stage.add(layers.poly);
			}
			
			<?php //rapt_qsfooter(); ?>
			
			function OnResize()
			{
				dims.ReCalculate();
				stage.setSize(dims.screen.width, dims.screen.height);
				infobar.setPosition(dims.info.x, 0);
				
				if(dims.BGLoaded())
				{
					var bgimg = layers.bg.get('#bgimg')[0];
					bgimg.setPosition(dims.bg.cur.x, dims.bg.cur.y);
					bgimg.setSize(dims.bg.cur.width, dims.bg.cur.height);
					
					for(var i = 0; i < dims.floor.length; i++)
					{
						dims.floor[i].polyobj.setPoints(dims.floor[i].poly);
					}
				}
				
				<?php //QSOnResize(); ?>
			}
			
			$(window).resize(OnResize);
			OnResize();
		});
	</script>
<?php
	rapt_qs_footer();
}


function rapt_enqueue_scripts()
{
	//wp_register_style('formalize', plugins_url('css/formalize.css', __FILE__));
	//wp_enqueue_script('unitview', plugins_url('js/jquery.unitview.js', __FILE__));
	//wp_enqueue_script('jquery-history', plugins_url('js/jquery.history.js', __FILE__), array('jquery'));
	wp_enqueue_script('magnific-popup', '//cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/0.9.9/jquery.magnific-popup.min.js', array('jquery'));
	wp_enqueue_style('magnific-popup', plugins_url('css/magnific-popup.css', __FILE__));
	//wp_enqueue_style('formalize');
	//rapt_qs_enqueue_scripts();
}


function formatNumber($number, $prefix="", $formatted=true, $decimals=0)
{
	if($formatted) $number = number_format($number, $decimals);
	return $prefix . $number;
}

function formatRange($min, $max, $prefix="", $short=false, $formatted=true, $decimals=0)
{
	$min = intval($min);
	$max = intval($max);
	$out = $min;
	if($min != $max)
	{
		if($short) return formatNumber(round(($min + $max) / 2), '~'.$prefix, $formatted, $decimals);
		else return formatNumber($min, $prefix, $formatted, $decimals) . ' - ' . formatNumber($max, $prefix, $formatted, $decimals);
	}
	else
	{
		return formatNumber($min, $prefix, $formatted, $decimals);
	}
}
function rangeDisplay($min, $max, $prefix="", $short=false, $formatted=true, $decimals=0) //Legacy
{
	return formatRange($min, $max, $prefix, $short, $formatted, $decimals);
}

function prettyPrint( $json )
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}

?>