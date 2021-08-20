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
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

FormHelper::loadFieldClass('list');

/**
 * Form Field State class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class KeystatusField extends ListField
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Keystatus';

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getOptions()
	{
		$attributes = '';

		if ($v = (string) $this->element['onchange'])
		{
			$attributes .= ' onchange="' . $v . '"';
		}

		$attributes .= ' class="filter-select"';
		$options = array();

		foreach ($this->element->children() as $option)
		{
			$options[] = HTMLHelper::_('select.option', '', Text::_(trim($option)),
						array('option.attr' => 'attributes', 'attr' => 'class="filter-select"')
						);
		}

		$options[] = HTMLHelper::_('select.option', 'allkeys', Text::_('JALL'),
						array('option.attr' => 'attributes', 'attr' => 'class="allkeys"')
						);

		$options[] = HTMLHelper::_('select.option', 'translatedkeys', Text::_('COM_LOCALISE_TEXT_TRANSLATION_TRANSLATED'),
						array('option.attr' => 'attributes', 'attr' => 'class="translated"')
						);
		$options[] = HTMLHelper::_('select.option', 'untranslatedkeys', Text::_('COM_LOCALISE_TEXT_TRANSLATION_UNTRANSLATED'),
						array('option.attr' => 'attributes', 'attr' => 'class="untranslated"')
						);
		$options[] = HTMLHelper::_('select.option', 'unchangedkeys', Text::_('COM_LOCALISE_TEXT_TRANSLATION_UNCHANGED'),
						array('option.attr' => 'attributes', 'attr' => 'class="unchanged"')
						);
		$options[] = HTMLHelper::_('select.option', 'textchangedkeys', Text::_('COM_LOCALISE_TEXT_TRANSLATION_TEXTCHANGED'),
						array('option.attr' => 'attributes', 'attr' => 'class="textchanged"')
						);

		return $options;
	}
}
