<?php
/**
 * @package     Com_Localise
 * @subpackage  helper
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
//namespace Joomla\Component\Localise\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;

// Define constants
$params = ComponentHelper::getParams('com_localise');

define('LOCALISEPATH_SITE', JPATH_SITE);
define('LOCALISEPATH_ADMINISTRATOR', JPATH_ADMINISTRATOR);
define('LOCALISEPATH_INSTALLATION', JPATH_ROOT . '/' . $params->get('installation', 'installation'));
define('LOCALISEPATH_API', JPATH_ROOT . '/api');
