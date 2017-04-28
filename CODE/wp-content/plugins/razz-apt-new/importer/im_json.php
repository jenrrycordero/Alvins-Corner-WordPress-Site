<?php
	class JSONImporter extends Importer
	{
		public function __construct()
		{
			parent::__construct('import_json');
		}
		
		public function addSettings($panel)
		{
			$panel->Title('JSON');
			
			$myfields = array();
			$myfields[] = $panel->addCode('json', array('name'=> 'JSON', 'std'=> '{}', 'syntax'=>'javascript', 'desc' => 'JSON Data to import into database. <strong>Use with caution</strong>'), true);
			$panel->addCondition($this->setname, array('name' => 'Active', 'fields' => $myfields, 'std' => false));
		}
		
		public function fetchData()
		{
			try
			{
				$settings = parent::getSettings();
				$data = json_decode(stripslashes($settings['json']), true);
				if(is_null($data) || $data === false) throw new Exception('Invalid JSON.');
				$this->data = parent::cleanData($data);
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