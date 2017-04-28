<?php
class LBManager
{
	//get_terms('model-tags', array())
	//var $models = false;
	
	public function __construct()
	{
		$this->handleHashbang();
		//add_shortcode('apt_models', array($this, 'sc_apt_models'));
		//add_action('wp_loaded', array($this, 'handleHashbang'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('wp_ajax_lbm', array($this, 'ajax_request'));
		add_action('wp_ajax_nopriv_lbm', array($this, 'ajax_request'));
	}
	
	public function handleHashbang()
	{
		global $options_panel;
		if(!isset($_GET['_escaped_fragment_']) || empty($_GET['_escaped_fragment_'])) return;
		$path = explode('/', $_GET['_escaped_fragment_']);
		if(empty($path[0])) array_shift($path);
		if($path[0] != 'floor-plans' && $path[0] != 'apt') return;
		//$redirect = plugin_dir_url(__FILE__) . 'floor-plan/';
		$redirect = '';
		if(!isset($path[1]))
		{
			$redirect .= 'index.php';
		}
		elseif(preg_match('/^unit?$/', $path[1]))
		{
			$redirect .= 'unit.php';
		}
		elseif(preg_match('/^model?$/', $path[1]))
		{
			$redirect .= 'model.php';
		}
		elseif(preg_match('/^models?$/', $path[1]))
		{
			$redirect .= 'models.php';
		}
		elseif(preg_match('/^floor?$/', $path[1]))
		{
			$redirect .= 'floor.php';
		}
		elseif(preg_match('/^floors?$/', $path[1]))
		{
			$redirect .= 'floors.php';
		}
		elseif($path[1] == 'search')
		{
			$redirect .= 'search.php';
		}
		else
		{
			return;
		}
		if(isset($path[2]))
			$_GET['id'] = $path[2];
//			$redirect .= '?id=' . $path[2];

//		header("Location: {$redirect}");
		set_include_path(plugin_dir_path(__FILE__) . 'floor-plan');
		require_once $redirect;
		die;
	}
	
	
	public function ajax_request()
	{

		//check_ajax_referer('apt_lbmjs', 'sec');
		global $adata;
		$settings = get_option('razz_apt_opt');
        if(!empty($settings['widget_allow_origin']))
            header('Access-Control-Allow-Origin: ' . $settings['widget_allow_origin']);
		switch($_GET['a'])
		{
			case 1:
			{
				$units = $adata->_('units');
				foreach($units as $key=>$value)
				{
					if($value['disabled']) unset($units[$key]);
				}
				$models = $adata->_('models');
				foreach($models as $key=>$value)
				{
					if($value['disabled']) unset($models[$key]);
				}
				echo json_encode(array(
					'res'		=> 'ok',
					'units'		=> $units,
					'models'	=> $models,
					'floors'	=> $adata->_('floors')
				));
				break;
			}
			default:
			{
				echo json_encode(array(
					'res'	=> 'err'
				));
				break;
			}
		}
		die();
	}
	
	
	public function enqueue_scripts()
	{
		wp_enqueue_script('apt-widget', '/?widget', array('jquery'));
		//wp_enqueue_script('jquery-address', '//cdnjs.cloudflare.com/ajax/libs/jquery.address/1.6/jquery.address.min.js', array('jquery'));
		//wp_enqueue_script('lbmanager', plugins_url('js/jquery.lbmanager.js', __FILE__), array('jquery', 'magnific-popup', 'jquery-address'));
		/*
		$vars = array(
			'ajaxurl'	=> admin_url('admin-ajax.php'),
			//'sec'		=> wp_create_nonce("apt_lbmjs"),
			'pluginurl'	=> plugin_dir_url(__FILE__)
		);
		wp_localize_script('lbmanager', 'aptdata', $vars);*/
	}
}
?>