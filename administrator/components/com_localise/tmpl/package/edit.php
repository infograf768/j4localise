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
use Joomla\CMS\Session\Session;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('jquery.framework');
HTMLHelper::_('bootstrap.modal', '.modal', []);


$fieldSets = $this->form->getFieldsets();
$ftpSets   = $this->formftp->getFieldsets();
Text::script('COM_LOCALISE_MSG_CONFIRM_PACKAGE_SAVE');
Factory::getDocument()->addScriptDeclaration("
	(function($){
		$(document).ready(function () {
			$('.fileupload').click(function(e){

				var form   = $('#filemodalForm');

				// Assign task
				form.find('input[name=task]').val('package.uploadOtherFile');

				// Submit the form
				if (confirm('" . Text::_('COM_LOCALISE_MSG_FILES_VALID_IMPORT') . "'))
				{
					form.trigger('submit');
				}

				// Avoid the standard link action
				e.preventDefault();
			});
		});
	})(jQuery);

	Joomla.submitbutton = function(task)
	{
		if ((task == 'package.apply' || task == 'package.save') && document.formvalidator.isValid(document.getElementById('localise-package-form')))
		{
			if (confirm(Joomla.JText._('COM_LOCALISE_MSG_CONFIRM_PACKAGE_SAVE')))
			{
				Joomla.submitform(task, document.getElementById('localise-package-form'));
			}
		}
		else if (task == 'package.cancel' || task == 'package.download')
		{
			Joomla.submitform(task, document.getElementById('localise-package-form'));
		}
	}
");

Factory::getDocument()->addScriptDeclaration("
	function updateTranslationsList() {
		var packagename = jQuery('#jform_name').val();
		var languagetag = jQuery('#jform_language').val();
		var token = '". Session::getFormToken() ."';
		var required_data = JSON.stringify([{
									'packagename': packagename,
									'languagetag': languagetag
									}]);
		jQuery.post('index.php',{
				                'option' : 'com_localise',
				                'controller' : 'package',
				                'task' : 'package.updatetranslationslist',
				                'format' : 'raw',                   
				                'data' : required_data,                   
				                [token] : '1',
				                'dataType' : 'json'
				        },function(result){                     
				         //handle the result here
						const reply = JSON.parse(result);
						//console.log(reply);
						if (reply.success)
						{
							jQuery('#jform_translations').html(reply.data.html);

							if (reply.data.success_message)
							{
								jQuery('#flash-message-success').empty().show().html(reply.data.success_message).delay(2000).fadeOut(300);
							}
						}
						else
						{
							if (reply.data.error_message)
							{
								jQuery('#flash-message-danger').empty().show().html(reply.data.error_message).delay(2000).fadeOut(300);
							}
						}

						// display the enqueued messages in the message area
						if (reply.messages)
						{
							Joomla.renderMessages(reply.messages);
						}

				        return;
		})
	}

	jQuery(document).ready(function() {
		jQuery('#jform_language').change(function(){
			updateTranslationsList();
		})
	});
");
?>
<?php
	echo '<div class="text-center"><div id="flash-message-danger" class="alert alert-danger flash-message" style="position: fixed; top:45%; right:45%; z-index: 9; display: none;"></div></div>';
	echo '<div class="text-center"><div id="flash-message-notice" class="alert alert-notice flash-message" style="position: fixed; top:45%; right:45%; z-index: 9; display: none;"></div></div>';
	echo '<div class="text-center"><div id="flash-message-success" class="alert alert-success flash-message" style="position: fixed; top:45%; right:45%; z-index: 9; display: none;"></div></div>';
?>
<form action="<?php echo Route::_('index.php?option=com_localise&view=package&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="localise-package-form" class="form-validate">
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
						<?php foreach($this->formftp->getFieldset('ftp',false) as $field) : ?>
							<?php echo $field->renderField(); ?>
						<?php endforeach; ?>
					<?php echo HTMLHelper::_('uitab.endTab');; ?>
					<?php endif; ?>
					<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'default', Text::_($fieldSets['default']->label, true)); ?>
						<div class="row">
						<div class="col-lg-12 col-xl-6">
							<fieldset id="fieldset-default" class="options-form">
								<legend><?php echo Text::_('COM_LOCALISE_FIELDSET_PACKAGE_DETAIL'); ?></legend>
								<?php foreach($this->form->getFieldset('default') as $field) : ?>
									<?php echo $field->renderField(); ?>
								<?php endforeach; ?>
							</fieldset>
						</div>
						<div class="col-lg-12 col-xl-6">
							<fieldset id="fieldset-translations" class="options-form">
								<legend><?php echo Text::_($fieldSets['translations']->label); ?></legend>
								<?php foreach($this->form->getFieldset('translations') as $field) : ?>
									<?php echo $field->renderField(); ?>
								<?php endforeach; ?>
							</fieldset>
						</div>
						</div>
					<?php echo HTMLHelper::_('uitab.endTab'); ?>
					<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_($fieldSets['permissions']->label, true)); ?>
						<fieldset id="fieldset-rules" class="options-form">
							<legend><?php echo Text::_($fieldSets['permissions']->label, true); ?></legend>
							<div>
								<?php echo $this->form->getInput('rules'); ?>
							</div>
						</fieldset>
					<?php echo HTMLHelper::_('uitab.endTab'); ?>
					<input type="hidden" name="task" value="" />
					<?php echo HTMLHelper::_('form.token'); ?>
				<?php echo HTMLHelper::_('uitab.endTabSet'); ?>
		</div>
		<!-- End Localise Package -->
	</div>
</form>

<div id="fileModal" class="modal hide fade">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title"><?php echo Text::_('COM_LOCALISE_IMPORT_NEW_FILE_HEADER'); ?></h3>
			</div>
			<div class="contentpane component">
				<p><?php echo Text::_('COM_LOCALISE_IMPORT_NEW_FILE_DESC'); ?></p>
				<form method="post" action="<?php echo Route::_('index.php?option=com_localise&task=package.uploadOtherFile&file=' . $this->file); ?>"
					class="well" enctype="multipart/form-data" name="filemodalForm" id="filemodalForm">
					<fieldset>
						<label><?php echo Text::_('COM_LOCALISE_TEXT_CLIENT'); ?></label>
						<select name="location" class="custom-select" type="location" required >
							<option value="admin"><?php echo Text::_('JADMINISTRATOR'); ?></option>
							<option value="site"><?php echo Text::_('JSITE'); ?></option>
						</select>
						<label></label>
						<input type="file" name="files" required />
							<a href="#" class="btn btn-primary fileupload">
							<?php echo Text::_('COM_LOCALISE_BUTTON_IMPORT'); ?>
							</a>
					</fieldset>
				</form>
			</div>
			<div class="modal-footer">
				<a role="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-hidden="true"><?php echo Text::_('COM_LOCALISE_MODAL_CLOSE'); ?></a>
			</div>
		</div>
	</div>
</div>

