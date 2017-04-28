<?php
	class RentcafeImporter extends Importer
	{
		public function __construct()
		{
			parent::__construct('import_rc');
		}
	
		public function addSettings($panel)
		{
			$panel->Title('Rentcafe');
			
			$myfields = array();
			$myfields[] = $panel->Subtitle('Identification', true);
			$myfields[] = $panel->addText('company_code', array('name'=> 'Company Code', 'std'=> ''), true);
			$myfields[] = $panel->addText('property_code', array('name'=> 'Property Code', 'std'=> ''), true);
			$myfields[] = $panel->Subtitle('Parsing', true);
			$myfields[] = $panel->addText('parsing_floor', array('name'=> 'Floor Regex', 'std'=> '', 'desc' => '<strong>Common Formats:</strong><br /><strong>1</strong>23 - <code>^([0-9])[0-9]{2}$</code><br />1-<strong>1</strong>23 - <code>^[0-9]-([0-9])[0-9]{2}$</code><br />1<strong>1</strong>23 - <code>^[0-9]([0-9])[0-9]{2}$</code>'), true);
			$panel->addCondition($this->setname, array('name' => 'Active', 'fields' => $myfields, 'std' => false));
		}

		
		public function fetchData()
		{
			$settings = parent::getSettings();
			try
			{
				$ccode = $settings['company_code'];
				$pcode = $settings['property_code'];
				$dataref = &$this->data;
				$floor_regex = !empty($settings['parsing_floor']) ? $settings['parsing_floor'] : '^([0-9])[0-9]{2}$';
				if(empty($ccode) || empty($pcode))	throw new Exception('Company Code or Property Code not specified.');

				$xmldata = file_get_contents("http://www.rentcafe.com/feeds/{$ccode}.xml");
				if(!$xmldata) throw new Exception('Invalid Company Code.');
				
				$xml = simplexml_load_string($xmldata);
				if($xml === false) throw new Exception('Invalid XML.(1)');
				$json = json_encode($xml);
				if($json === false) throw new Exception('Invalid XML.(2)');
				$data = json_decode($json, false);
				if(is_null($data) || $data === false) throw new Exception('Invalid XML.(3)');
				
				$property = null;
				if(!is_array($data->Property)) $data->Property = array($data->Property);
				foreach($data->Property as $prop)
				{
					if($prop->Identification->PrimaryID == $pcode)
					{
						$property = $prop;
						break;
					}
				}
				if(is_null($property)) throw new Exception('Invalid Property Code.');

				$unitdata = json_decode(file_get_contents("http://www.rentcafe.com/rentcafeapi.aspx?requestType=apartmentavailability&companyCode={$ccode}&propertycode={$pcode}"), true);


				$ident = $property->Identification;
				$identa = array(
					'MarketingName'	=> $ident->MarketingName,
					'Address' => array(
						'lat' => $ident->Latitude,
						'lon' => $ident->Longitude
					),
					'Phone' => array(
						'primary' => $ident->Phone->Number,
						'fax' => $ident->Fax->Number
					),
					'Email'	=> $ident->Email
				);
				foreach($ident->Address as $key=>$value)
				{
					$identa['Address'][$key] = $value;
				}
				$this->data['ident'] = array_replace_recursive($this->data['ident'], $identa);
				
				
				if(!is_array($property->Amenities)) $property->Amenities = objectToArray($property->Amenities);
				$this->data['amenities'] = array_replace_recursive($this->data['amenities'], $property->Amenities);

				$mymodels = &$this->data['models'];
				parent::runElement($property->Floorplan, function($key, $fp) use(&$mymodels)
				{
					$id = $fp->{'@attributes'}->id;
					$model = array(
						'id' => $id,
						'type' => 'model',
						'name' => $fp->Name,
						'sqft' => array(
							'min' => $fp->SquareFeet->{'@attributes'}->min,
							'max' => $fp->SquareFeet->{'@attributes'}->max
						),
						'rooms' => array(),
						'terms' => array(),
						'units' => array(
							'total' => $fp->UnitCount,
							'available' => $fp->UnitsAvailable
						),
						'rent' => array( //or EffectiveRent
							'min' => $fp->MarketRent->{'@attributes'}->min,
							'max' => $fp->MarketRent->{'@attributes'}->max
						),
						'deposit' => array(
							'min' => $fp->Deposit->Amount,
							'max' => $fp->Deposit->Amount
						)
					);
					if(!is_array($fp->Room)) $fp->Room = array($fp->Room);
					foreach($fp->Room as $room)
					{
						$type = $room->{'@attributes'}->type;
						$count = $room->Count;
						$model['rooms'][$type] = $count;
						$model['terms'][] = $count . ' ' . ucwords($type);
					}
					
					if(!array_key_exists($id, $mymodels))
						$mymodels[$id] = array();

					$mymodels[$id] = array_replace_recursive($mymodels[$id], $model);
				});
				
				parent::runElement($property->File, function($key, $file) use($dataref)
				{
					if($file->{'@attributes'}->active != "true") return;
					$id = $file->{'@attributes'}->id;
					$file->id = $id;
					$dataref['files'][$id] = $file;
					if(isset($dataref['models'][$id]))
						$dataref['models'][$id]['image'] = $file->Src;
				});


				$myunits = &$this->data['units'];

				parent::runElement($property->ILS_Unit, function($key, $unit) use(&$myunits, $mymodels, $floor_regex)
				{
					$id = $unit->UnitID;
					$mdl = $mymodels[$unit->FloorplanID];
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

					$apt = array(
						'id' => $id,
						'type' => 'unit',
						'model' => $unit->FloorplanID,
						'floor' => $fid,
						'date' => $unit->DateAvailable,
						'sqft' => array(
							'min' => $unit->MinSquareFeet,
							'max' => $unit->MaxSquareFeet
						),
						'rooms' => array(
							'bedroom' => $unit->UnitBedrooms,
							'bathroom' => $unit->UnitBathrooms+0
						),
						'rent' => array(
							'unit' => $unit->UnitRent,
							'market' => $unit->MarketRent
						),
						'deposit' => array(
							'min' => $mdl['deposit']['min'],
							'max' => $mdl['deposit']['max']
						)
					);

					if(!array_key_exists($id, $myunits))
						$myunits[$id] = array();

					$myunits[$id] = array_replace_recursive($myunits[$id], $apt);
				});

				foreach($myunits as $apt)
				{
					parent::addFloor($apt['floor']);
					parent::addRoom('bedroom', $apt['rooms']['bedroom']+0);
					parent::addRoom('bathroom', $apt['rooms']['bathroom']+0);
				}


				/*
				foreach($unitdata as $unit)
				{
					$id = $unit['ApartmentName'];
					$mdl = $mymodels[$unit['FloorplanId']];
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

					$apt = array(
						'id' => $id,
						'type' => 'unit',
						'model' => $unit['FloorplanId'],
						'floor' => $fid,
						'date' => $unit['AvailableDate'],
						'sqft' => array(
							'min' => $unit['SQFT'],
							'max' => $unit['SQFT']
						),
						'rooms' => array(
							'bedroom' => $unit['Beds'],
							'bathroom' => $unit['Baths']+0
						),
						'rent' => array(
							'unit' => $unit['MinimumRent'],
							'market' => $unit['MinimumRent']
						),
						'deposit' => array(
							'min' => $mdl['deposit']['min'],
							'max' => $mdl['deposit']['max']
						)
					);

					if(!empty($apt['date']))
					{
						parent::addRoom('bedroom', $apt['rooms']['bedroom']+0);
						parent::addRoom('bathroom', $apt['rooms']['bathroom']+0);
					}

					if(!array_key_exists($id, $this->data['units']))
						$this->data['units'][$id] = array();

					$this->data['units'][$id] = array_replace_recursive($this->data['units'][$id], $apt);
				}*/
				
				$this->data = parent::cleanData($this->data);
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