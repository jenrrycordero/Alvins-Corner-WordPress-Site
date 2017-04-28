<?php
require_once(WP_CONTENT_DIR . '/helpers/razz-plugin-options/admin-page-class.php');

$importers = array(
	new RentcafeImporter(),
	new PropertySolutionsImporter(),
	new OnSiteImporter(),
	new JSONImporter()
);

$config = array(    
	'menu'				=> 'settings',             //sub page to settings page
	'page_title'		=> 'Apartment',       //The name of this page 
	'capability'		=> 'manage_options',         // The capability needed to view the page 
	'option_group'		=> 'razz_apt_opt',       //the name of the option to create in the database
	'id'				=> 'razz_apt_id',            // meta box id, unique per page
	'google_fonts'		=> true,					// Use google fonts
	'fields'			=> array(),            // list of fields (can be added by field arrays)
	'local_images'		=> false,          // Use local or hosted images (meta box images for add/remove)
	'use_with_theme'	=> false
);

$settings = get_option('razz_apt_opt');

$options_panel = new BF_Admin_Page_Class($config);
$options_panel->OpenTabs_container('');

$options_panel->TabsListing(array(
	'links' => array(
		'options_main' => 'Main',
		'options_widget' => 'Widget',
		'options_text' => 'Text Overrides',
		'options_ibldg' => 'Building View',
		'options_floor_view' => 'Floor View',
		'options_models_view' => 'Models View',
		'options_unit_view' => 'Unit/Model View',
		'options_qs' => 'Quick Search',
		'options_pdf' => 'PDF Settings',
//		'options_rentcafe' => 'Rent Cafe',
		'options_importtool' => 'Import Tool',
		'options_import_export' => 'Import/Export'
	)
));
$options_panel->OpenTab('options_main');
$options_panel->Title('Main Settings');
$options_panel->addImage('img_loader', array('name'=> 'Loader Image'));
$options_panel->addRadio('data_sqft_source', array('unit' => 'Unit', 'model' => 'Model'), array('name'=> 'Square Footage Source', 'std'=> array('unit')));
$options_panel->addRadio('data_price_decimals', array('0' => 'False', '2' => 'True'), array('name'=> 'Display Cents in Price', 'std'=> array('2')));
$options_panel->addText('data_fixed_header_height', array('name'=> 'Fixed Header Height', 'std'=> ''));
$options_panel->addText('data_price_override', array('name'=> 'Price Override', 'std'=> '', 'desc' => 'If not empty, will display this message instead of unit price.'));
$options_panel->CloseTab();

//var_dump($settings);

$options_panel->OpenTab('options_widget');
$options_panel->Subtitle('Settings');
$options_panel->addParagraph("<div class=\"desc-field\">If hosted on an external site, the following needs to be added to their .htaccess file if they wish the widget hash urls to be scrapable by Facebook, Google, etc..<pre>&lt;IfModule mod_rewrite.c&gt;\n\tRewriteEngine On\n\tRewriteCond %{QUERY_STRING} (_escaped_fragment_=(?:/|%2F)floor-plans.*) [NC]\n\tRewriteRule ^(.*)$ " . home_url() . "?%1 [L,NE,R=302]\n&lt;/IfModule&gt;</pre></div>");
$options_panel->addRadio('widget_home', array('nothing' => 'Nothing', 'floors' => 'Floors', 'models' => 'Models', 'search' => 'Search'), array('name'=> 'Widget Homepage', 'std'=> array(($settings['widget_usebldg'] === 'false') ? 'nothing' : 'floors')));
$options_panel->addText('widget_pathname', array('name'=> 'Path Name', 'std'=> '', 'desc' => 'Path after domain the hash-urls are accepted. Leave blank for all. Ex. "/", "/floor-plans/", "/floorplans.php"'));
$options_panel->addText('widget_description', array('name'=> 'Description', 'std'=> '', 'desc' => 'Description to use for search engines.'));

$options_panel->Subtitle('Main Menu');
$options_panel->addCheckbox('widget_hide_floors', array('name'=> 'Hide Floors', 'std' => false));
$options_panel->addCheckbox('widget_hide_models', array('name'=> 'Hide Models', 'std' => false));
$options_panel->addCheckbox('widget_hide_search', array('name'=> 'Hide Search', 'std' => false));

$options_panel->Subtitle('Animation');
$options_panel->addText('widget_animate_in', array('name'=> 'Animate In', 'std'=> 'fadeInDownBig', 'desc' => 'Enter animation name from <a href="http://daneden.github.io/animate.css/" target="_blank">Animate.css</a> or blank for no animation.'));
$options_panel->addText('widget_animate_out', array('name'=> 'Animate Out', 'std'=> 'fadeOutUpBig', 'desc' => 'Enter animation name from <a href="http://daneden.github.io/animate.css/" target="_blank">Animate.css</a> or blank for no animation.'));
$options_panel->addText('widget_transition_in', array('name'=> 'Transition In', 'std'=> 'fadeIn', 'desc' => 'Enter animation name from <a href="http://daneden.github.io/animate.css/" target="_blank">Animate.css</a> or blank for no animation.'));
$options_panel->addText('widget_transition_out', array('name'=> 'Transition Out', 'std'=> 'fadeOut', 'desc' => 'Enter animation name from <a href="http://daneden.github.io/animate.css/" target="_blank">Animate.css</a> or blank for no animation.'));

$options_panel->Subtitle('Host Remotely');
$options_panel->addText('widget_external_url', array('name'=> 'Primary External URL', 'std'=> '', 'desc' => 'Leave blank if not external site. This url will be used for building urls back to the external site (Ex. The url Facebook will share)'));
$options_panel->addText('widget_allow_origin', array('name'=> 'Allowed Origin', 'std'=> '', 'desc' => 'Leave blank if widget not being used on a remote site. Otherwise enter the external domain or * for all (Potentially Dangerous)'));

$options_panel->Subtitle('General');
$options_panel->addTypo('lb_mainfont',array('name' => 'Main Font', 'std' => array('size' => '14px', 'color' => '#fff', 'face' => 'lato', 'weight' => '700')));
$options_panel->addColor('lb_bgcolor', array('name' => 'Background Color', 'std' => 'rgba(0,0,0,0.8)'));
$options_panel->addColor('lb_fgcolor', array('name' => 'Text Color', 'std' => '#fff'));
$options_panel->addColor('lb_arrow_color', array('name' => 'Arrow Color', 'std' => '#fff'));
$options_panel->addColor('lb_arrow_bordercolor', array('name' => 'Arrow Border Color', 'std' => '#000'));

$options_panel->Subtitle('Header');
$options_panel->addColor('lb_header_bgcolor', array('name' => 'Background Color', 'std' => '#000'));
$options_panel->addColor('lb_header_close_color', array('name' => 'Close Button Color', 'std' => '#fff'));
$options_panel->addColor('lb_header_close_color_hover', array('name' => 'Close Button Hover Color', 'std' => '#cecece'));
$options_panel->addColor('lb_header_bread_active', array('name' => 'Active Breadcrumb Color', 'std' => '#fff'));
$options_panel->addColor('lb_header_bread_link', array('name' => 'Link Breadcrumb Color', 'std' => '#9f9f9f'));
$options_panel->addColor('lb_header_bread_link_hover', array('name' => 'Link Breadcrumb Hover Color', 'std' => '#6C6C6C'));
$options_panel->addColor('lb_header_bread_sep', array('name' => 'Breadcrumb Separator Color (>)', 'std' => '#fff'));
$options_panel->addColor('lb_header_bread_floors_btn_color', array('name' => 'Floor Dropdown Button Color', 'std' => '#9f9f9f'));
$options_panel->addColor('lb_header_bread_floors_btn_color_hover', array('name' => 'Floor Dropdown Button Hover Color', 'std' => '#6C6C6C'));
$options_panel->addColor('lb_header_bread_floors_fgcolor', array('name' => 'Floor Dropdown Text Color', 'std' => '#000'));
$options_panel->addColor('lb_header_bread_floors_fgcolor_hover', array('name' => 'Floor Dropdown Text Hover Color', 'std' => '#000'));
$options_panel->addColor('lb_header_bread_floors_bgcolor', array('name' => 'Floor Dropdown Background Color', 'std' => '#fff'));
$options_panel->addColor('lb_header_bread_floors_bgcolor_hover', array('name' => 'Floor Dropdown Background Hover Color', 'std' => '#0088cc'));

$options_panel->Subtitle('Footer No-Hover');
$options_panel->addColor('lb_footer_bg_color', array('name' => 'Background Color', 'std' => '#cecece'));
$options_panel->addTypo('lb_footer_font', array('name' => 'Font', 'std' => array('size' => '14px', 'color' => '#fff', 'face' => 'lato', 'weight' => '700')));
$options_panel->addColor('lb_footer_a', array('name' => 'Link Color', 'std' => '#cecece'));

$options_panel->Subtitle('Footer Hover');
$options_panel->addColor('lb_footer_hover_bg_color', array('name' => 'Background Color', 'std' => '#cecece'));
$options_panel->addColor('lb_footer_hover_font', array('name' => 'Font Color', 'std' => '#cecece'));
$options_panel->addColor('lb_footer_hover_a', array('name' => 'Link Color', 'std' => '#cecece'));
$options_panel->addColor('lb_footer_hover_a_hover', array('name' => 'Link Hover Color', 'std' => '#cecece'));
$options_panel->CloseTab();

$options_panel->OpenTab('options_text');
$options_panel->Title('Text Overrides');
$options_panel->addText('txt_search', array('name'=> 'Search', 'std'=> '', 'desc' => 'Override "Search" on Lightbox pages.'));
$options_panel->addText('uv_applynow_label', array('name'=> 'Apply Now Label', 'std'=> '', 'desc'=>'Use this text to override the default text of the label Apply Now on the model detail page'));
$options_panel->CloseTab();


$options_panel->OpenTab('options_ibldg');
$options_panel->Title('Building View');
$options_panel->addImage('qs_ibldg_img', array('name'=> 'Building Image', 'preview_height' => '120px', 'preview_width' => '440px'));
$options_panel->addCheckbox('ibldg_img_retina', array('name'=> 'Retina', 'std' => true));
$options_panel->addRadio('ibldg_link', array('floor' => 'Floor', 'search' => 'Search'), array('name'=> 'Link Target', 'std'=> array('floor')));
$options_panel->addColor('qs_ibldg_floorhovercolor', array('name' => 'Floor Hover Color', 'std' => 'rgba(0,0,0,0.4)'));
$options_panel->Subtitle('Container Settings');
$options_panel->addText('qs_ibldg_width', array('name'=> 'Width', 'std'=> '100%'));
$options_panel->addText('qs_ibldg_height', array('name'=> 'Height', 'std'=> '600px', 'desc' => 'No longer used.'));
$options_panel->addText('ibldg_fadeout_distance', array('name'=> 'Fade Distance', 'std'=> '20'));
$options_panel->addColor('ibldg_fadeout_from', array('name' => 'Fade From', 'std' => 'rgba(255, 255, 255, 0)'));
$options_panel->addColor('ibldg_fadeout_to', array('name' => 'Fade To', 'std' => 'rgba(255, 255, 255, 1)'));
$options_panel->Subtitle('Tooltip Settings');
$options_panel->addCheckbox('ibldg_tooltip_enabled', array('name'=> 'Enabled', 'std' => true));
$options_panel->addSelect('ibldg_tooltip_direction', array('down'=>'Above','up'=>'Below','right'=>'Left','left'=>'Right'), array('name'=> 'Position', 'std' => 'down'));
$options_panel->addColor('ibldg_tooltip_bgcolor', array('name' => 'Background Color', 'std' => '#000'));
$options_panel->addColor('ibldg_tooltip_fgcolor', array('name' => 'Font Color', 'std' => '#fff'));
$options_panel->addColor('ibldg_tooltip_bordercolor', array('name' => 'Border Color', 'std' => '#222'));
$options_panel->addText('ibldg_tooltip_borderwidth', array('name'=> 'Border Width', 'std'=> '2'));
$options_panel->Subtitle('Info Bar Settings');
$options_panel->addCheckbox('ibldg_infobar_enabled', array('name'=> 'Enabled', 'std' => true));
$options_panel->addCheckbox('ibldg_infobar_floors_enabled', array('name'=> 'Show Floors', 'std' => true));
$options_panel->addColor('qs_ibldg_infobar_bgcolor', array('name' => 'Main Background Color', 'std' => 'rgba(0, 0, 0, 0.8)'));
$options_panel->addColor('qs_ibldg_infobar_fgcolor', array('name' => 'Main Font Color', 'std' => '#fff'));
$options_panel->addColor('qs_ibldg_infobar_block1_bgcolor', array('name' => 'Block 1 Background Color', 'std' => '#868c36'));
$options_panel->addColor('qs_ibldg_infobar_block1_fgcolor', array('name' => 'Block 1 Font Color', 'std' => '#fff'));
$options_panel->addText('ibldg_infobar_block1_text', array('name'=> 'Block 1 Text', 'std'=> 'SELECT A FLOOR TO SEE AVAILABLE APARTMENTS AND MORE INFO.'));
$options_panel->addColor('qs_ibldg_infobar_floorbtn_bgcolor', array('name' => 'Floor Button Background Color', 'std' => 'rgba(0, 0, 0, 0.5)'));
$options_panel->addColor('qs_ibldg_infobar_floorbtn_fgcolor', array('name' => 'Floor Button Font Color', 'std' => '#fff'));
$options_panel->addColor('qs_ibldg_infobar_floorbtn_bgcolor_hover', array('name' => 'Floor Button Hover Background Color', 'std' => '#2c697c'));
$options_panel->addColor('qs_ibldg_infobar_floorbtn_fgcolor_hover', array('name' => 'Floor Button Hover Font Color', 'std' => '#fff'));
$options_panel->addText('qs_ibldg_infobar_offset', array('name'=> 'Top Offset', 'std'=> '0', 'desc' => 'Offset in pixels from top to start info bar.'));
$options_panel->CloseTab();

$options_panel->OpenTab('options_floor_view');
$options_panel->Title('Floor View');

$options_panel->addCheckbox('fv_apts_startoff', array('name'=> 'Start with no unit selected.', 'std' => false));

$options_panel->Subtitle('Position');
$options_panel->addText('fv_pos_top', array('name'=> 'Top', 'std'=> '0', 'desc' => 'Pixels from top to start info bar.', 'validate' => array('numeric' => array('message' => 'Must be numeric.'))));
$options_panel->addCheckbox('fv_nospace', array('name'=> 'Remove image spacing', 'std' => false, 'desc' => 'Used for Alta At The Estate'));
$options_panel->addCheckbox('fv_valign', array('name'=> 'Vertically Align Top', 'std' => false, 'desc' => ''));

$options_panel->Subtitle('Block 1');
$options_panel->addCheckbox('fv_block1_show', array('name'=> 'Show', 'std' => true));
$options_panel->addColor('fv_block1_bgcolor', array('name' => 'Background Color', 'std' => 'rgba(0, 0, 0, 0.8)'));
$options_panel->addColor('fv_block1_fgcolor', array('name' => 'Font Color', 'std' => '#ffffff'));

$options_panel->Subtitle('Block 2');
$options_panel->addColor('fv_block2_bgcolor', array('name' => 'Background Color', 'std' => '#868c36'));
$options_panel->addColor('fv_block2_fgcolor', array('name' => 'Font Color', 'std' => '#ffffff'));

$options_panel->Subtitle('Block 3');
$options_panel->addColor('fv_block3_bgcolor', array('name' => 'Background Color', 'std' => '#1e4c5b'));
$options_panel->addColor('fv_block3_fgcolor', array('name' => 'Font Color', 'std' => '#ffffff'));

$options_panel->Subtitle('Block 4');
$options_panel->addColor('fv_block4_bgcolor', array('name' => 'Background Color', 'std' => '#163740'));
$options_panel->addColor('fv_block4_fgcolor', array('name' => 'Font Color', 'std' => '#ffffff'));

$options_panel->Subtitle('Block 5');
$options_panel->addColor('fv_block5_bgcolor', array('name' => 'Background Color', 'std' => '#163740'));
$options_panel->addColor('fv_block5_titlefgcolor', array('name' => 'Title Font Color', 'std' => '#bdbbbc'));
$options_panel->addColor('fv_block5_datafgcolor', array('name' => 'Data Font Color', 'std' => '#ffffff'));

$options_panel->Subtitle('Apartment Dots');
$options_panel->addText('fv_dot_radius', array('name' => 'Inner Radius', 'std' => '6', 'desc' => 'Default: 6', 'validate' => array('numeric' => array('params' => '', 'message' => 'Must be numeric.'))));
$options_panel->addText('fv_dot_stroke', array('name' => 'Stroke Width', 'std' => '3', 'desc' => 'Default: 3', 'validate' => array('numeric' => array('params' => '', 'message' => 'Must be numeric.'))));
$options_panel->addColor('fv_dot_available_inner', array('name' => 'Available Inner Color', 'std' => '#696b2c'));
$options_panel->addColor('fv_dot_available_outer', array('name' => 'Available Stroke Color', 'std' => '#bbc263'));
$options_panel->addColor('fv_dot_selected_inner', array('name' => 'Selected Inner Color', 'std' => '#2c697c'));
$options_panel->addColor('fv_dot_selected_outer', array('name' => 'Selected Stroke Color', 'std' => '#89bdd2'));

$options_panel->Subtitle('Amenity Dots');
$options_panel->addText('fv_amenity_dot_radius', array('name' => 'Inner Radius', 'std' => '6', 'desc' => 'Default: 6', 'validate' => array('numeric' => array('params' => '', 'message' => 'Must be numeric.'))));
$options_panel->addText('fv_amenity_dot_stroke', array('name' => 'Stroke Width', 'std' => '3', 'desc' => 'Default: 3', 'validate' => array('numeric' => array('params' => '', 'message' => 'Must be numeric.'))));
$options_panel->addColor('fv_amenity_dot_available_inner', array('name' => 'Available Inner Color', 'std' => '#696b2c'));
$options_panel->addColor('fv_amenity_dot_available_outer', array('name' => 'Available Stroke Color', 'std' => '#bbc263'));
$options_panel->addColor('fv_amenity_dot_selected_inner', array('name' => 'Selected Inner Color', 'std' => '#2c697c'));
$options_panel->addColor('fv_amenity_dot_selected_outer', array('name' => 'Selected Stroke Color', 'std' => '#89bdd2'));

$options_panel->CloseTab();


$options_panel->OpenTab('options_models_view');
$options_panel->Title('Models View');
$options_panel->addText('lb_models_terms', array('name'=> 'Tags to track', 'desc' => 'Space separated list of model tags to track.', 'std'=> 'studio 1-bedroom 2-bedroom 3-bedroom'));
$options_panel->addText('lb_models_layout_mode', array('name'=> 'Layout Mode', 'desc' => 'Isotope Layout Mode (fitRows, fitCols, masonry, etc)', 'std'=> 'fitRows'));
$options_panel->addColor('lb_models_item_bgcolor', array('name' => 'Item Background Color', 'std' => '#ffffff'));
$options_panel->addColor('lb_models_filters_bgcolor', array('name' => 'Filters Background Color', 'std' => '#ffffff'));
$options_panel->addTypo('lb_models_filters_font',array('name' => 'Filters Font', 'std' => array('size' => '30px', 'color' => '#db927c', 'face' => 'open_sans', 'variation' => '700')));
$options_panel->addColor('lb_models_details_header_bgcolor', array('name' => 'Details Header Background Color', 'std' => '#ffffff'));
$options_panel->addTypo('lb_models_details_header_font',array('name' => 'Details Header Font', 'std' => array('size' => '14px', 'color' => '#db927c', 'face' => 'open_sans', 'variation' => '400')));
$options_panel->addColor('lb_models_details_footer_bgcolor', array('name' => 'Details Footer Background Color', 'std' => '#db927c'));
$options_panel->addTypo('lb_models_details_footer_font',array('name' => 'Details Footer Font', 'std' => array('size' => '14px', 'color' => '#ffffff', 'face' => 'open_sans', 'variation' => '400')));
$options_panel->CloseTab();

$options_panel->OpenTab('options_unit_view');
$options_panel->Title('Unit View');
$options_panel->addText('uv_applyurl', array('name'=> 'Apply Online URL', 'std'=> '', 'desc'=>'Ex. https://therialtoapartments.securecafe.com/onlineleasing/the-rialto/oleapplication.aspx?stepname=RentalOptions&myOlePropertyId=%propid%&FloorPlanID=%fpid%&UnitID=%aptid%&MoveInDate=%adate%&header=1'));
$options_panel->addText('uv_contacturl', array('name'=> 'Override Contact Us URL', 'std'=> '', 'desc'=>'Enter a url to replace the default contact url, otherwise leave blank for default.'));
$options_panel->addText('uv_leasingterm', array('name'=> 'Leasing Term', 'std'=> '12 Months'));
$options_panel->Subtitle('Lightbox');
    $options_panel->addTypo('uvlb_header_text', array('name' => 'Header Font', 'std' => array('size' => '14px', 'color' => '#fff', 'face' => 'lato', 'variation' => '700')));
	$lbfields = array();
	$lbfields[] = $options_panel->addColor('uvlb_subheader_fgcolor', array('name' => 'Sub-Header Color', 'std' => '#fff'), true);
    $lbfields[] = $options_panel->addColor('uvlb_table_header', array('name' => 'Data Table Header Color', 'std' => '#fff'), true);
    $lbfields[] = $options_panel->addColor('uvlb_table_value', array('name' => 'Data Table Value Color', 'std' => '#fff'), true);
    $lbfields[] = $options_panel->addColor('uvlb_a', array('name' => 'Link Color', 'std' => '#fff'), true);
    $lbfields[] = $options_panel->addColor('uvlb_a_hover', array('name' => 'Link Hover Color', 'std' => '#fff'), true);
	$lbfields[] = $options_panel->addColor('uvlb_inforow_bgcolor', array('name' => 'Info Row Background Color', 'std' => '#5e717f'), true);
	$lbfields[] = $options_panel->addColor('uvlb_inforow_fgcolor', array('name' => 'Info Row Text Color', 'std' => '#fff'), true);
	$lbfields[] = $options_panel->addColor('uvlb_image_bordercolor', array('name' => 'Image Border Color', 'std' => '#d9d9d9'), true);
	$lbfields[] = $options_panel->addColor('uvlb_link_bgcolor', array('name' => 'Link Background Color', 'std' => '#3b4a56'), true);
	$lbfields[] = $options_panel->addColor('uvlb_link_fgcolor', array('name' => 'Link Text Color', 'std' => '#fff'), true);
	$lbfields[] = $options_panel->addColor('uvlb_link_bordercolor', array('name' => 'Link Border Color', 'std' => '#3b4a56'), true);
	$lbfields[] = $options_panel->addColor('uvlb_link_hover_bgcolor', array('name' => 'Button Link Hover Background Color', 'std' => '#6d8292'), true);
	$lbfields[] = $options_panel->addColor('uvlb_link_hover_fgcolor', array('name' => 'Button Link Hover Text Color', 'std' => '#fff'), true);
	$lbfields[] = $options_panel->addColor('uvlb_link_hover_bordercolor', array('name' => 'Button Link Hover Border Color', 'std' => '#fff'), true);
	$lbfields[] = $options_panel->addColor('uvlb_link_active_bgcolor', array('name' => 'Button Link Active Background Color', 'std' => '#6d8292'), true);
	$lbfields[] = $options_panel->addColor('uvlb_link_active_fgcolor', array('name' => 'Button Link Active Text Color', 'std' => '#fff'), true);
	$lbfields[] = $options_panel->addColor('uvlb_link_active_bordercolor', array('name' => 'Button Link Active Border Color', 'std' => '#fff'), true);
	$options_panel->addCondition('uv_lightbox', array('name' => 'Active', 'fields' => $lbfields, 'std' => false));
$options_panel->Subtitle('Email a Friend');
$options_panel->addParagraph('<div class="desc-field"><b>Available Shortcodes:</b><ul><li><em>[br]</em> - Line Break</li><li><em>[prop_name]</em> - Property Name</li><li><em>[title]</em> - Subject Title (ex. "Unit #123" or "Model A3")</li><li><em>[unit_num]</em> - Unit Number</li><li><em>[unit_beds]</em> - Bedrooms</li><li><em>[unit_baths]</em> - Bathrooms</li><li><em>[unit_sqft]</em> - Square Feet</li><li><em>[unit_url]</em> - URL to unit page</li></div>');
$options_panel->addText('uv_email_url_override', array('name'=> 'Base URL Override', 'std'=> '', 'desc'=>'Leave blank for default.'));
$options_panel->addText('uv_email_subject', array('name'=> 'Message Subject', 'std'=> 'Check out this apartment!'));
$options_panel->addText('uv_email_body', array('name'=> 'Message Body', 'std'=> 'Check out this floor plan for Apartment #[unit_num] from [prop_name].[br][br]Bedrooms: [unit_beds][br]Bathrooms: [unit_baths][br]Square Feet: [unit_sqft][br][br][unit_url]'));
$options_panel->CloseTab();


$options_panel->OpenTab('options_qs');
$options_panel->Title('Quick Search Settings');
$options_panel->Subtitle('Header Bar');
$options_panel->addColor('qs_headerbar_bgcolor', array('name' => 'Background Color', 'std' => '#868c36'));
$options_panel->addTypo('qs_headerbar_font',array('name' => 'Font', 'std' => array('size' => '16px', 'color' => '#FFF', 'face' => 'lato', 'weight' => '700')));
$options_panel->Subtitle('Subheader Bar');
$options_panel->addColor('qs_subheaderbar_bgcolor', array('name' => 'Background Color', 'std' => '#9f3618'));
$options_panel->addTypo('qs_subheaderbar_font',array('name' => 'Font', 'std' => array('size' => '16px', 'color' => '#FFF', 'face' => 'lato', 'weight' => '700')));
$options_panel->Subtitle('Table');
$options_panel->addTypo('qs_table_header_font',array('name' => 'Header Font', 'std' => array('size' => '14px', 'color' => '#B2B2B2', 'face' => 'lato', 'weight' => '700')));
$options_panel->addColor('qs_table_header_bg', array('name' => 'Header Background Color', 'std' => '#1E1E1E'));
$options_panel->addTypo('qs_table_body_font',array('name' => 'Body Font', 'std' => array('size' => '14px', 'color' => '#B2B2B2', 'face' => 'lato', 'weight' => '700')));
$options_panel->addColor('qs_table_body_bg', array('name' => 'Body Background Color', 'std' => '#0f0f0f'));
$options_panel->addColor('qs_table_body_bg_hover', array('name' => 'Body Background Hover Color', 'std' => '#424240'));

$options_panel->Subtitle('Pagination');
$options_panel->addColor('qs_table_page_bg', array('name' => 'Background Color', 'std' => '#1E1E1E'));
$options_panel->addColor('qs_table_page_bg_hover', array('name' => 'Background Hover Color', 'std' => '#1E1E1E'));
$options_panel->addColor('qs_table_page_bg_active', array('name' => 'Background Active Color', 'std' => '#1E1E1E'));
$options_panel->addColor('qs_table_page_bg_disabled', array('name' => 'Background Disabled Color', 'std' => '#1E1E1E'));
$options_panel->addTypo('qs_table_page_font',array('name' => 'Font', 'std' => array('size' => '14px', 'color' => '#fff', 'face' => 'lato', 'weight' => '400')));

$options_panel->Subtitle('Bed/Bath Filters');
$options_panel->addColor('qs_filter_bg', array('name' => 'Background Color', 'std' => '#1E1E1E'));
$options_panel->addTypo('qs_filter_font',array('name' => 'Font', 'std' => array('size' => '14px', 'color' => '#fff', 'face' => 'lato', 'weight' => '400')));
$options_panel->addColor('qs_filter_display_bg', array('name' => 'Display Circle Background', 'std' => '#1E1E1E'));
$options_panel->addTypo('qs_filter_display_font',array('name' => 'Display Circle Font', 'std' => array('size' => '28px', 'color' => '#fff', 'face' => 'lato', 'weight' => '400')));
$options_panel->addText('qs_filter_display_font_studio', array('name' => 'Display Circle Font Size when "Studio"', 'std' => '22'));
$options_panel->addTypo('qs_filter_btn_font',array('name' => 'Button Font', 'std' => array('size' => '15px', 'color' => '#fff', 'face' => 'lato', 'weight' => '400')));
$options_panel->addColor('qs_filter_btn_plus', array('name' => 'Plus Button Background', 'std' => '#2b697d'));
$options_panel->addColor('qs_filter_btn_plus_hover', array('name' => 'Plus Button Hover Background', 'std' => '#24515b'));
$options_panel->addColor('qs_filter_btn_minus', array('name' => 'Minus Button Background', 'std' => '#6d95a3'));
$options_panel->addColor('qs_filter_btn_minus_hover', array('name' => 'Minus Button Hover Background', 'std' => '#56747c'));
$options_panel->addColor('qs_filter_btn_disabled', array('name' => 'Disabled Button Background', 'std' => '#666666'));

$options_panel->Subtitle('Custom Filters');
$options_panel->addColor('qs_filter_custom_bg', array('name' => 'Background Color', 'std' => '#1E1E1E'));
$options_panel->addColor('qs_filter_custom_bg_on', array('name' => 'Background Color ON', 'std' => '#1E1E1E'));
$options_panel->addTypo('qs_filter_custom_font',array('name' => 'Font', 'std' => array('size' => '14px', 'color' => '#fff', 'face' => 'lato', 'weight' => '400')));


$options_panel->Subtitle('View Details Button');
$options_panel->addColor('qs_viewdetails_bg', array('name' => 'Background Color', 'std' => '#9e3614'));
$options_panel->addColor('qs_viewdetails_bgplus', array('name' => '+ Background Color', 'std' => '#832b11'));
$options_panel->addColor('qs_viewdetails_fg', array('name' => 'Font Color', 'std' => '#fff'));
$options_panel->CloseTab();

$options_panel->OpenTab('options_pdf');
$options_panel->Title('PDF Settings');
$options_panel->addImage('pdf_logo', array('name'=> 'Site Logo'));

$options_panel->Subtitle('Floorplan Image');
$options_panel->addCheckbox('pdf_grayscale', array('name'=> 'Convert to Grayscale', 'std' => false));


$options_panel->Subtitle('Model Bar');
$options_panel->addColor('pdf_bar_bg', array('name' => 'Background Color', 'std' => '#000'));
$options_panel->addColor('pdf_bar_fg', array('name' => 'Foreground Color', 'std' => '#fff'));

$options_panel->Subtitle('Footer');
$options_panel->addText('pdf_footer1', array('name' => 'Line 1', 'std' => ''));
$options_panel->addText('pdf_footer', array('name' => 'Line 2', 'std' => ''));

$options_panel->CloseTab();

$options_panel->OpenTab('options_rentcafe');
$options_panel->Title('Rentcafe Settings');

$options_panel->Subtitle('Identification');
$options_panel->addText('rentcafe_company_code', array('name'=> 'Company Code', 'std'=> ''));
$options_panel->addText('rentcafe_property_code', array('name'=> 'Property Code', 'std'=> ''));

$options_panel->Subtitle('Schedule');
$options_panel->addText('rentcafe_schedule_timestamp', array('name'=> 'First Run Timestamp (GMT)', 'std'=> '', 'desc' => '<a href="http://www.timestampgenerator.com/" target="_blank">[Timestamp Generator]</a>'));
$options_panel->addRadio('rentcafe_schedule_interval', array('cron_int_0' => 'Daily', 'cron_int_1' => 'Twice Daily', 'cron_int_2' => 'Hourly'), array('name'=> 'Interval', 'std'=> array('cron_int_0')));

$options_panel->Subtitle('Parsing');
$options_panel->addText('rentcafe_parsing_floor', array('name'=> 'Floor Regex', 'std'=> '', 'desc' => '<strong>Common Formats:</strong><br /><strong>1</strong>23 - <code>^([0-9])[0-9]{2}$</code><br />1-<strong>1</strong>23 - <code>^[0-9]-([0-9])[0-9]{2}$</code><br />1<strong>1</strong>23 - <code>^[0-9]([0-9])[0-9]{2}$</code>'));
$options_panel->CloseTab();

$options_panel->OpenTab('options_importtool');
$options_panel->Title('Import Settings');
$options_panel->addParagraph('<div class="desc-field">The following are the available import providers and their settings. For now, only one provider should be selected at a time.</div>');

$options_panel->Subtitle('Schedule');
$options_panel->addText('import_schedule_timestamp', array('name'=> 'First Run Timestamp (GMT)', 'std'=> '', 'desc' => '<a href="http://www.timestampgenerator.com/" target="_blank">[Timestamp Generator]</a>'));
$options_panel->addRadio('import_schedule_interval', array('cron_int_0' => 'Daily', 'cron_int_1' => 'Twice Daily', 'cron_int_2' => 'Hourly'), array('name'=> 'Interval', 'std'=> array('cron_int_0')));

foreach($importers as $importer)
{
	$importer->addSettings($options_panel);
}

$options_panel->CloseTab();


$options_panel->OpenTab('options_import_export');
$options_panel->Title('Import/Export');
$options_panel->addImportExport();
$options_panel->CloseTab();

function getAptSettings()
{
	return get_option('razz_apt_opt');
}

?>