<?php
/**
 * @package     Com_Localise
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Access\Exception\Notallowed;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\Component\Localise\Administrator\Helper\LocaliseHelper;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_localise'))
{
	throw new Notallowed(Text::_('JERROR_ALERTNOAUTHOR'), 403);
}

// Include helper files
require_once JPATH_ADMINISTRATOR . '/components/com_localise/Helper/defines.php';
require_once JPATH_ADMINISTRATOR . '/components/com_localise/Helper/LocaliseHelper.php';

// Load Composer Autoloader

require_once JPATH_ADMINISTRATOR . '/components/com_localise/vendor/autoload.php';


// Get the controller
$controller = BaseController::getInstance('Localise');

// Execute the task.
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
