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

use Joomla\CMS\Form\FormHelper;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Factory;

\JLoader::register('JFolder', JPATH_LIBRARIES . '/joomla/filesystem/folder.php');
include_once JPATH_ADMINISTRATOR . '/components/com_localise/helper/defines.php';

jimport('joomla.filesystem.folder');
FormHelper::loadFieldClass('list');

/**
 * Renders a list of all languages
 * Use instead of the joomla library languages element, which only lists languages for one client
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class LanguageField extends ListField
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Language';

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getOptions()
	{
		$app  = Factory::getApplication();
		$state = $app->getUserState('com_localise.select');
		$attributes = '';

		if ($v = (string) $this->element['onchange'])
		{
			$attributes .= ' onchange="' . $v . '"';
		}

		$params    = ComponentHelper::getParams('com_localise');
		$reference = $params->get('reference', 'en-GB');
		$admin     = LanguageHelper::getKnownLanguages(LOCALISEPATH_ADMINISTRATOR);
		$site      = LanguageHelper::getKnownLanguages(LOCALISEPATH_SITE);

		if (\JFolder::exists(LOCALISEPATH_INSTALLATION))
		{
			$install = LanguageHelper::getKnownLanguages(LOCALISEPATH_INSTALLATION);
		}
		else
		{
			$install = array();
		}

		// Listing languages in the language filter depending on the client filter
		if ($state['client'] == 'installation')
		{
			$languages = $install;
		}
		elseif ($state['client'] == 'administrator')
		{
			$languages = $admin;
		}
		elseif ($state['client'] == 'site')
		{
			$languages = $site;
		}
		else
		{
			$languages  = array_merge($admin, $site, $install);
		}

		$attributes .= ' class="' . (string) $this->element['class'] . ($this->value == $reference ? ' iconlist-16-reference"' : '"');

		foreach ($languages as $i => $language)
		{
			$languages[$i] = ArrayHelper::toObject($language);
		}

		ArrayHelper::sortObjects($languages, 'name');
		$options = array();

		foreach ($this->element->children() as $option)
		{
			$options[] = \JHtml::_('select.option', $option->attributes('value'), \JText::_(trim($option)), array('option.attr' => 'attributes', 'attr' => ''));
		}

		foreach ($languages as $language)
		{
			$options[] = \JHtml::_(
				'select.option',
				$language->tag,
				$language->name,
				array(
					'option.attr' => 'attributes',
					'attr' => 'class="' . ($language->tag == $reference ? 'iconlist-16-reference" title="'
						. \JText::_('COM_LOCALISE_TOOLTIP_FIELD_LANGUAGE_REFERENCE') . '"' : '"'
					)
				)
			);
		}

		return $options;
	}
}
