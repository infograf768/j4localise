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

HTMLHelper::_('stylesheet', 'com_localise/localise.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('jquery.framework');
HTMLHelper::_('bootstrap.tooltip');

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
//$saveOrder  = $listOrder == 'tag';
$sortFields = $this->getSortFields();
Factory::getDocument()->addScriptDeclaration("
	(function($){
		$(document).ready(function () {
			$('.fileupload').click(function(e){

				var form   = $('#filemodalForm');

				// Assign task
				form.find('input[name=task]').val('package.uploadFile');

				// Submit the form
				if (confirm('" . Text::_('COM_LOCALISE_MSG_PACKAGES_VALID_IMPORT') . "'))
				{
					form.trigger('submit');
				}

				// Avoid the standard link action
				e.preventDefault();
			});
		});
	})(jQuery);
");
?>
<form action="<?php echo Route::_('index.php?option=com_localise&view=packages');?>" method="post" name="adminForm" id="adminForm">
	<?php echo $this->loadTemplate('filter'); ?>
		<table class="table table-striped" id="localiseList">
			<thead>
				<?php echo $this->loadTemplate('head'); ?>
			</thead>
			<tfoot>
				<?php echo $this->loadTemplate('foot'); ?>
			</tfoot>
			<tbody>
				<?php echo $this->loadTemplate('body'); ?>
			</tbody>
		</table>
		<div>
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="task" value="" />
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	<!-- End Content -->
</form>

<div id="fileModal" class="modal hide fade">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
		<div class="modal-header">
			<h3 class="modal-title"><?php echo Text::_('COM_LOCALISE_IMPORT_NEW_PACKAGE_HEADER'); ?></h3>
		</div>
		<div class="modal-body">
			<div class="col-md-12">
				<form method="post" action="<?php echo Route::_('index.php?option=com_localise&task=package.uploadFile&file=' . $this->file); ?>"
					class="well" enctype="multipart/form-data" name="filemodalForm" id="filemodalForm">
					<fieldset>
						<input type="file" name="files" required />
						<a href="#" class="hasTooltip btn btn-primary fileupload">
							<?php echo Text::_('COM_LOCALISE_BUTTON_IMPORT'); ?>
						</a>
					</fieldset>
				</form>
			</div>
		</div>
		<div class="modal-footer">
			<a role="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true"><?php echo Text::_('COM_LOCALISE_MODAL_CLOSE'); ?></a>
		</div>
	</div>
  </div>
</div>
