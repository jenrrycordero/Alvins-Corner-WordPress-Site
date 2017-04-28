<?php
class ModelsView
{
	//get_terms('model-tags', array())
	var $models = false;
	
	public function __construct()
	{
		add_shortcode('apt_models', array($this, 'sc_apt_models'));
		add_action('wp_enqueue_scripts', array($this, 'register_scripts'));
	}
	
	function sc_apt_models($atts = array(), $content)
	{
		//$settings = get_option('razz_apt_opt');
		$params = shortcode_atts(array(
			'tagglue'		=> ' '
		), $atts);
		
		$output = "";
		$aptdata = AptData::Instance();
		$models = $aptdata->_('models');
		
		foreach($models as $model)
		{
			$moutput = $content;
			$moutput = str_replace('[name]', $model['name'], $moutput);
			$moutput = str_replace('[bed]', $model['rooms']['bedroom'], $moutput);
			$moutput = str_replace('[bath]', $model['rooms']['bathroom'], $moutput);
			$moutput = str_replace('[sqft]', rangeDisplay($model['sqft']['min'], $model['sqft']['max']), $moutput);
			$moutput = str_replace('[price]', rangeDisplay($model['rent']['min'], $model['rent']['max'], '$'), $moutput);
			$moutput = str_replace('[rent]', rangeDisplay($model['rent']['min'], $model['rent']['max'], '$'), $moutput);
			$moutput = str_replace('[image]', $model['image'], $moutput);
			$moutput = str_replace('[tags]', implode($params['tagglue'], array_map(function($c){ return $c->slug; }, wp_get_object_terms($model['pid'], 'model-tags'))), $moutput);
			$output .= $moutput;
		}
		return $output;
	}
	
	public function loadIsotope()
	{
		static $loaded = false;
		if($loaded) return;
		add_action('wp_footer', array($this, 'enqueue_scripts'));
		$loaded = true;
	}
	
	public function register_scripts()
	{
		//wp_register_script('isotope', THEME_DIR . '/assets/js/jquery.isotope.min.js', array('jquery'), false, true );
	}
}
?>