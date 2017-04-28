<?php
class FloorView
{

	public function __construct()
	{
		add_shortcode('rapt_floor_title', array($this, 'sc_get_title')); //Legacy
		add_shortcode('rapt_floor_content', array($this, 'sc_get_content')); //Legacy
				
		//add_action('wp_enqueue_scripts', array($this, 'enqueue_stuff'));
	}
	
	function get_floor()
	{
		global $adata;
		$floor = false;
		if(isset($_GET['floor']))
			$floor = $adata->_('floors.^floor');
		
		return $floor;
	}
	
	function sc_get_title()
	{
		global $adata;
		return $adata->_('floors.^floor.name');
	}
	
	function sc_get_content()
	{
		$this->enqueue_stuff();
		$floor = $this->get_floor();
		if($floor === false) return 'Invalid floor.';
		
		ob_start();
	?>
	<div id="maincanvas" style="overflow: hidden; top: 0; width: 100%; height: 750px;"></div>
	<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	public function enqueue_stuff()
	{
		global $adata;
		$floor = $this->get_floor();
		if($floor === false) return 'Invalid floor.';
		global $options_panel;
		$settings = get_option('razz_apt_opt');
		
		$vars = array();
		
		$vars['nospace'] = $settings['fv_nospace'];
		$vars['valign'] = $settings['fv_valign'];
		$vars['floor'] = $floor['id'];
		
		$vars['url'] = array(
			'home' => home_url()
		);
		
		$vars['pos'] = array(
			'top' => $settings['fv_pos_top']
		);
		
		$vars['blocks'] = array(
			array(
				'show' => $settings['fv_block1_show'],
				'bgcolor' => $settings['fv_block1_bgcolor'],
				'fgcolor' => $settings['fv_block1_fgcolor']
			),
			array(
				'bgcolor' => $settings['fv_block2_bgcolor'],
				'fgcolor' => $settings['fv_block2_fgcolor']
			),
			array(
				'bgcolor' => $settings['fv_block3_bgcolor'],
				'fgcolor' => $settings['fv_block3_fgcolor']
			),
			array(
				'bgcolor' => $settings['fv_block4_bgcolor'],
				'fgcolor' => $settings['fv_block4_fgcolor']
			),
			array(
				'bgcolor' => $settings['fv_block5_bgcolor'],
				'fgcolor' => array(
					'title' => $settings['fv_block5_titlefgcolor'],
					'data' => $settings['fv_block5_datafgcolor']
				)
			)
		);
		
		$vars['dots'] = array(
			'radius'	=> $settings['fv_dot_radius'],
			'stroke'	=> $settings['fv_dot_stroke'],
			'available' => array(
				'inner'		=> $settings['fv_dot_available_inner'],
				'outer'		=> $settings['fv_dot_available_outer']
			),
			'selected' => array(
				'inner'		=> $settings['fv_dot_selected_inner'],
				'outer'		=> $settings['fv_dot_selected_outer']
			)
		);
		
		$vars['apts'] = array();
		if($settings['fv_apts_startoff']) //If you want it to start blank, add a first unit that is blank
			$vars['apts'][] = array('x'=> '-99999','y'=> '0','id'=> '','title'=> '','floor'=> '','price'=> '','deposit'=> '','leaseterm'=> '','pets'=> '','occupancy'=> '','mid'=> '','bedrooms'=> '','bathrooms'=> '','sqft'=> '');
		
		if(isset($floor['image']) && !empty($floor['image']))
			$vars['imgurl'] = $floor['image'];
		else
		{
			$imgurl = wp_get_attachment_image_src(get_post_thumbnail_id($floor['pid']), 'full');
			$vars['imgurl'] = $imgurl[0];
		}
		$floortitle = $floor['id'];
		
		//$units = $adata->_search('units', 'floor', $floor['id']);
		$units = $adata->_('units');
		foreach($units as $unit)
		{
			if($unit['floor'] != $floor['id'] || $unit['disabled']) continue;
			$model = $adata->_('models.' . $unit['model']);
			$aptdata = array(
				'id'		=> $unit['pid'],
				'x'			=> $unit['loc']['x'],
				'y'			=> $unit['loc']['y'],
				'unit'		=> $unit['id'],
				'mid'		=> $model['name'],
				'title'		=> $unit['id'],
				'floor'		=> $unit['floor'],
				'price'		=> getFormattedRent($unit),
				'deposit'	=> getFormattedDeposit($unit),
				'leaseterm'	=> $settings['uv_leasingterm'],
				'bedrooms'	=> $unit['rooms']['bedroom'],
				'bathrooms'	=> $unit['rooms']['bathroom']
			);
			
			if($aptdata['bedrooms'] == 0) $aptdata['bedrooms'] = 'Studio';
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
			$vars['apts'][] = $aptdata;
		}
		
		wp_enqueue_script('color', plugins_url('js/jquery.color.js', __FILE__), array('jquery'));
		wp_enqueue_script('kinetic', plugins_url('js/kinetic-v4.7.4.min.js', __FILE__), array('jquery'));
		wp_enqueue_script('floorview', plugins_url('js/jquery.floorview.js', __FILE__), array('jquery', 'kinetic', 'color'));
		wp_localize_script('floorview', 'fvars', $vars);
	}

}
?>