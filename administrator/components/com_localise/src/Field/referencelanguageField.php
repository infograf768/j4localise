<?php
/**
 * @package     Com_Localise
 * @subpackage  models
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Localise\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\LanguageHelper;

FormHelper::loadFieldClass('list');
\JLoader::register('JFolder', JPATH_LIBRARIES . '/joomla/filesystem/folder.php');

jimport('joomla.filesystem.folder');
include_once JPATH_ADMINISTRATOR . '/components/com_localise/helper/defines.php';

/**
 * Renders a list of all possible languages (they must have a site, language and installation part)
 * Use instead of the joomla library languages element, which only lists languages for one client
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class ReferenceLanguageField extends ListField
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'ReferenceLanguage';

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getOptions()
	{
		$admin = LanguageHelper::getKnownLanguages(LOCALISEPATH_ADMINISTRATOR);
		$site  = LanguageHelper::getKnownLanguages(LOCALISEPATH_SITE);

		if (\JFolder::exists(LOCALISEPATH_INSTALLATION))
		{
			$installation = LanguageHelper::getKnownLanguages(LOCALISEPATH_INSTALLATION);
			$languages    = array_intersect_key($admin, $site, $installation);
		}
		else
		{
			$languages = array_intersect_key($admin, $site);
		}

		foreach ($languages as $i => $language)
		{
			$languages[$i] = ArrayHelper::toObject($language);
		}

		ArrayHelper::sortObjects($languages, 'name');

		$options = parent::getOptions();

		foreach ($languages as $language)
		{
			$options[] = \JHtml::_('select.option', $language->tag, $language->name);
		}

		return $options;
	}
}
