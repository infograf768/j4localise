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
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('stylesheet', 'com_localise/localise.css', ['version' => 'auto', 'relative' => true]);

HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.core');
HTMLHelper::_('bootstrap.tooltip');

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$params     = ComponentHelper::getParams('com_localise');
$ref_tag    = $params->get('reference', 'en-GB');
?>

<form action="<?php echo \JRoute::_('index.php?option=com_localise&view=translations');?>" method="post" name="adminForm" id="adminForm">
	<?php echo $this->loadTemplate('filter'); ?>
	<div class="col-md-12">
		<?php echo $this->loadTemplate('legend'); ?>
	</div>
	<div class="col-md-12">
		<?php if ($ref_tag == 'en-GB') : ?>
			<?php echo $this->loadTemplate('references'); ?>
		<?php endif; ?>
	</div>
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
		<?php echo \JHtml::_('form.token'); ?>
	</div>
	<!-- End Content -->
</form>
