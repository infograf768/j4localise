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
 * Form Field Place class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class TypeField extends ListField
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Type';

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

		$attributes .= ' class="' . (string) $this->element['class'] . ' iconlist-16-' . $this->value . '"';
		$options = array();

		foreach ($this->element->children() as $option)
		{
			$options[] = HTMLHelper::_('select.option', $option->attributes('value'), Text::_(trim($option)), array('option.attr' => 'attributes', 'attr' => ''));
		}

		$options[] = HTMLHelper::_('select.option', 'component', Text::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_COMPONENT'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-component"')
						);
		$options[] = HTMLHelper::_('select.option', 'module', Text::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_MODULE'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-module"')
						);
		$options[] = HTMLHelper::_('select.option', 'plugin', Text::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_PLUGIN'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-plugin"')
						);
		$options[] = HTMLHelper::_('select.option', 'template', Text::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_TEMPLATE'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-template"')
						);
		$options[] = HTMLHelper::_('select.option', 'package', Text::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_PACKAGE'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-package"')
						);
		$options[] = HTMLHelper::_('select.option', 'library', Text::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_LIBRARY'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-library"')
						);
		$options[] = HTMLHelper::_('select.option', 'file', Text::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_FILE'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-file"')
						);
		$options[] = HTMLHelper::_('select.option', 'joomla', Text::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_JOOMLA'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-joomla"')
						);
		$options[] = HTMLHelper::_('select.option', 'override', Text::sprintf('COM_LOCALISE_OPTION_TRANSLATIONS_TYPE_OVERRIDE'),
						array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-override"')
						);

		return $options;
	}
}
