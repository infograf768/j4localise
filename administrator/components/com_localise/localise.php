<?php
/**
 * @package     Com_Localise
 * @subpackage  com_localise
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_localise'))
{
	throw new \JAccessExceptionNotallowed(\JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

// Include helper files
require_once JPATH_COMPONENT_ADMINISTRATOR . '/helper/defines.php';
require_once JPATH_ADMINISTRATOR . '/components/com_localise/helper/localisehelper.php';
//require_once JPATH_COMPONENT . '/helper/localisehelper.php';

// Load Composer Autoloader

require_once JPATH_COMPONENT_ADMINISTRATOR . '/vendor/autoload.php';
\JLoader::register('JFile', JPATH_LIBRARIES . '/joomla/filesystem/file.php');
\JLoader::register('JFolder', JPATH_LIBRARIES . '/joomla/filesystem/folder.php');
\JLoader::register('JPath', JPATH_LIBRARIES . '/joomla/filesystem/path.php');
\JLoader::register('LocaliseHelper', JPATH_COMPONENT . '/helper/localisehelper.php');

// Get the controller
$controller = \JControllerLegacy::getInstance('Localise');

// Execute the task.
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
