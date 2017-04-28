<?php


class PopcardIntegrationPostType extends AdminPageFramework_PostType
{

    function __construct()
    {
        parent::__construct('popcard_integration');
    }

    public function setUp()
    {
        $this->setArguments(
            array(
                'labels' => array(
                    'name'               => 'Popcard Integrations',
                    'singular_name'      => 'Popcard Integration',
                    'menu_name'          => 'Popcard Integrations',
                    'name_admin_bar'     => 'Popcard Integration',
                    'add_new'            => 'Add New',
                    'add_new_item'       => 'Add New Popcard Integration',
                    'new_item'           => 'New Popcard Integration',
                    'edit_item'          => 'Edit Popcard Integration',
                    'view_item'          => 'View Popcard Integration',
                    'all_items'          => 'All Popcard Integrations',
                    'search_items'       => 'Search Popcard Integrations',
                    'parent_item_colon'  => 'Parent Popcard Integrations:',
                    'not_found'          => 'No Popcard Integrations Found',
                    'not_found_in_trash' => 'No Popcard Integrations Found in Trash'
                ),
                'supports'          => array('title'), // e.g. array( 'title', 'editor', 'comments', 'thumbnail', 'excerpt' ),
                'public'            => false,
                'show_ui'           => true,
                'menu_icon'         => 'dashicons-admin-generic'
            )
        );
    }
}