<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Layout\LayoutHelper;


HTMLHelper::_('stylesheet', 'com_localise/localise.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.core');

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
//$params = (isset($this->state->params)) ? $this->state->params : new \JObject;
?>
<form action="<?php echo Route::_('index.php?option=com_localise&view=languages');?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('filterButton' => false))); ?>
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

				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="task" value="" />
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
	<!-- End Content -->
</form>
