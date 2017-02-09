<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Sttestimonials
 * @author     Sopan Technologies <info@sopantech.com>
 * @copyright  2016 Sopan Technologies
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Sttestimonials', JPATH_COMPONENT);
JLoader::register('SttestimonialsController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = JControllerLegacy::getInstance('Sttestimonials');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
