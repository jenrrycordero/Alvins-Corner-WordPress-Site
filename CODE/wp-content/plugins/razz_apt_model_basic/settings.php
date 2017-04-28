<?php
/**
 * Created by PhpStorm.
 * User: alian
 * Date: 04-27-2015
 * Time: 17:09
 */



if( function_exists('acf_add_options_page') )
{

    acf_add_options_page(array(
        'page_title' => 'Razz Apartment Models',
        'menu_title' => 'R.A.M. Options',
        'menu_slug'  => 'razz_apartment_models',
        'capability' => 'manage_options',
        'redirect'   => FALSE
    ));
}

