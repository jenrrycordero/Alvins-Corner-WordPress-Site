<?php
class QuickSearch
{
	
	public function __construct()
	{
		add_shortcode('rapt_quicksearch', array($this, 'show'));
		add_shortcode('apt_qs_enqueue', array($this, 'sc_qs_enqueue'));
		
		//add_action('wp_ajax_rapt_qs', array($this, 'ajax_request'));
		//add_action('wp_ajax_nopriv_rapt_qs', array($this, 'ajax_request'));
		add_action('wp_head', array($this, 'qs_style'));
		
		//add_action('wp_enqueue_scripts', array($this, 'enqueue_stuff'));
	}
	
	public function sc_qs_enqueue()
	{
		$this->enqueue_stuff();
	}
	
	public function enqueue_stuff()
	{
		$settings = get_option('razz_apt_opt');
		$adata = AptData::Instance();
		$beds = $adata->_('rooms.bedroom');
		$baths = $adata->_('rooms.bathroom');
		wp_enqueue_style('qsstyle', plugins_url('css/qs.css', __FILE__));
		wp_enqueue_style('ftcore', plugins_url('css/ft/footable.core.css', __FILE__));
		wp_enqueue_style('ftsa', plugins_url('css/ft/footable.standalone.css', __FILE__));
		
		wp_enqueue_script('footable', plugins_url('js/ft/footable.js', __FILE__), array('jquery'), false, true);
		//wp_enqueue_script('footable-paginate', plugins_url('js/ft/footable.paginate.js', __FILE__), array('jquery', 'footable'), false, true);
		wp_enqueue_script('footable-sort', plugins_url('js/ft/footable.sort.js', __FILE__), array('jquery', 'footable'), false, true);
		
		wp_enqueue_script('jsgraphics', plugins_url('js/wz_jsgraphics.js', __FILE__), array('jquery'));
		wp_enqueue_script('cvimaplib', plugins_url('js/mapper/cvi_map_lib.js', __FILE__), array('jsgraphics'));
		wp_enqueue_script('maputil', plugins_url('js/mapper/maputil.js', __FILE__), array('cvimaplib'));
		wp_enqueue_script('rwdimagemaps', plugins_url('js/jquery.rwdImageMaps.min.js', __FILE__), array('jquery'));
		
		wp_enqueue_script('quicksearch', plugins_url('js/jquery.quicksearch.js', __FILE__), array('jquery'));
		
		$vars = array(
			'ajaxurl'	=> admin_url('admin-ajax.php'),
			'pluginurl'	=> plugin_dir_url(__FILE__),
			'studio_size' => $settings['qs_filter_display_font_studio'],
			'filter'	=> array('bed'=>$beds, 'bath'=>$baths),
			'units'		=> array()
		);
		
		$units = $adata->_('units');
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
				'date'			=> (($timestamp = strtotime($unit['date'])) !== false) ? strtoupper(date("M j, Y", $timestamp)) : $unit['date'],
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
			ob_start();
?>
					<td><a href="<?php echo $settings['uv_lightbox']['enabled'] ? "#!/floor-plans/unit/{$unit['id']}" : home_url("/apartment/?unit={$unit['id']}"); ?>"><div class="qsviewdetails" style="background: <?php echo $settings['qs_viewdetails_bg']; ?>;"><div class="qsviewdetails_plus" style="background: <?php echo $settings['qs_viewdetails_bgplus']; ?>; color: <?php echo $settings['qs_viewdetails_fg']; ?>; float: left;"><span>+</span></div><span>VIEW DETAILS</span></div></a></td>
					<td><a href="<?php echo $settings['uv_lightbox']['enabled'] ? "#!/floor-plans/unit/{$unit['id']}" : home_url("/apartment/?unit={$unit['id']}"); ?>"><div class="qsviewdetails_plus" style="background: <?php echo $settings['qs_viewdetails_bgplus']; ?>; color: <?php echo $settings['qs_viewdetails_fg']; ?>;"><span>+</span></div></a></td>
<?php
			$aptdata['buttons'] = ob_get_clean();
			
			$vars['units'][] = $aptdata;
		}
		
		wp_localize_script('quicksearch', 'qsvars', $vars);
	}
	
	public function ajax_request()
	{
		$settings = get_option('razz_apt_opt');
		$adata = AptData::Instance();
		$adata_raw = $adata->getData();
		
		$units = $adata->getUnits();
		foreach($units as $unit)
		{
			$ubed = $unit['rooms']['bedroom'];
			$ubath = $unit['rooms']['bathroom'];
			$special = isset($unit['special']) ? ' ' . $unit['special'] : '';
			$model = $adata->getModel($unit['model']);
			if($settings['data_sqft_source'] == 'unit')
				$source = $unit;
			else
				$source = $model;
			
			$sqftmin = $source['sqft']['min'];
			$sqftmax = $source['sqft']['max'];
			$sqft = number_format(($sqftmin+$sqftmax)/2, 0);
			
			$price = getFormattedRent($unit);
			//$price = print_r($unit, true);
			$beddisp = $ubed;
			if($beddisp == 0) $beddisp = "Studio"; //TODO: Allow overriding any number with any text via settings
			
			$timestamp = strtotime($unit['date']);
			$adate = $unit['date'];
			if($timestamp !== false) $adate = strtoupper(date("M j, Y", $timestamp));
			
			
			?>
				<tr class="unitrow<?php echo $special; ?>">
					<td><?php echo $unit['id']; ?></td>
					<td class="bedroom" data-value="<?php echo $ubed; ?>"><?php echo $beddisp; ?></td>
					<td class="bathroom" data-value="<?php echo $ubath; ?>"><?php echo $ubath; ?></td>
					<?php if($sqftmin != $sqftmax): ?>
						<td data-value="<?php echo $sqft; ?>" title="<?php echo $sqftmin; ?> - <?php echo $sqftmax; ?>">~<?php echo $sqft; ?></td>
					<?php else: ?>
						<td><?php echo $sqft; ?></td>
					<?php endif; ?>
					<td data-value="<?php echo $unit['rent']['market']; ?>"><?php echo $price; ?></td>
					<td><?php echo $adate; ?></td>
					<td><a href="#" class="btn-viewdetails"><div class="qsviewdetails" style="background: <?php echo $settings['qs_viewdetails_bg']; ?>;"><div class="qsviewdetails_plus" style="background: <?php echo $settings['qs_viewdetails_bgplus']; ?>; color: <?php echo $settings['qs_viewdetails_fg']; ?>; float: left;"><span>+</span></div><span>VIEW DETAILS</span></div></a></td>
					<td><a href="#" class="btn-viewdetails"><div class="qsviewdetails_plus" style="background: <?php echo $settings['qs_viewdetails_bgplus']; ?>; color: <?php echo $settings['qs_viewdetails_fg']; ?>;"><span>+</span></div></a></td>
				</tr>
			<?php
		}
		die();
	}
	
	public function qs_style()
	{
		global $options_panel;
		$settings = get_option('razz_apt_opt');
?>
		<style>
			#qshead{
				background-color: <?php echo $settings['qs_headerbar_bgcolor']; ?>;
			}
			#qshead > p{
				<?php echo $options_panel->getFontCSS($settings['qs_headerbar_font']); ?>
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
		</style>
<?php
	}
	
	public function show($atts, $content)
	{
		global $adata;
		//$this->enqueue_stuff();
		$atts = shortcode_atts(array(
			
		), $atts);
		
		global $options_panel;
		$settings = get_option('razz_apt_opt');
		$filters = $adata->_('quicksearch.filters');
		
		ob_start();
?>
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
						<center><a href="#" class="qsfilter_btn_circle btnup" data-type="bed" data-inc="1"></a> <a href="#" class="qsfilter_btn_circle btndn" data-type="bed" data-inc="-1"></a></center>
					</div>
					<div id="qscntbath">
						BATHS
						<div id="qsfilter_disp_bath" class="qsfilter_disp_circle">ANY</div>
						<center><a href="#" class="qsfilter_btn_circle btnup" data-type="bath" data-inc="1"></a> <a href="#" class="qsfilter_btn_circle btndn" data-type="bath" data-inc="-1"></a></center>
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
						<tr><td colspan="7"><img src='[apt_settings path="img_loader.src"]' style="margin-right: 10px;" /> Loading Apartments...</td></tr>
					</tbody>
					<tfoot>
						<tr><td colspan="8">No apartments found that match your criteria.</td></tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
}
?>