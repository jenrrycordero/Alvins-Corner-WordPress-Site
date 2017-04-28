<?php
	class GenericImporter extends Importer
	{
		public function __construct()
		{
			parent::__construct('import_generic');
		}
		
		public function addSettings($panel)
		{
			$panel->Title('Generic');

			$myfields = array();
			$myfields[] = $panel->addText('url', array('name'=> 'Property URL', 'std'=> ''), true);
			$authfields = array();
			$authfields[] = $panel->addSelect('type', array('basic'=>'Basic', 'ps', 'Property Solutions'), array('name'=> 'Type', 'std' => 'basic'), true);
			$authfields[] = $panel->addText('user', array('name'=> 'Username', 'std'=> ''), true);
			$authfields[] = $panel->addText('pass', array('name'=> 'Password', 'std'=> ''), true);
			$myfields[] = $panel->addCondition('auth', array('name' => 'Requires Authentication', 'fields' => $authfields, 'std' => false), true);
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
				//if(empty($settings['user']) || empty($settings['pass']) || empty($settings['property_code'])) throw new Exception('Missing identification fields in configuration.');
				
				$context = stream_context_create(array(
					'http' => array(
						'header'  => "Authorization: Basic " . base64_encode($settings['user'] . ":" . $settings['pass'])
					)
				));
				$content = file_get_contents($settings['url'], false, $context);
				if(!$content) throw new Exception('Problem fetching url.');
				$content = json_decode($data_models, false);
				
				if(is_null($data_models) || $data_models === false) throw new Exception('Invalid JSON.');
				
				
				parent::runElement($data_models['unit_styles'], function($key, $fp)
				{
					$id = $fp->style_id;
					$model = array(
						'id' => $id,
						'type' => 'model',
						'name' => $fp->description,
						'sqft' => array(
							'min' => $fp->square_footage,
							'max' => $fp->square_footage
						),
						'rooms' => array(
							'bedrooms'	=> $fp->num_bedrooms,
							'bathrooms'	=> $fp->num_bathrooms
						),
						'terms' => array(
							$fp->num_bedrooms . ' Bedroom',
							$fp->num_bathrooms . ' Bathroom'
						),
						'units' => array(
							'available' => $fp->num_available
						),
						'rent' => array( //or EffectiveRent
							'min' => parent::parseMoney($fp->min_rent),
							'max' => parent::parseMoney($fp->max_rent)
						),
						'deposit' => array(
							'min' => parent::parseMoney($fp->min_deposit),
							'max' => parent::parseMoney($fp->max_deposit)
						)
					);
				});
				
				$data_units = file_get_contents("https://www.on-site.com/web/api/properties/{$settings['property_code']}/units.json", false, $context);
				if(!$data_models) throw new Exception('Problem fetching units.');
				
				
				$identa = array(
					'MarketingName'	=> $ident->MarketingName,
					'Address' => array(
						'lat' => $ident2->Latitude,
						'lon' => $ident2->Longitude
					),
					'Phone' => array(),
					'Email'	=> $ident->Address->Email
				);
				
				foreach($ident->Address as $key=>$value)
				{
					$identa['Address'][$key] = $value;
				}
				$identa['Address']['Zip'] = $ident->Address->PostalCode;
				
				parent::runElement($ident->Phone, function($key, $phone) use(&$identa)
				{
					$identa['Phone'][$phone->{'@attributes'}->PhoneType] = $phone->PhoneNumber;
				});
				
				$this->data['ident'] = array_replace_recursive($this->data['ident'], $identa);
				
				parent::runElement($property->Amenity, function($key, $amenity)
				{
					$type = $amenity->{'@attributes'}->AmenityType;
					if(!isset($this->data['amenities'][$type])) $this->data['amenities'][$type] = array();
					$this->data['amenities'][$type][] = $amenity;
				});
				
				
				parent::runElement($property->Floorplan, function($key, $fp)
				{
					$id = $fp->Identification->IDValue;
					$model = array(
						'id' => $id,
						'type' => 'model',
						'name' => $fp->Name,
						'sqft' => array(
							'min' => $fp->SquareFeet->{'@attributes'}->Min,
							'max' => $fp->SquareFeet->{'@attributes'}->Max
						),
						'rooms' => array(),
						'terms' => array(),
						'units' => array(
							'total' => $fp->UnitCount,
							'available' => $fp->UnitsAvailable
						),
						'rent' => array( //or EffectiveRent
							'min' => parent::parseMoney($fp->MarketRent->{'@attributes'}->Min),
							'max' => parent::parseMoney($fp->MarketRent->{'@attributes'}->Max)
						),
						'deposit' => array(
							'type' => $fp->Deposit->{'@attributes'}->DepositType,
							'min' => parent::parseMoney($fp->Deposit->Amount->ValueRange->{'@attributes'}->Min),
							'max' => parent::parseMoney($fp->Deposit->Amount->ValueRange->{'@attributes'}->Max)
						),
						'image' => $fp->File->Src
					);
					
					parent::runElement($fp->Room, function($key, $room) use(&$model)
					{
						$type = strtolower($room->{'@attributes'}->RoomType);
						$count = $room->Count;
						$model['rooms'][$type] = $count;
						$model['terms'][] = $count . ' ' . ucwords($type);
					});
					
					if(!array_key_exists($id, $this->data['models']))
						$this->data['models'][$id] = array();
					
					$this->data['models'][$id] = array_replace_recursive($this->data['models'][$id], $model);
				});
				
				parent::runElement($property->File, function($key, $file)
				{
					if($file->{'@attributes'}->Active != "true") return;
					$id = $file->{'@attributes'}->FileID;
					$file->id = $id;
					$this->data['files'][$id] = $file;
				});
				
				
				parent::runElement($property->ILS_Unit, function($key, $unit) use(&$floor_regex)
				{
					$id = $unit->Units->Unit->MarketingName;
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
					$date = $unit->Availability->VacateDate;
					$apt = array(
						'id' => $id,
						'type' => 'unit',
						'model' => $unit->Units->Unit->{'@attributes'}->FloorPlanId,
						'floor' => $fid,
						'date' => isset($date) ? $date->{'@attributes'}->Month . '/' . $date->{'@attributes'}->Day . '/' . $date->{'@attributes'}->Year : false,
						'sqft' => array(
							'min' => $unit->Units->Unit->MinSquareFeet,
							'max' => $unit->Units->Unit->MaxSquareFeet
						),
						'rooms' => array(
							'bedrooms' => $unit->Units->Unit->UnitBedrooms,
							'bathrooms' => $unit->Units->Unit->UnitBathrooms
						),
						'rent' => array(
							'unit' => parent::parseMoney($unit->Units->Unit->UnitRent),
							'market' => parent::parseMoney($unit->Units->Unit->MarketRent)
						),
						'amenities' => array()
					);
					
					if(isset($date))
					{
						parent::addRoom('bedroom', $unit->Units->Unit->UnitBedrooms);
						parent::addRoom('bathroom', $unit->Units->Unit->UnitBathrooms);
					}
					
					parent::runElement($unit->Amenity, function($key, $amenity) use(&$apt)
					{
						$type = $amenity->{'@attributes'}->AmenityType;
						if(!isset($apt['amenities'][$type])) $apt['amenities'][$type] = array();
						$apt['amenities'][$type][] = $amenity;
					});
					
					if(!array_key_exists($id, $this->data['units']))
						$this->data['units'][$id] = array();
					
					$this->data['units'][$id] = array_replace_recursive($this->data['units'][$id], $apt);
				});
				
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