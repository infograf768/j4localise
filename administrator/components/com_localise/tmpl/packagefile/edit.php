<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');

$fieldSets = $this->form->getFieldsets();
$ftpSets   = $this->formftp->getFieldsets();
Text::script('COM_LOCALISE_MSG_CONFIRM_PACKAGE_SAVE');

Factory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(task)
	{
		if ((task == 'packagefile.apply' || task == 'packagefile.save') && document.formvalidator.isValid(document.getElementById('localise-package-form')))
		{
			if (confirm(Joomla.JText._('COM_LOCALISE_MSG_CONFIRM_PACKAGE_SAVE')))
			{
				Joomla.submitform(task, document.getElementById('localise-package-form'));
			}
		}
		else if (task == 'packagefile.cancel' || task == 'packagefile.download')
		{
			Joomla.submitform(task, document.getElementById('localise-package-form'));
		}
	}
");
?>
<form action="<?php echo Route::_('index.php?option=com_localise&view=packagefile&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="localise-package-form" class="form-validate">
	<div class="row-fluid">
		<!-- Begin Localise Package -->
		<div class="col-md-12">
				<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => $this->ftp ? 'ftp' : 'default')); ?>
					<?php if ($this->ftp) : ?>
					<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'ftp', Text::_($ftpSets['ftp']->label, true)); ?>
						<?php if (!empty($ftpSets['ftp']->description)):?>
								<p class="tip"><?php echo Text::_($ftpSets['ftp']->description); ?></p>
						<?php endif;?>
						<?php if ($this->ftp instanceof Exception): ?>
								<p class="error"><?php echo Text::_($this->ftp->message); ?></p>
						<?php endif; ?>
						<?php foreach($this->formftp->getFieldset('ftp',false) as $field): ?>
								<div class="control-group">
									<div class="control-label">
										<?php echo $field->label; ?>
									</div>
									<div class="controls">
										<?php echo $field->input; ?>
									</div>
								</div>
						<?php endforeach; ?>
						<?php echo HTMLHelper::_('uitab.endTab'); ?>
					<?php endif; ?>
					<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'default', Text::_($fieldSets['default']->label, true)); ?>
						<div class="row">
						<div class="col-lg-12 col-xl-6">
							<fieldset id="fieldset-default" class="options-form">
								<?php if (!empty($fieldSets['default']->description)):?>
									<legend><?php echo Text::_($fieldSets['default']->description); ?></legend>
								<?php endif;?>
								<?php foreach($this->form->getFieldset('default') as $field): ?>
									<?php echo $field->renderField(); ?>
								<?php endforeach; ?>
							</fieldset>
						</div>
						<div class="col-lg-12 col-xl-6">
							<fieldset id="fieldset-translations" class="options-form">
							 	<legend><?php echo Text::_($fieldSets['translations']->label); ?></legend>
								<?php if (!empty($fieldSets['translations']->description)):?>
									<legend><?php echo Text::_($fieldSets['translations']->description); ?></legend>
								<?php endif;?>
								<?php foreach($this->form->getFieldset('translations') as $field): ?>
									<?php echo $field->renderField(); ?>
								<?php endforeach; ?>
							</fieldset>
						</div>
						</div>
					<?php echo HTMLHelper::_('uitab.endTab'); ?>
					<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_($fieldSets['permissions']->label, true)); ?>
						<?php if (!empty($fieldSets['permissions']->description)):?>
								<p class="tip"><?php echo Text::_($fieldSets['permissions']->description); ?></p>
						<?php endif;?>
						<?php foreach($this->form->getFieldset('permissions') as $field): ?>
								<div class="control-group form-vertical">
									<div class="controls">
										<?php echo $field->input; ?>
									</div>
								</div>
						<?php endforeach; ?>
					<?php echo HTMLHelper::_('uitab.endTab'); ?>

					<input type="hidden" name="task" value="" />

					<?php echo HTMLHelper::_('form.token'); ?>

				<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
		</div>
		<!-- End Localise Package -->
	</div>
</form>
