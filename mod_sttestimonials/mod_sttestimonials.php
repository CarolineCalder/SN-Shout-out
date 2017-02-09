<?php
/**
 * @copyright	Copyright © 2016 - All rights reserved.
 * @license		GNU General Public License v2.0
 */
defined('_JEXEC') or die;

$doc = JFactory::getDocument();

// Include assets
if($params->get('load_jquery', 0) == 1) {
    $doc->addScript("//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js");
}
$doc->addStyleSheet(JURI::root()."modules/mod_sttestimonials/assets/jquery.bxslider.css");
$doc->addScript(JURI::root()."modules/mod_sttestimonials/assets/jquery.bxslider.min.js");

$db = JFactory::getDBO();

$db->setQuery("SELECT * FROM #__sttestimonials_testimonials where state=1");

$objects = $db->loadAssocList();

require JModuleHelper::getLayoutPath('mod_sttestimonials', $params->get('layout', 'default'));