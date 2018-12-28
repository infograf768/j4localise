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

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Form\FormHelper;
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
class StateField extends ListField
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'State';

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

		$attributes .= ' class="' . (string) $this->element['class'] . ' iconlist-16-' . $this->value . ' ' . $this->value . '"';
		$options = array();

		foreach ($this->element->children() as $option)
		{
			$options[] = HTMLHelper::_('select.option', $option->attributes('value'), Text::_(trim($option)),
						array('option.attr' => 'attributes', 'attr' => 'class="localise-icon inlanguage"')
						);
		}

		$options[] = HTMLHelper::_('select.option', 'inlanguage', Text::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_STATE_INLANGUAGE'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-inlanguage inlanguage"')
						);
		$options[] = HTMLHelper::_('select.option', 'unexisting', Text::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_STATE_UNEXISTING'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-unexisting unexisting"')
						);
		$options[] = HTMLHelper::_('select.option', 'notinreference', Text::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_STATE_NOTINREFERENCE'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-notinreference notinreference"')
						);
		$options[] = HTMLHelper::_('select.option', 'error', Text::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_STATE_ERROR'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-error error"')
						);

		return $options;
	}
}
