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

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Localise\Administrator\Helper\LocaliseHelper;

/**
 * Form Field Client class
 *
 * @package     Joomla.Administrator
 * @subpackage  com_localise
 *
 * @since       1.0
 */
class TranslationsClientField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 */
	protected $type = 'TranslationsClient';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 */
	protected function getInput()
	{
		$attributes = '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attributes .= ' disabled="disabled"';
		}

		if ($v = (string) $this->element['onchange'])
		{
			$attributes .= ' onchange="' . $v . '"';
		}

		$attributes .= ' class="custom-select"';

		$options = array();

		foreach ($this->element->children() as $option)
		{
			$options[] = HTMLHelper::_('select.option', $option->attributes('value'), Text::_(trim($option)), array('option.attr' => 'attributes', 'attr' => ''));
		}

		$options[] = HTMLHelper::_('select.option', 'site', Text::_('COM_LOCALISE_OPTION_CLIENT_SITE'),
					array('option.attr' => 'attributes', 'attr' => '')
					);

		$options[] = HTMLHelper::_('select.option', 'administrator', Text::_('COM_LOCALISE_OPTION_CLIENT_ADMINISTRATOR'),
					array('option.attr' => 'attributes', 'attr' => '')
					);

		if (LocaliseHelper::hasInstallation())
		{
			$options[] = HTMLHelper::_('select.option', 'installation', Text::_('COM_LOCALISE_OPTION_CLIENT_INSTALLATION'),
						array('option.attr' => 'attributes', 'attr' => '')
						);
		}

		$return = array();

		if ((string) $this->element['readonly'] == 'true')
		{
			$return[] = HTMLHelper::_('select.genericlist', $options, '', array('id' => $this->id,
						'list.select' => $this->value, 'option.attr' => 'attributes', 'list.attr' => $attributes)
						);
			$return[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}
		else
		{
			$return[] = HTMLHelper::_('select.genericlist', $options, $this->name, array('id' => $this->id,
						'list.select' => $this->value, 'option.attr' => 'attributes', 'list.attr' => $attributes)
						);
		}

		return implode($return);
	}
}
