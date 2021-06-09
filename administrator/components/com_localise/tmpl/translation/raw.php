<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('stylesheet', 'com_localise/localise.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');


$params            = ComponentHelper::getParams('com_localise');
$ref_tag           = $params->get('reference', 'en-GB');
$allow_develop     = $params->get('gh_allow_develop', 0);
$saved_ref         = $params->get('customisedref', 0);
$source_ref        = $saved_ref;
$istranslation     = $this->item->istranslation;
$installed_version = new Version;
$installed_version = $installed_version->getShortVersion();

	if ($saved_ref == 0)
	{
		$source_ref = $installed_version;
	}

	if ($saved_ref != 0 && $allow_develop == 1 && $ref_tag == 'en-GB' && $istranslation == 0)
	{
		Factory::getApplication()->enqueueMessage(
		Text::sprintf('COM_LOCALISE_NOTICE_EDIT_REFERENCE_HAS_LIMITED_USE', $source_ref),
		'notice');
	}

$fieldSets = $this->form->getFieldsets();
$ftpSets   = $this->formftp->getFieldsets();

/*Factory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if (task == 'translation.cancel' || document.formvalidator.isValid(document.getElementById('localise-translation-form')))
		{
			" . $this->form->getField('source')->save() . "
			Joomla.submitform(task, document.getElementById('localise-translation-form'));
		}
	}
");*/
?>
<form action="" method="post" name="adminForm" id="localise-translation-form" class="form-validate">
	<?php if ($this->ftp) : ?>
	<fieldset class="panelform">
		<legend><?php echo Text::_($ftpSets['ftp']->label); ?></legend>
		<?php if (!empty($ftpSets['ftp']->description)):?>
		<p class="tip"><?php echo Text::_($ftpSets['ftp']->description); ?></p>
		<?php endif;?>
		<?php if ($this->ftp instanceof Exception): ?>
		<p class="error"><?php echo Text::_($this->ftp->message); ?></p>
		<?php endif; ?>
		<ul class="adminformlist">
			<?php foreach($this->formftp->getFieldset('ftp',false) as $field): ?>
			<?php if ($field->hidden): ?>
			<?php echo $field->input; ?>
			<?php else:?>
			<li>
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
			</li>
			<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</fieldset>
	<?php endif; ?>
	<fieldset class="panelform">
		<legend><?php echo Text::_($fieldSets['source']->label); ?></legend>
		<?php if (isset($fieldSets['source']->description)):?>
		<p class="label"><?php echo Text::_($fieldSets['source']->description); ?></p>
		<?php endif;?>
		<div class="clr"></div>
		<div class="editor-border">
			<?php echo $this->form->getInput('source'); ?>
		</div>
	</fieldset>
	<input type="hidden" name="task" value="" />
	<?php echo \JHtml::_('form.token'); ?>
</form>
