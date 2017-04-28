<?php
class Importer
{
	
	public function __construct()
	{
		add_action('admin_menu', array($this, 'admin_menu'));
	}
	
	function admin_menu()
	{
		add_options_page
		(
			"Apartment Tool Importer",
			"Apartment Tool Importer",
			"manage_options",
			"rapt_apt_import",
			array($this, 'display_settings')
		);
		
	}
	
	function enqueue_scripts()
	{
		 wp_enqueue_script(
			'apt_importer', 
			plugins_url( '/js/jquery.importer.js', __FILE__ ),
			array(
				'jquery-ui-sortable',
				'jquery'
			)
		);
	}
	
	function display_settings()
	{
		$this->enqueue_scripts();
		if(isset($_POST['rapt_importer']) && wp_verify_nonce($_POST['rapt_importer'], plugin_basename( __FILE__ )))
		{
			//TODO: save stuff
			echo "<div class=\"updated\"><strong>Success</strong></div>";
		}
		
?>
<div id="icon-tools" class="icon32"></div>
<h2>Apartment Tool Importer</h2>
<form method="post" target="_self">
<?php wp_nonce_field(plugin_basename( __FILE__ ), 'rapt_importer'); ?>
<div id="poststuff" class="metabox-holder columns-2">
	<div id="postbox-container-1" class="postbox-container">
		<div class="meta-box-sortables ui-sortable">
			<div class="postbox">
				<div class="handlediv"></div>
				<h3 class="hndle">
					<span>Title</span>
				</h3>
				<div class="inside">
					Content
				</div>
			</div>
		</div>
		<div class="meta-box-sortables ui-sortable">
			<div class="postbox">
				<div class="handlediv"></div>
				<h3 class="hndle">
					<span>Title</span>
				</h3>
				<div class="inside">
					Content
				</div>
			</div>
		</div>
		<div class="meta-box-sortables ui-sortable">
			<div class="postbox">
				<div class="handlediv"></div>
				<h3 class="hndle">
					<span>Title</span>
				</h3>
				<div class="inside">
					Content
				</div>
			</div>
		</div>
	</div>
	<div id="postbox-container-2" class="postbox-container">
	
	</div>
</div>


<br /><br />
<input type="submit" value="Save" class="button-primary" />
</form>
<?php
	}
	
}
?>