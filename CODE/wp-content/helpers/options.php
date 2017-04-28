<?php
class OptionPage
{
	private $options;
	
	private $prefix, $title, $menu_title, $slug, $parentmenu, $permission;
	private $sections = array();
	private $fields = array();
	
	private $option_section_id = 'rsvp_section_id';
	
	public function __construct($prefix, $title, $menu_title, $slug, $parentmenu="", $permission="manage_options")
	{
		$this->prefix = $prefix . '_';
		$this->title = $title;
		$this->menu_title = $menu_title;
		$this->slug = $slug;
		$this->parentmenu = $parentmenu;
		$this->permission = $permission;
		
		$this->options = $this->getOptions();
		
		add_action('admin_menu', array($this, 'add_plugin_page'));
		add_action('admin_init', array($this, 'page_init'));
		add_filter("option_page_capability_{$prefix}_settings_group", array($this, 'option_capability'));
	}
	
	function option_capability($permission)
	{
		return $this->permission;
	}
	
	public function getOption($group, $id)
	{
		$index = $this->prefix . $group . '_' . $id;
		return isset($this->options[$index]) ? $this->options[$index] : '';
	}
	
	public function getOptions()
	{
		return get_option($this->prefix . 'options');
	}
	
	public function add_plugin_page()
	{
		if(empty($this->parentmenu))
		{
			add_options_page(
				$this->title, 
				$this->menu_title, 
				$this->permission, 
				$this->slug, 
				array($this, 'create_admin_page')
			);
		}
		else
		{
			add_submenu_page
			(
				$this->parentmenu,
				$this->title,
				$this->menu_title,
				$this->permission,
				$this->slug,
				array($this, 'create_admin_page')
			);
		}
	}
	
	public function create_admin_page()
	{
		?>
			<div class="wrap">
				<?php screen_icon(); ?>
				<h2><?php echo $this->title; ?></h2>           
				<form method="post" action="options.php">
				<?php
					settings_fields($this->prefix . 'settings_group');   
					do_settings_sections($this->slug);
					submit_button(); 
				?>
				</form>
			</div>
		<?php
	}
	
	public function addSection($id, $title, $info)
	{
		$this->sections[] = array(
			'id'		=> $this->prefix . $id,
			'title'		=> $title,
			'callback'	=> function() use ($info) { echo $info; }
		);
		return $this;
	}
	
	public function addField($group, $id, $title, $input, $inputdata = array())
	{
		$this->fields[] = array(
			'id'		=> $this->prefix . $group . '_' . $id,
			'title'		=> $title,
			'group'		=> $this->prefix . $group,
			'input'		=> $input,
			'inputdata'	=> $inputdata
		);
		return $this;
	}
	
	public function page_init()
	{
		register_setting(
			$this->prefix . 'settings_group',
			$this->prefix . 'options',
			array($this, 'sanitize')
		);
		
		foreach($this->sections as $section)
		{
			add_settings_section(
				$section['id'],
				$section['title'],
				$section['callback'],
				$this->slug
			);
		}
		
		foreach($this->fields as $field)
		{
			add_settings_field(
				$field['id'],
				$field['title'],
				array($this, 'field_callback'),
				$this->slug,
				$field['group'],
				array(
					'id'		=> $field['id'],
					'input'		=> $field['input'],
					'inputdata'	=> $field['inputdata']
				)
			);
		}
	}
	
	public function field_callback($args)
	{
		$id			= $args['id'];
		$input		= $args['input'];
		$inputdata	= $args['inputdata'];
		
		$class = isset($inputdata['class']) ? $inputdata['class'] : '';
		$style = isset($inputdata['style']) ? $inputdata['style'] : '';
		
		$value = isset($this->options[$id]) ? $this->options[$id] : '';
		
		switch($input)
		{
			case 'textbox':
			{
				echo '<input id="' . $id . '" name="' . $this->prefix . 'options' . '[' . $id . ']" class="' . $class . '" style="' . $style . '" value="' . esc_attr($value) . '" />';
				break;
			}
			case 'textarea':
			{
				$rows = isset($inputdata['rows']) ? $inputdata['rows'] : 8;
				$cols = isset($inputdata['cols']) ? $inputdata['cols'] : 50;
				
				echo '<textarea id="' . $id . '" name="' . $this->prefix . 'options' . '[' . $id . ']" rows="' . $rows . '" cols="' . $cols . '" class="' . $class . '" style="' . $style . '">' . esc_textarea($value) . '</textarea>';
				break;
			}
		}
	}
	
	public function sanitize($input)
	{
		//TODO: Maybe actually sanitize this.
		return $input;
	}
}
?>