<?php
class PlotImporter
{
	
	public function __construct()
	{
		add_action('admin_menu', array($this, 'admin_menu'));
	}
	
	function admin_menu()
	{
		add_management_page
		(
			"CSV Plot Importer",
			"CSV Plot Importer",
			"manage_options",
			"rapt_plot_import",
			array($this, 'display_form')
		);
		add_submenu_page
		(
			"rz_apt_menu_import",
			"CSV Plot Importer",
			"CSV Plot Importer",
			"manage_options",
			"rz_apt_menu_csv_units",
			array($this, 'display_form')
		);
	}
	
	function display_form()
	{
		if(isset($_POST['rapt_plotimporter']) && wp_verify_nonce($_POST['rapt_plotimporter'], plugin_basename( __FILE__ )))
		{
			$this->ParseCSV($_POST['csvdata']);
			echo "<div class=\"updated\"><strong>Success</strong></div>";
		}
		
?>
<div id="icon-tools" class="icon32"></div>
<h2>CSV Plot Importer</h2>
<form method="post" target="_self">
<?php wp_nonce_field(plugin_basename( __FILE__ ), 'rapt_plotimporter'); ?>
<textarea name="csvdata" style="width: 95%; height: 600px;"></textarea>
<br /><br />
<input type="submit" value="Begin Import" class="button-primary" />
</form>
<?php
	}
	
	public function ParseCSV($data)
	{
		$posts = $this->getAptPosts();
	
		$rows = explode("\n", $data);
		foreach($rows as $row)
		{
			$csv = str_getcsv($row);
			if(!$csv || count($csv) != 3) continue;
			$unit = $csv[0];
			$x = $csv[1];
			$y = $csv[2];
			
			if(!isset($posts[$unit])) continue;
			$id = $posts[$unit];
			update_post_meta($id, '_rapt_unit_locx', sanitize_text_field($x));
			update_post_meta($id, '_rapt_unit_locy', sanitize_text_field($y));
			wp_publish_post($id);
		}
	}
	
	public function getAptPosts()
	{
		$args = array(
			'post_type' 	=> 'apt',
			'post_status'	=> array
							(
								'publish',
								'future',
								'draft',
								'pending'
							),
			'orderby'		=> 'title',
			'order'			=> 'ASC',
			'posts_per_page'=> -1
		);
		
		$units = new WP_Query($args);
		
		$posts = array();
		
		foreach($units->posts as $unit)
		{
			$posts[$unit->post_title] = $unit->ID;
		}
		
		return $posts;
	}
	
}
?>