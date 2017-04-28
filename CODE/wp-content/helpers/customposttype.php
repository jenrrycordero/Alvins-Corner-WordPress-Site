<?php
class CustomPostType
{
	private $posttype, $labels, $supports;
	private $fields;
	
	public function __construct($posttype, $labels, $supports)
	{
		$this->posttype = $posttype;
		$this->labels = $labels;
		$this->supports = $supports;
		
		add_action('admin_init', array($this, 'finish'));
		add_action('init', array($this, 'create_post_type'));
		add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
		add_action('save_post', array($this, 'save_meta_box'));
		
		add_filter('manage_edit-' . $posttype . '_columns', array($this, 'create_cols'));
		add_action('manage_' . $posttype . '_posts_custom_column', array($this, 'col_data'), 1000);
		add_filter('manage_edit-' . $posttype . '_sortable_columns', array($this, 'col_sort'));
		add_action('load-edit.php', array($this, 'edit_load'));
	}
	
	public function Import($csv)
	{
		if(!is_array($csv))
		{
			$filename = $csv;
			$header = NULL;
			$csv = array();
			if (($handle = fopen($filename, 'r')) !== false)
			{
				while (($row = fgetcsv($handle)) !== false)
				{
					if(!$header)
						$header = $row;
					else
						$csv[] = array_combine($header, $row);
				}
				fclose($handle);
			}
			else
			{
				return false;
			}
		}
		
		foreach($csv as $entry)
		{
			$post = array(
				'post_type'			=>	$this->posttype,
				'post_status'		=>	'draft',
				'post_title'		=>	(array_key_exists('fname', $entry) || array_key_exists('lname', $entry)) ? sanitize_text_field($entry['fname'] . ' ' . $entry['lname']) : "Unnamed"
			);
		
			$pid = wp_insert_post($post);
			foreach($this->fields as $id=>$field)
			{
				$value = null;
				if(array_key_exists($id, $entry))
				{
					switch($field['type'])
					{
						case 'select':
						{
							if(empty($entry[$id]))
							{
								reset($field['data']['options']);
								$value = key($field['data']['options']);
							}
							else
							{
								$value = $entry[$id];
							}
							break;
						}
						default:
						{
							$value = $entry[$id];
							break;
						}
					}
				}
				else
				{ //Defaults
					switch($field['type'])
					{
						case 'select':
						{
							reset($field['data']['options']);
							$value = key($field['data']['options']);
							break;
						}
						default:
						{
							$value = "";
							break;
						}
					}
				}
				update_post_meta($pid, $field['metaid'], sanitize_text_field($value));
			}
		}
		return $csv;
	}
	
	public function finish()
	{
		$this->handleExport();
	}
	
	function handleExport()
	{
		if(!isset($_REQUEST['get_export']) || $_REQUEST['get_export'] != $this->posttype || !current_user_can('edit_page')) return;
		
		$filename = apply_filters($this->posttype . "_export_filename", $this->posttype . '_export_' . date('Y_m_d-H_i_s') . '.csv');
		
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header("Pragma: no-cache");
		header("Expires: 0");
		
		$first = true;
		foreach($this->fields as $field)
		{
			if(!$first) echo ",";
			echo '"' . $field['name'] . '"';
			$first = false;
		}
		
		$query = new WP_Query(array(
			'post_type'			=> $this->posttype,
			'posts_per_page'	=> -1
		));
		
		$posts = json_decode(json_encode($query->posts), true);
		
		for($i = 0; $i < count($posts); $i++)
		{
			$post = $posts[$i];
			$pid = $post['ID'];
			$posts[$i]['meta'] = array();
			foreach($this->fields as $key => $field)
			{
				$value = get_post_meta($pid, $field['metaid'], true);

				switch($field['type'])
				{
					case 'select':
					{
						if(is_array($value)) $value = $value[0];
						$value = $field['data']['options'][$value];
						break;
					}
				}
				$posts[$i]['meta'][$key] = $value;
			}
		}
		
		$posts = apply_filters($this->posttype . "_export_posts", $posts);
		
		foreach($posts as $post)
		{
			$pid = $post['ID'];
			
			echo "\n";
			$first = true;
			foreach($this->fields as $key => $field)
			{
				if(!$first) echo ",";
				$value = $post['meta'][$key];
				$value = str_replace('"', '""', $value);
				echo '"' . $value . '"';
				$first = false;
			}
		}

		wp_reset_postdata();
		
		die;
	}
	
	function edit_load()
	{
		add_filter('request', array($this, 'col_sort_handler'));
	}
	
	function col_sort_handler($vars)
	{
		if(!isset($vars['post_type']) || $vars['post_type'] != $this->posttype) return $vars;
		
		//meta_value_num
		
		
		if(!$vars['meta_query'])
			$vars['meta_query'] = array();
		
		foreach($this->fields as $field)
		{
			if(!$field['canfilter'] || !isset($_GET[$field['id']])) continue;
			$get = $_GET[$field['id']];
			switch($field['type'])
			{
				case 'select':
				{
					$val = 0;
					foreach($field['data']['options'] as $key => $option)
					{
						if($option != $get) continue;
						$val = $key;
						break;
					}
					$vars['meta_query'][] = array(
						'key'		=> $field['metaid'],
						'compare'	=> '=',
						'value'		=> $val
					);
					break;
				}
				case 'checkbox':
				{
					if($get == 'Yes')
					{
						$vars['meta_query'][] = array(
							'key'		=> $field['metaid'],
							'compare'	=> 'LIKE',
							'value'		=> 'yes'
						);
					}
					else
					{
						$vars['meta_query']['relation'] = 'OR';
						$vars['meta_query'][] = array(
							'key'		=> $field['metaid'],
							'compare'	=> 'NOT LIKE',
							'value'		=> 'yes'
						);
						$vars['meta_query'][] = array(
							'key'		=> $field['metaid'],
							'compare'	=> 'NOT EXISTS',
							'value'		=> ''
						);
					}
					break;
				}
				default:
				{
					$vars['meta_query'][] = array(
						'key'		=> $field['metaid'],
						'compare'	=> '=',
						'value'		=> $get
					);
				}
			}
		}
		
		$orderby = $this->findFieldByMetaID($vars['orderby']);
		
		if($orderby !== false)
		{
			$vars = array_merge(
				$vars,
				array(
					'meta_key'	=> $orderby['metaid'],
					'orderby'	=> $orderby['orderby']
				)
			);
		}
		
		return $vars;
	}
	
	function col_sort($cols)
	{
		foreach($this->fields as $field)
		{
			if(!$field['sort']) continue;
			
			$cols[$field['metaid']] = $field['metaid'];
		}
		return $cols;
	}
	
	public function col_data($col)
	{
		global $post;
		$pid = $post->ID;
		
		$url = $_SERVER['REQUEST_URI'];
		if(isset($_POST['_wp_http_referer']))
			$url = $_POST['_wp_http_referer'];
		
		$val = get_post_meta($pid, $col, true);
		$field = $this->findFieldByMetaID($col);
		if($field === false)
		{
			echo $val;
			return;
		}
		
		$display = $val;
		
		switch($field['type'])
		{
			case 'select':
			{
				if(is_array($val)) $val = $val[0];
				$display = $field['data']['options'][$val];
				break;
			}
			case 'checkbox':
			{
				$display = (strstr(is_array($val) ? implode($val) : $val, 'yes') !== false) ? 'Yes' : 'No';
				break;
			}
		}
		
		if($field['canfilter'])
		{
			$queryparams = array($field['id'] => $display);
			foreach($this->fields as $value)
			{
				if($field['id'] == $value['id'] || !$value['canfilter']) continue;
				$queryparams[$value['id']] = false;
			}
			
			$display = '<a href="' . add_query_arg($queryparams, $url) . '">' . $display . '</a>';
		}
		
		echo $display;
	}
	
	public function findFieldByID($id)
	{
		if(!isset($this->fields[$id]))
		{
			foreach($this->fields as $field)
			{
				if($field['id'] == $id) return $field;
			}
			return false;
		}
		return $this->fields[$id];
	}
	
	public function findFieldByMetaID($metaid)
	{
		$id = substr($metaid, 1);
		return $this->findFieldByID($id);
	}
	
	public function create_cols($cols)
	{
		$newcols = array
		(
			'cb'				=> '<input type="checkbox" />',
			'title'				=> 'Title'
		);
		
		foreach($this->fields as $field)
		{
			if($field['showcolumn'])
				$newcols[$field['metaid']] = $field['name'];
		}
		
		$newcols['date'] = 'Date';
		return $newcols;
	}
	
	public function add_meta_boxes()
	{
		add_meta_box($this->posttype . '-options', $this->labels['singular_name'] . ' Info', array($this, 'display_info_meta_box'), $this->posttype, 'advanced', 'high');
	}
	
	public function addField($id, $name, $desc, $type, $showcolumn=false, $canfilter=false, $cansort=false, $orderby="meta_value", $data = array())
	{
		$this->fields[$id] = array(
			'id'			=> $this->posttype . '_' . $id,
			'metaid'		=> '_' . $this->posttype . '_' . $id,
			'name'			=> $name,
			'desc'			=> $desc,
			'type'			=> $type,
			'showcolumn'	=> $showcolumn,
			'canfilter'		=> $canfilter,
			'sort'			=> $cansort,
			'orderby'		=> $orderby,
			'data'			=> $data
		);
	}
	
	function save_meta_box($pid)
	{
		if(!current_user_can('edit_page', $pid) || $_POST['post_type'] != $this->posttype) return;

		if(!isset($_POST[$this->posttype . '_input_meta']) || !wp_verify_nonce($_POST[$this->posttype . '_input_meta'], plugin_basename( __FILE__ ))) return;
		
		foreach($this->fields as $field)
		{
			$id = $field['id'];
			update_post_meta($pid, $field['metaid'], sanitize_text_field($_POST[$id]));
		}
	}
	
	function display_info_meta_box($post)
	{
		wp_nonce_field(plugin_basename( __FILE__ ), $this->posttype . '_input_meta');
	?>
	<table class="form-table">
	<?php
		foreach($this->fields as $field)
		{
			$id = $field['id'];
			$content = get_post_meta($post->ID, $field['metaid'], true);
?>
		<tr>
			<th style="width:25%">
				<label for="<?php echo $id; ?>">
					<strong><?php echo $field['name']; ?></strong>
					<span style="display:block; color:#999; margin:5px 0 0 0; line-height: 18px;">
						<?php echo $field['desc']; ?>
					</span>
				</label>
			</th>
			<td>
<?php
			switch($field['type'])
			{
				case 'textbox':
				{
					echo '<input type="text" id="' . $id . '" name="' . $id . '" value="' . esc_attr($content) . '">';
					break;
				}
				case 'select':
				{
					if(is_array($content)) $content = $content[0];
					echo '<select id="' . $id . '" name="' . $id . '">';
					foreach($field['data']['options'] as $key => $value)
					{
						echo '<option value="' . $key . '" ' . selected($content, $key) . '>' . $value . '</option>';
					}
					echo '</select>';
					break;
				}
				case 'checkbox':
				{
					echo '<input type="checkbox" id="' . $id . '" name="' . $id . '" value="yes" ' . checked(strstr(is_array($content) ? implode($content) : $content, 'yes') !== false, true, false) . '>';
					break;
				}
			}
		
?>
			</td>
		</tr>
<?php
		}
?>
	</table>
<?php
	}
	
	function create_post_type()
	{
		register_post_type($this->posttype, array(
			'labels'	=> $this->labels,
			'public' => true,
			'supports' => $this->supports
		));
	}
}
?>