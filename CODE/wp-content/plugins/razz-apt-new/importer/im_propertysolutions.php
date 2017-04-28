<?php
	class PropertySolutionsImporter extends Importer
	{
		public function __construct()
		{
			parent::__construct('import_ps');
		}
		
		public function addSettings($panel)
		{
			$panel->Title('Property Solutions');

			$myfields = array();
			$myfields[] = $panel->Subtitle('Identification', true);
			$myfields[] = $panel->addText('url', array('name'=> 'Property URL', 'std'=> ''), true); //https://lincolnapts.propertysolutions.com/api/propertyunits
			$myfields[] = $panel->addText('user', array('name'=> 'Username', 'std'=> ''), true); //razzmatazz
			$myfields[] = $panel->addText('pass', array('name'=> 'Password', 'std'=> ''), true); //Mourra786
			$myfields[] = $panel->addText('property_code', array('name'=> 'Property ID', 'std'=> ''), true); //63919
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
				if(empty($settings['url']) || empty($settings['user']) || empty($settings['pass']) || empty($settings['property_code'])) throw new Exception('Missing identification fields in configuration.');
				ob_start();
?>
<request>
	<auth>
		<type>basic</type>
		<password><?php echo $settings['pass']; ?></password>
		<username><?php echo $settings['user']; ?></username>
	</auth>
	<requestId>15</requestId>
	<method>
		<name>getMitsPropertyUnits</name>
		<params>
			<propertyIds><?php echo $settings['property_code']; ?></propertyIds>
			<availableUnitsOnly>1</availableUnitsOnly>
			<usePropertyPreferences>0</usePropertyPreferences>
			<includeDisabledFloorplans>0</includeDisabledFloorplans>
		</params>
	</method>
</request>
<?php
				$xml_request = ob_get_clean();
				$ch = curl_init($settings['url']);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: APPLICATION/XML; CHARSET=UTF-8'));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_request);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$xmldata = curl_exec($ch);
				curl_close($ch);
				//print_r($xmldata);
				//$xmldata = file_get_contents("https://www.rentcafe.com/feeds/{$ccode}.xml");
				if(!$xmldata) throw new Exception('Invalid Response.');
				
				$xml = simplexml_load_string($xmldata);
				if($xml === false) throw new Exception('Invalid XML.(1)');
				if(isset($xml->error)) throw new Exception('Response: ' . $xml->error->message);
				$json = json_encode($xml);
				if($json === false) throw new Exception('Invalid XML.(2)');
				$data = json_decode($json, false);
				if(is_null($data) || $data === false) throw new Exception('Invalid XML.(3)');
				
				$property = $data->result->PhysicalProperty->Property;
				$ident = $property->PropertyID;
				$ident2 = $property->ILS_Identification;
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
				
				$amenities = &$this->data['amenities'];
				parent::runElement($property->Amenity, function($key, $amenity) use(&$amenities)
				{
					$type = $amenity->{'@attributes'}->AmenityType;
					if(!isset($amenities[$type])) $amenities[$type] = array();
					$amenities[$type][] = $amenity;
				});
				
				
				$models = &$this->data['models'];
				parent::runElement($property->Floorplan, function($key, $fp) use(&$models)
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
							'min' => Importer::parseMoney($fp->MarketRent->{'@attributes'}->Min),
							'max' => Importer::parseMoney($fp->MarketRent->{'@attributes'}->Max)
						),
						'deposit' => array(
							'type' => $fp->Deposit->{'@attributes'}->DepositType,
							'min' => Importer::parseMoney($fp->Deposit->Amount->ValueRange->{'@attributes'}->Min),
							'max' => Importer::parseMoney($fp->Deposit->Amount->ValueRange->{'@attributes'}->Max)
						),
						'image' => $fp->File->Src
					);
					
					Importer::runElement($fp->Room, function($key, $room) use(&$model)
					{
						$type = strtolower($room->{'@attributes'}->RoomType);
						$count = $room->Count;
						$model['rooms'][$type] = $count;
						$model['terms'][] = $count . ' ' . ucwords($type);
					});
					
					if(!array_key_exists($id, $models))
						$models[$id] = array();
					
					$models[$id] = array_replace_recursive($models[$id], $model);
				});
				
				$files = &$this->data['files'];
				parent::runElement($property->File, function($key, $file) use(&$files)
				{
					if($file->{'@attributes'}->Active != "true") return;
					$id = $file->{'@attributes'}->FileID;
					$file->id = $id;
					$files[$id] = $file;
				});
				
				$floorlist = array();
				$myunits = &$this->data['units'];
				parent::runElement($property->ILS_Unit, function($key, $unit) use(&$floor_regex, &$floorlist, &$myunits)
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
					
					$floorlist[] = $fid;
					$date = $unit->Availability->VacateDate;
					$apt = array(
						'id' => $id,
						'type' => 'unit',
						'model' => $unit->Units->Unit->{'@attributes'}->FloorPlanId,
						'floor' => $fid,
						'date' => isset($date) ? $date->{'@attributes'}->Month . '/' . $date->{'@attributes'}->Day . '/' . $date->{'@attributes'}->Year : false,
						'disabled' => (!isset($date)),
						'sqft' => array(
							'min' => $unit->Units->Unit->MinSquareFeet,
							'max' => $unit->Units->Unit->MaxSquareFeet
						),
						'rooms' => array(
							'bedroom' => $unit->Units->Unit->UnitBedrooms,
							'bathroom' => $unit->Units->Unit->UnitBathrooms
						),
						'rent' => array(
							'unit' => Importer::parseMoney($unit->Units->Unit->UnitRent),
							'market' => Importer::parseMoney($unit->Units->Unit->MarketRent)
						),
						'amenities' => array()
					);
					
					if(!array_key_exists($id, $myunits))
						$myunits[$id] = array();

					$myunits[$id] = array_replace_recursive($myunits[$id], $apt);
				});
				foreach($floorlist as $floorid)
				{
					parent::addFloor($floorid);
				}

				foreach($myunits as $unit)
				{
					parent::addRoom('bedroom', $unit['rooms']['bedroom']);
					parent::addRoom('bathroom', $unit['rooms']['bathroom']);

//					parent::runElement($unit->Amenity, function($key, $amenity) use(&$apt)
//					{
//						$type = $amenity->{'@attributes'}->AmenityType;
//						if(!isset($apt['amenities'][$type])) $apt['amenities'][$type] = array();
//						$apt['amenities'][$type][] = $amenity;
//					});
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