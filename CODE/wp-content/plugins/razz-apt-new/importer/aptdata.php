<?php
	require_once plugin_dir_path(__FILE__) . 'aptdata.php';
	require_once plugin_dir_path(__FILE__) . 'importer.php';
	require_once plugin_dir_path(__FILE__) . 'im_rentcafe.php';
	require_once plugin_dir_path(__FILE__) . 'im_propertysolutions.php';
	require_once plugin_dir_path(__FILE__) . 'im_onsite.php';
	require_once plugin_dir_path(__FILE__) . 'im_json.php';
	
	add_shortcode('apt_data', function($atts)
	{
		$params = shortcode_atts(array(
			'path' => false,
			'tag' => false,
			'json' => false
		), $atts);
		$adata = AptData::Instance();
		$result = $adata->_($params['path']);
		if($params['json']) $result = json_encode($result);
		return $result;
	});
	
	add_shortcode('apt_settings', function($atts)
	{
		$params = shortcode_atts(array(
			'path' => false,
			'json' => false
		), $atts);
		if($params['path'] === false) return "[ Settings shortcode missing path ]";
		$adata = AptData::Instance();
		$result = $adata->_($params['path'], get_option('razz_apt_opt'));
		if($params['json']) $result = json_encode($result);
		return $result;
	});
	
	add_action('admin_print_footer_scripts', function()
	{
    if (wp_script_is('quicktags')){
		?>
			<script type="text/javascript">
				QTags.addButton('rzsc_apt_data', 'Apt Data', '[apt_data path=""]', '', '', 'Insert apartment data shortcode', 980);
			</script>
		<?php
    }
	});

	
	$adata = AptData::Instance();
	
	class AptData
	{
		private $data, $datanew;
		
		public static function Instance()
		{
			static $inst = false;
			if($inst === false)
				$inst = new self();
			return $inst;
		}
		
		public function __construct()
		{
			$this->data = AptData::getBase();
			$this->datanew = AptData::getBase();
			$this->retrieve();
		}
		
		public function _($path, $start=false)
		{
			if(!is_string($path)) return false;
			if($start === false) $start = $this->getData();
			if(is_string($start)) return $this->_($start);
			$result = $start;
			$parts = explode('.', $path);
			$full = "";
			while($part = array_shift($parts))
			{
				
				if(substr($part, 0, 1) == '^')
				{
					$part = substr($part, 1);
					if(!isset($_GET[$part])) return false;
					$part = $_GET[$part];
				}
				if($part === '')
				{
					$result = $result;
				}
				if(is_array($result) && array_key_exists($part, $result))
				{
					$result = $result[$part];
				}
				elseif(is_numeric($result))
				{
					if($part == 'min' || $part == 'max') return $result; //return single numbers as min/max if requested
					//Could add more numeric functions if desired
				}
				else
				{
					return false;
				}
			}
			return $result;
		}
		
		public function _search($haystack, $key, $needle) 
		{
			return $this->__search($haystack, array($key=>$needle));
		}
		public function __search($haystack, $rules=array()) 
		{
			if(is_string($haystack)) $haystack = $this->_($haystack);
			if(!is_array($haystack)) return false;
			$results = array();
			foreach($haystack as $value)
			{
				foreach($rules as $key=>$needle)
					if($this->_($key, $value) != $needle) continue 2;
				$results[] = $value;
			}
			return $results;
		}
		
		public function getIdent()
		{
			$data = $this->getData();
			return $data['ident'];
		}
		public function getFloors()
		{
			$data = $this->getData();
			return $data['floors'];
		}
		
		public function getUnits()
		{
			$ret = array();
			$units = $this->_('units');
			foreach($units as $key=>$unit)
			{
				if($unit['disabled'] || $unit['type'] != 'unit') continue;
				$ret[$key] = $unit;
			}
			return $ret;
		}
		
		public function getUnit($id){return $this->getUnitByID($id);}
		public function getUnitByID($id)
		{
			$units = $this->getUnits();
			if(!isset($units[$id])) return false;
			return $units[$id];
		}
		
		public function getUnitByPID($pid)
		{
			$units = $this->getUnits();
			foreach($units as $key=>$value)
				if($value['pid'] == $pid) return $value;
			return false;
		}
		
		public function getUnitsByModel($model)
		{
			$result = array();
			$units = $this->getUnits();
			foreach($units as $key=>$value)
			{
				if($value['model'] == $model)
					$result[$key] = $value;
			}
			return $result;
		}
		
		public function getUnitsByFloor($floor)
		{
			$result = array();
			$units = $this->getUnits();
			foreach($units as $key=>$value)
			{
				if($value['floor'] == $floor)
					$result[$key] = $value;
			}
			return $result;
		}
		
		public function getModels()
		{
			$data = $this->getData();
			return $data['models'];
		}
		
		public function getModel($id){return $this->getModelByID($id);}
		public function getModelByID($id)
		{
			$models = $this->getModels();
			if(!isset($models[$id])) return false;
			return $models[$id];
		}
		
		public function getModelByPID($pid)
		{
			$models = $this->getModels();
			foreach($models as $key=>$value)
			{
				if($value['pid'] == $pid)
					return $value;
			}
			return false;
		}
		
		public function importData($data)
		{
			$this->datanew = array_replace_recursive($this->datanew, $data);
		}
		
		public function clearChanges()
		{
			$this->datanew = AptData::getBase();
		}
		
		public function clearRooms()
		{
			$this->data['rooms'] = array();
		}
/*
		public function disableAll()
		{
			foreach($this->data['models'] as $key=>$model)
			{
				$this->data['models'][$key]['disabled'] = true;
			}
			foreach($this->data['units'] as $key=>$unit)
			{
				$this->data['units'][$key]['disabled'] = true;
			}
		}*/
		
		public function disableUnused()
		{
			foreach($this->data['models'] as $key=>$model)
			{
				if(!array_key_exists($key, $this->datanew['models']))
					$this->data['models'][$key]['disabled'] = true;
				if(sizeof($this->_search('units', 'model', $key)) < 1)
					$this->datanew['models'][$key]['disabled'] = true;
			}
			foreach($this->data['units'] as $key=>$unit)
			{
				if(!array_key_exists($key, $this->datanew['units']))
					$this->data['units'][$key]['disabled'] = true;
			}
		}
		
		public function retrieve()
		{
			try
			{
				$meta = get_post_meta(1, '_apttool_aptdata', true);
				if(!$meta) throw new Exception();
				$this->data = json_decode($meta, true);
				if(!$this->data) throw new Exception();
			}
			catch(Exception $ex)
			{
				$this->data = AptData::getBase();
				return false;
			}
			return true;
		}
		
		public function getRawData()
		{
			return $this->data;
		}

		public function getDataNew()
		{
			return $this->datanew;
		}
		
		public function getData()
		{
			return array_replace_recursive($this->getRawData(), $this->datanew);
		}
		
		public function wipeData($wipeposts=false)
		{
			if($wipeposts)
				foreach(array_merge($this->data['units'], $this->data['models'], $this->data['floors']) as $value) 
					if(isset($value['pid']))
						wp_delete_post($value['pid']);
			
			$this->data = AptData::getBase();
			$this->datanew = AptData::getBase();
			$this->store();
		}
		
		public function store()
		{
			$this->data = array_replace_recursive($this->data, $this->datanew);
			$this->clearChanges();
			
			ksort($this->data['floors']);
			foreach($this->data['floors'] as $key => &$floor)
			{
				if(!isset($floor['type']))
				{
					unset($this->data['floors'][$key]);
					continue;
				}
				if(!$floor['pid'])
				{
					$post = array(
						'post_type'			=>	'apt-floor',
						'post_status'		=>	'publish',
						'post_title'		=>	$key
					);
					
					$floor['pid'] = wp_insert_post($post);
				}
				else
				{
					wp_update_post(array(
						'ID'				=>	$floor['pid'],
						'post_title'		=>	$key
					));
				}
			}
			
			ksort($this->data['models']);
			foreach($this->data['models'] as $key => &$model)
			{
				if(!isset($model['type']))
				{
					unset($this->data['models'][$key]);
					continue;
				}
				if(!array_key_exists('terms', $model)) $model['terms'] = array();
				foreach($model['terms'] as $key=>$term)
					if(preg_match('/^([0-9.]* B(ed|ath)room|Studio)$/', $term))
						unset($model['terms'][$key]);
				
				if($model['rooms']['bedroom'] == 0)
					$model['terms'][] = 'Studio';
				$model['terms'][] = $model['rooms']['bedroom'] . ' Bedroom';
				$model['terms'][] = $model['rooms']['bathroom'] . ' Bathroom';
				$model['terms'] = array_values($model['terms']);
				
				if(!$model['pid'])
				{
					$model['pid'] = wp_insert_post(array(
						'post_type'			=>	'apt-model',
						'post_status'		=>	'publish',
						'post_title'		=>	$model['name']
					));
				}
				else
				{
					wp_update_post(array(
						'ID'				=>	$model['pid'],
						'post_title'		=>	$model['name']
					));
					wp_set_object_terms($model['pid'], $model['terms'], 'model-tags', true);
				}
			}
			
			ksort($this->data['units']);
			foreach($this->data['units'] as $key => &$unit)
			{
				if(!isset($unit['type']))
				{
					unset($this->data['units'][$key]);
					continue;
				}

				$adate = strtotime($unit['date']);
				if($adate !== false && $adate < time())
				{
					$unit['date'] = "Available Now";
				}

				if(!$unit['pid'])
				{
					$unit['pid'] = wp_insert_post(array(
						'post_type'			=>	'apt',
						'post_status'		=>	'publish',
						'post_title'		=>	$key
					));
				}
				else
				{
					wp_update_post(array(
						'ID'				=>	$unit['pid'],
						'post_title'		=>	$key
					));
				}
				if(!array_key_exists('terms', $unit)) $unit['terms'] = array();
				foreach($unit['terms'] as $key=>$term)
					if(preg_match('/^([0-9.]* B(ed|ath)room|Studio)$/', $term))
						unset($unit['terms'][$key]);
				
				if($unit['rooms']['bedroom'] == 0)
					$unit['terms'][] = 'Studio';
				$unit['terms'][] = $unit['rooms']['bedroom'] . ' Bedroom';
				$unit['terms'][] = $unit['rooms']['bathroom'] . ' Bathroom';
				$unit['terms'] = array_values($unit['terms']);
			}
			
			foreach($this->data['rooms'] as &$room)
				sort($room, SORT_NUMERIC);
			
			
			update_post_meta(1, '_apttool_aptdata', json_encode($this->data));
		}
		
		public static function getBase()
		{
			return array(
				'ident' => array(),
				'amenities' => array('Community'=>array(), 'Floorplan'=>array()),
				'floors' => array(),
				'models' => array(),
				'units' => array(),
				'files' => array(),
				'rooms' => array(),
				'tags' => array(
					array('name' => 'ident', 'children' => array()),
					array('name' => 'units', 'children' => array()),
					array('name' => 'models', 'children' => array()),
					array('name' => 'amenities', 'children' => array())
				)
			);
		}
	}
	
	function objectToArray($object) {
		if(!is_object($object) && !is_array($object))
			return $object;

		return array_map('objectToArray', (array)$object);
	}
	
	function getFormattedSqft($input, $short=false)
	{
		$settings = getAptSettings();
		if(!isset($input['type']) || ($input['type'] != 'model' && $input['type'] != 'unit')) return false;
		return formatRange($input['sqft']['min'], $input['sqft']['max'], "", $short);
	}
	
	function getFormattedRent($input, $short=false)
	{
		$settings = getAptSettings();
		if(!isset($input['type'])) return false;
		if(!empty($settings['data_price_override'])) return $settings['data_price_override'];
		switch($input['type'])
		{
			case 'model':
			{
				return formatRange($input['rent']['min'], $input['rent']['max'], "$", $short, true, $settings['data_price_decimals']);
			}
			case 'unit':
			{
				return formatNumber($input['rent']['market'], "$", true, $settings['data_price_decimals']);
			}
		}
		return false;
	}
	
	function getFormattedDeposit($input, $short=false)
	{
		$settings = getAptSettings();
		$adata = AptData::Instance();
		if(!isset($input['type'])) return false;
		switch($input['type'])
		{
			case 'unit':
			{
				$input = $adata->getModel($input['model']);
				//Falls through to model with input changed to the model of the unit
			}
			case 'model':
			{
				return formatRange($input['deposit']['min'], $input['deposit']['max'], "$", $short, true, $settings['data_price_decimals']);
			}
		}
		return false;
	}
?>