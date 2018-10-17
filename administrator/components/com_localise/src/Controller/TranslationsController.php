<?php
/**
 * @package     Com_Localise
 * @subpackage  controller
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Localise\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

/**
 * Translations Controller class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 * @since       1.0
 */
class TranslationsController extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $text_prefix  = 'COM_LOCALISE_TRANSLATIONS';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = '', $prefix = '', $config = array())
	{
		if (empty($name))
		{
			$name = 'Translation';
		}

		$config = array_merge($config, array('ignore_request' => true));

		return parent::getModel($name, $prefix, $config);
	}
}
