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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::_('stylesheet', 'com_localise/localise.css', ['version' => 'auto', 'relative' => true]);

HTMLHelper::_('behavior.core');
HTMLHelper::_('jquery.framework');

$wa = $this->document->getWebAssetManager();
$wa->useScript('webcomponent.core-loader');
$wa->addInlineScript("
	// Page is loading
	window.onload = function() {
		initSpinner();
	};

	function initSpinner() {
		spinner = document.createElement('joomla-core-loader');
		document.body.appendChild(spinner);
	}

	// Page is loaded
	jQuery(document).ready(function() {
		// Close the spinner when page is loaded
		var spinner = document.querySelector('joomla-core-loader');
		spinner.parentNode.removeChild(spinner);

		// Any field from the Select fieldset triggers the spinner
		jQuery('select').change(function(){
			spinner = document.createElement('joomla-core-loader');
			document.body.appendChild(spinner);
		})
	});
");

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$params     = ComponentHelper::getParams('com_localise');
$ref_tag    = $params->get('reference', 'en-GB');
?>

<form action="<?php echo Route::_('index.php?option=com_localise&view=translations');?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<!-- Begin Content -->
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this, 'options' => array('selectorFieldName' => 'develop'))); ?>
			</div>
		</div>
		<div class="col-md-12 mb-2">
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

		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="task" value="" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	<!-- End Content -->
</form>
