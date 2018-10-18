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

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<tr>
	<th width="1%" class="nowrap center hidden-phone">#</th>
	<th width="1%" class="hidden-phone"> <?php echo HTMLHelper::_('grid.checkall'); ?></th>
	<th class="title"><?php echo HTMLHelper::_('searchtools.sort', 'COM_LOCALISE_HEADING_PACKAGES_TITLE', 'title', $listDirn, $listOrder); ?></th>
	<th class="title"><?php echo HTMLHelper::_('searchtools.sort', 'COM_LOCALISE_LABEL_PACKAGE_LANGUAGE', 'language', $listDirn, $listOrder); ?></th>
	<th class="title"><?php echo HTMLHelper::_('searchtools.sort', 'COM_LOCALISE_LABEL_PACKAGE_VERSION', 'version', $listDirn, $listOrder); ?></th>
	<th width="20%"><?php echo HTMLHelper::_('searchtools.sort', 'COM_LOCALISE_HEADING_PACKAGES_TYPE', 'core', $listDirn, $listOrder); ?></th>
</tr>
