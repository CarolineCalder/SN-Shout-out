<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Sttestimonials
 * @author     Sopan Technologies <info@sopantech.com>
 * @copyright  2016 Sopan Technologies
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Class SttestimonialsFrontendHelper
 *
 * @since  1.6
 */
class SttestimonialsHelpersSttestimonials
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_sttestimonials/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_sttestimonials/models/' . strtolower($name) . '.php';
			$model = JModelLegacy::getInstance($name, 'SttestimonialsModel');
		}

		return $model;
	}
}
