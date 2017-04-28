<?php
	class OnSiteImporter extends Importer
	{
		public function __construct()
		{
			parent::__construct('import_os');
		}
		
		public function addSettings($panel)
		{
			$panel->Title('On-Site');

			$myfields = array();
			$myfields[] = $panel->Subtitle('Identification', true);
			$myfields[] = $panel->addText('user', array('name'=> 'Username', 'std'=> ''), true); //wood_gtma
			$myfields[] = $panel->addText('pass', array('name'=> 'Password', 'std'=> ''), true); //website519
			$myfields[] = $panel->addText('property_code', array('name'=> 'Property ID', 'std'=> ''), true); //
			$myfields[] = $panel->Subtitle('Parsing', true);
			$myfields[] = $panel->addText('parsing_floor', array('name'=> 'Floor Regex', 'std'=> '', 'desc' => '<strong>Common Formats:</strong><br /><strong>1</strong>23 - <code>^([0-9])[0-9]{2}$</code><br />1-<strong>1</strong>23 - <code>^[0-9]-([0-9])[0-9]{2}$</code><br />1<strong>1</strong>23 - <code>^[0-9]([0-9])[0-9]{2}$</code>'), true);
			$panel->addCondition($this->setname, array('name' => 'Active', 'fields' => $myfields, 'std' => false));
		}
		
		public function fetchData()
		{
			try
			{
				$settings = parent::getSettings();
				$floor_regex = !empty($settings['parsing_floor']) ? $settings['parsing_floor'] : '^([0-9])[0-9]{2}$';
				if(empty($settings['user']) || empty($settings['pass']) || empty($settings['property_code'])) throw new Exception('Missing identification fields in configuration.');
				
				$context = stream_context_create(array(
					'http' => array(
						'header'  => "Authorization: Basic " . base64_encode($settings['user'] . ":" . $settings['pass'])
					)
				));
				
				$data_models = @file_get_contents("https://www.on-site.com/web/api/properties/{$settings['property_code']}.json", false, $context);
				if(!$data_models) throw new Exception('Problem fetching models.');
				$data_models = json_decode($data_models, false);
				if(is_null($data_models) || $data_models === false) throw new Exception('Invalid Model JSON.');
				$data_models = $data_models->property;
				
				$mymodels = &$this->data['models'];
				parent::runElement($data_models->unit_styles, function($key, $fp) use(&$mymodels, $settings)
				{
					$id = $fp->style_id;
					$model = array(
						'id' => $id,
						'type' => 'model',
						'name' => $fp->description,
						'disabled' => false,
						'sqft' => array(
							'min' => $fp->square_footage,
							'max' => $fp->square_footage
						),
						'rooms' => array(
							'bedroom'	=> $fp->num_bedrooms,
							'bathroom'	=> $fp->num_bathrooms
						),
						'terms' => array(
							$fp->num_bedrooms . ' Bedroom',
							$fp->num_bathrooms . ' Bathroom'
						),
						'units' => array(
							'available' => $fp->num_available
						),
						'rent' => array( //or EffectiveRent
							'min' => Importer::parseMoney($fp->min_rent),
							'max' => Importer::parseMoney($fp->max_rent)
						),
						'deposit' => array(
							'min' => Importer::parseMoney($fp->min_deposit),
							'max' => Importer::parseMoney($fp->max_deposit)
						),
						'applyurl' => "https://www.on-site.com/apply/property/{$settings['property_code']}?floorplan=" . urlencode($fp->description)
					);
					
					if(!array_key_exists($id, $mymodels))
						$mymodels[$id] = array();
					
					$mymodels[$id] = array_replace_recursive($mymodels[$id], $model);
				});
				
				$ident = array(
					'MarketingName'	=> $data_models->property_name,
					'Address' => array(
						'Address'	=> $data_models->street_addr,
						'State'		=> $data_models->state,
						'City'		=> $data_models->city,
						'Zip'		=> $data_models->zip_code,
					)
				);
				$this->data['ident'] = array_replace_recursive($this->data['ident'], $ident);
				
				$data_units = @file_get_contents("https://www.on-site.com/web/api/properties/{$settings['property_code']}/units.json?available_only=true", false, $context);
				if(!$data_units) throw new Exception('Problem fetching units.');
				$data_units = json_decode($data_units, false);
				if(is_null($data_units) || $data_units === false) throw new Exception('Invalid Unit JSON.');
				
				foreach($data_units as $key=>$unit)
				{
					$unit = $unit->unit;
					$id = $unit->apartment_num;
					$fid = 0;
					$patterns = explode('::', $floor_regex);
					foreach($patterns as $pattern)
					{
						if(strpos($pattern, '==') !== false)
							list($pattern, $override) = explode('==', $pattern);
						
						if(preg_match("/{$pattern}/", $id, $matches))
						{
							if(isset($override))
								$fid = $override;
							else
								$fid = $matches[1];
							break;
						}
						unset($override);
					}
					
					parent::addFloor($fid);
					$date = $unit->available_date;
					if(isset($date))
					{
						list($y,$m,$d) = explode('-', $date);
						$date = "{$m}/{$d}/{$y}";
					}
					else
					{
						$date = false;
					}
					$mdl = $mymodels[$unit->style_id];
					$apt = array(
						'id' => $id,
						'type' => 'unit',
						'model' => $unit->style_id,
						'floor' => $fid,
						'date' => $date,
						'disabled' => ($date === false || (isset($unit->show_online) && !$unit->show_online)),
						'rent' => array(
							'market' => parent::parseMoney($unit->rent_amount)
							//'market' => $unit->rent_amount
						),
						'deposit' => $unit->deposit_amount,
						'applyurl' => "https://www.on-site.com/apply/property/{$settings['property_code']}?floorplan=" . urlencode($mdl['name'])
					);
					if(isset($this->data['models'][$apt['model']]))
					{
						$model = $this->data['models'][$apt['model']];
						$apt['sqft'] = $model['sqft'];
						$apt['rooms'] = $model['rooms'];
					}
					
					if($date !== false && $apt['rooms'] !== false)
					{
						parent::addRoom('bedroom', $apt['rooms']['bedroom']);
						parent::addRoom('bathroom', $apt['rooms']['bathroom']);
					}
					
					if(!array_key_exists($id, $this->data['units']))
						$this->data['units'][$id] = array();
					
					$this->data['units'][$id] = array_replace_recursive($this->data['units'][$id], $apt);
				}
				
				$this->data = parent::cleanData($this->data);
				//print_r($this->data);
				return true;
			}
			catch(Exception $ex)
			{
				return array(
					'res'	=> 'err',
					'msg'	=> '<strong>Error:</strong> ' . $ex->getMessage()
				);
			}
		}
	}
?>