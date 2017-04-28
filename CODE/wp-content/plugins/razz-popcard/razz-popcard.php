<?php
/*
Plugin Name: Razz: Popcard Integration
Plugin URI: http://razzinteractive.com
Description: Links to Contact Form 7 Submissions to send results directly to PopCard.
Author: Razz Interactive
Version: 1.0
*/

if(!class_exists('AdminPageFramework'))
{
    include_once(dirname(__FILE__) . '../admin-page-framework/library/admin-page-framework.min.php');
}

require_once(__DIR__ . '/src/CustomPostTypes/PopcardIntegrationPostType.php');
require_once(__DIR__ . '/src/MetaBoxes/PopcardIntegrationPostMeta.php');
require_once(__DIR__ . '/src/Handler/PopcardIntegrationHandler.php');
require_once(__DIR__ . '/src/Handler/ContactFormSubmitHandler.php');
require_once(__DIR__ . '/src/Helpers/ContactForm7Helper.php');
//require_once(__DIR__ . '/src/Helpers/GravityFormsHelper.php');



new PopcardIntegrationPostType;
new PopcardIntegrationPostMeta;

new ContactFormSubmitHandler;

$helper_cf7 = ContactForm7Helper::getInstance();