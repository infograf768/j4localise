<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Layout\LayoutHelper;

$params = (isset($this->state->params)) ? $this->state->params : new JObject;

?>
<div class="row">
<!-- Begin Sidebar using custom submenu layout -->
<div id="j-sidebar-container" class="col-md-2">
	<?php echo $this->sidebar; ?>
</div>
<!-- End Sidebar -->
<!-- Begin Content -->
<div class="col-md-10">
<div id="j-main-container" class="j-main-container">
	<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
