<?php
	abstract class Importer
	{
		protected $data, $setname, $settings = null;
		
		public function __construct($setname)
		{
			$this->data = AptData::getBase();
			$this->setname = $setname;
		}
		
		public abstract function addSettings($panel);
		public abstract function fetchData();
		
		protected function cleanData(&$data)
		{
			if(is_array($data))
			{
				foreach($data as $key=>$value)
				{
					$data[$key] = $this->cleanData($value);
				}
			}
			elseif(is_object($data) && count((array)$data) == 0)
				return '';
			
			return $data;
		}
		
		public function processData()
		{
			//Load $this->data into database
		}
		
		public function getData()
		{
			return $this->data;
		}
		
		public function storeData()
		{
			$aptdata = AptData::Instance();
			$aptdata->importData($this->data);
			//$aptdata->store();
		}
		
		public function getSettings()
		{
			if(is_null($this->settings))
			{
				$opt = get_option('razz_apt_opt');
				$this->settings = $opt[$this->setname];
				
			}
			return $this->settings;
		}
		
		public function getSetting($setting)
		{
			$settings = $this->getSettings();
			return $settings[$setting];
		}
		
		public function addFloor($floor)
		{
			if(!isset($this->data['floors'][$floor]))
				$this->data['floors'][$floor] = array(
					'id'		=> $floor,
					'type' => 'floor',
					'verticies'	=> array()
				);
		}
		public function addRoom($name, $count)
		{
			if(!array_key_exists($name, $this->data['rooms']))
				$this->data['rooms'][$name] = array('-1');
			if(!in_array($count, $this->data['rooms'][$name]))
				$this->data['rooms'][$name][] = $count;
		}
		
		public static function parseMoney($value)
		{
			if(!is_string($value) && !is_numeric($value)) return 0;
			return preg_replace('/[^[0-9]\.]/i', '', $value);
		}
		
		public static function runElement($element, $func)
		{
			if(!is_array($element)) $element = array($element);
			foreach($element as $key=>$value)
			{
				$func($key, $value);
			}
			return;
		}
	}
?>