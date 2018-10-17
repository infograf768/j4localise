<?php
/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Localise\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

/**
 * The Translator Model
 *
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @since       1.6
 */
class TranslatorModel extends BaseDatabaseModel
{
	/**
	 * todo: add function description
	 *
	 * @return string
	 */
	public function getText()
	{
		$params   = ComponentHelper::getParams('com_localise');
		$clientID = $params->get('clientID');
		$secret   = $params->get('client_secret');

		if (empty($clientID) || empty($secret))
		{
			$this->setError(\JText::_('COM_LOCALISE_MISSING_CLIENTID_SECRET'));

			return '';
		}

		$app  = Factory::getApplication();
		$text = $app->input->getHtml('text');

		if (empty($text))
		{
			$this->setError(\JText::_('COM_LOCALISE_MISSING_TEXT'));

			return '';
		}

		$to = $app->input->getCmd('to');

		if (empty($to))
		{
			$this->setError(\JText::_('COM_LOCALISE_MISSING_TO_LANGUAGECODE'));

			return '';
		}

		$from = $app->input->getCmd('from');

		class_exists('HTTPTranslator')
		or require dirname(__DIR__) . '/helpers/azuretranslator.php';

		$translator = new \HTTPTranslator;

		return $translator->translate($clientID, $secret, $to, $text, $from);
	}
}
