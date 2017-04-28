<?php
class InteractiveBuilding
{

	public function __construct()
	{
		add_shortcode('rapt_ibldg', array($this, 'show'));
				
		//add_action('wp_enqueue_scripts', array($this, 'enqueue_stuff'));
	}
	
	public function show($atts, $content)
	{
		$this->enqueue_stuff();
		$atts = shortcode_atts(array(
			
		), $atts);
		
		ob_start();
?>
<div id="maincanvas" style="overflow: hidden; top: 0; left: 0; width: 0; height: 0;"></div>
<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
	
	public function enqueue_stuff()
	{
		global $options_panel;
		$settings = get_option('razz_apt_opt');
		
		$vars = array();
		
		$vars['bldg_img'] = $settings['qs_ibldg_img']['src'];
		$vars['floorhovercolor'] = $settings['qs_ibldg_floorhovercolor'];
		$vars['width'] = $settings['qs_ibldg_width'];
		$vars['height'] = $settings['qs_ibldg_height'];
		$vars['retina'] = $settings['ibldg_img_retina'];
		
		$vars['fadeout'] = array();
		$vars['fadeout']['dist'] = $settings['ibldg_fadeout_distance'];
		$vars['fadeout']['from'] = $settings['ibldg_fadeout_from'];
		$vars['fadeout']['to'] = $settings['ibldg_fadeout_to'];
		
		$vars['tooltip'] = array();
		$vars['tooltip']['enabled'] = $settings['ibldg_tooltip_enabled'];
		$vars['tooltip']['dir'] = $settings['ibldg_tooltip_direction'];
		$vars['tooltip']['bg'] = $settings['ibldg_tooltip_bgcolor'];
		$vars['tooltip']['fg'] = $settings['ibldg_tooltip_fgcolor'];
		$vars['tooltip']['bordercolor'] = $settings['ibldg_tooltip_bordercolor'];
		$vars['tooltip']['borderwidth'] = $settings['ibldg_tooltip_borderwidth'];
		
		
		$vars['infobar'] = array();
		$vars['infobar']['enabled'] = $settings['ibldg_infobar_enabled'];
		$vars['infobar']['showfloors'] = $settings['ibldg_infobar_floors_enabled'];
		$vars['infobar']['offset'] = $settings['qs_ibldg_infobar_offset'];
		
		$vars['infobar']['main'] = array(
			'bg' => $settings['qs_ibldg_infobar_bgcolor'],
			'fg' => $settings['qs_ibldg_infobar_fgcolor']
		);
		$vars['infobar']['block1'] = array(
			'bg' => $settings['qs_ibldg_infobar_block1_bgcolor'],
			'fg' => $settings['qs_ibldg_infobar_block1_fgcolor'],
			'text' => $settings['ibldg_infobar_block1_text']
		);
		$vars['infobar']['floorbtn'] = array(
			'bg' => $settings['qs_ibldg_infobar_floorbtn_bgcolor'],
			'fg' => $settings['qs_ibldg_infobar_floorbtn_fgcolor'],
			'hover' => array(
				'bg' => $settings['qs_ibldg_infobar_floorbtn_bgcolor_hover'],
				'fg' => $settings['qs_ibldg_infobar_floorbtn_fgcolor_hover']
			)
		);
		
		
		$vars['dims'] = array();
		$vars['dims']['floor'] = array();
		
		$adata = AptData::Instance();
		$floors = $adata->_('floors');
		foreach($floors as $floor)
		{
			$poly = $floor['verticies'];
			if(is_string($poly)) $poly = json_decode($poly, true);
			if(!is_array($poly) || count($poly) < 3) continue;
			
			$vars['dims']['floor'][] = array(
				'id'	=> $floor['pid'],
				'floor'	=> $floor['id'],
				'name'		=> isset($floor['name']) ? $floor['name'] : 'FLOOR ' . $floor['id'],
				'origpoly'	=> $poly
			);
		}
		$vars['dims']['floor'] = array_reverse($vars['dims']['floor']);
		
		wp_enqueue_script('color', plugins_url('js/jquery.color.js', __FILE__), array('jquery'));
		wp_enqueue_script('kinetic', plugins_url('js/kinetic-v4.7.4.min.js', __FILE__), array('jquery'));
		//wp_enqueue_script('kinetic', plugins_url('js/kinetic-v5.1.0.min.js', __FILE__), array('jquery'));
		wp_enqueue_script('ibldg', plugins_url('js/jquery.interactivebldg.js', __FILE__), array('jquery', 'kinetic', 'color'));
		wp_localize_script('ibldg', 'ibvars', $vars);
	}

}
?>