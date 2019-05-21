<?php
/**
 * @package     Com_Localise
 * @subpackage  controller
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Localise\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Component\Localise\Administrator\Helper\LocaliseHelper;
use Joomla\Component\Localise\Administrator\Model\PackageModel;
use Joomla\Component\Localise\Administrator\Model\PackagefileModel;
use Joomla\Utilities\ArrayHelper;

/**
 * Packages Controller class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 * @since       1.0
 */
class PackagesController extends AdminController
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $text_prefix  = 'COM_LOCALISE_PACKAGES';

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  The array of possible config values. Optional.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = 'Packages', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

	/**
	 * Display View
	 *
	 * @param   boolean  $cachable   Enable cache or not.
	 * @param   array    $urlparams  todo: this params can probably be removed.
	 *
	 * @return  void     Display View
	 */
	public function display($cachable = false, $urlparams = array())
	{
		Factory::getApplication()->input->set('view', 'packages');
		parent::display($cachable);
	}

	/**
	 * Delete Packages
	 *
	 * @return  void
	 */
	public function delete()
	{
		// Check for request forgeries.
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user = Factory::getUser();
		$ids  = Factory::getApplication()->input->get('cid', array(), 'array');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			$path    = LocaliseHelper::getFilePath($id);
			$context = LocaliseHelper::isCorePackage($path) ?
						'package' : 'packagefile';

			//$model = \JModelLegacy::getInstance($context, 'LocaliseModel', array('ignore_request' => true));
			if ($context == 'package')
			{
				$model = new \Joomla\Component\Localise\Administrator\Model\PackageModel(array('ignore_request' => true));
			}
			elseif ($context == 'packagefile')
			{
				$model = new \Joomla\Component\Localise\Administrator\Model\PackagefileModel(array('ignore_request' => true));
			}

			$model->getState();
			$model->setState("$context.id", $id);
			$item  = $model->getItem();

			if (!$item->standalone)
			{
				// Prune items that you can't delete.
				unset($ids[$i]);
				return Factory::getApplication()->enqueueMessage(Text::_('COM_LOCALISE_ERROR_PACKAGES_DELETE'), 'notice');
			}

			if (!$user->authorise('core.delete', 'com_localise.' . (int) $id))
			{
				// Prune items that you can't delete.
				unset($ids[$i]);
				return Factory::getApplication()->enqueueMessage(Text::_('JERROR_CORE_DELETE_NOT_PERMITTED'), 'notice');
			}
		}

		if (empty($ids))
		{
			$msg  = Text::_('JERROR_NO_ITEMS_SELECTED');
			$type = 'error';
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			$ids = ArrayHelper::toInteger($ids);

			// Remove the items.
			if (!$model->delete($ids))
			{
				$msg  = implode("<br />", $model->getErrors());
				$type = 'error';
			}
			else
			{
				$msg  = Text::sprintf('JCONTROLLER_N_ITEMS_DELETED', count($ids));
				$type = 'message';
			}
		}

		$this->setRedirect(Route::_('index.php?option=com_localise&view=packages', false), $msg, $type);
	}

	/**
	 * Export Packages
	 *
	 * @return  void
	 */
	public function export()
	{
		// Check for request forgeries.
		Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user = Factory::getUser();
		$ids  = Factory::getApplication()->input->get('cid', array(), 'array');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.create', 'com_localise.' . (int) $id))
			{
				// Prune items that you can't export.
				unset($ids[$i]);
				return Factory::getApplication()->enqueueMessage(Text::_('COM_LOCALISE_EXPORT_NOT_PERMITTED'), 'notice');
			}
		}

		if (empty($ids))
		{
			$msg  = Text::_('JERROR_NO_ITEMS_SELECTED');
			$type = 'error';
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Export the packages.
			if (!$model->export($ids))
			{
				$msg  = implode("<br />", $model->getErrors());
				$type = 'error';
			}
		}

		$this->setRedirect(Route::_('index.php?option=com_localise&view=packages', false), $msg, $type);
	}

	/**
	 * Clone an existing package.
	 *
	 * @return  void
	 */
	public function duplicate()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user = Factory::getUser();
		$ids  = Factory::getApplication()->input->get('cid', array(), 'array');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.create', 'com_localise.' . (int) $id))
			{
				// Prune items that you can't clone.
				unset($ids[$i]);
				return Factory::getApplication()->enqueueMessage(Text::_('COM_LOCALISE_ERROR_PACKAGES_CLONE_NOT_PERMITTED'), 'notice');
			}
		}

		if (empty($ids))
		{
			$msg  = Text::_('JERROR_NO_ITEMS_SELECTED');
			$type = 'error';
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Clone the items.
			if (!$model->duplicate($ids))
			{
				$msg  = implode("<br />", $model->getErrors());
				$type = 'error';
			}
			else
			{
				$msg  = Text::plural('COM_LOCALISE_N_PACKAGES_DUPLICATED', count($ids));
				$type = 'message';
			}
		}

		$this->setRedirect(Route::_('index.php?option=com_localise&view=packages', false), $msg, $type);
	}

	/**
	 * Check in override to checkin one record of either package or packagefile.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   12.2
	 */
	public function checkin()
	{
		// Check for request forgeries.
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$ids = Factory::getApplication()->input->post->get('cid', array(), 'array');

		// Checkin only first id if more than one ids are present.
		$id = $ids[0];

		$path    = LocaliseHelper::getFilePath($id);
		$context = LocaliseHelper::isCorePackage($path) ?
					'package' : 'packagefile';
		//$model = \JModelLegacy::getInstance($context, 'LocaliseModel', array('ignore_request' => true));
		if ($context == 'package')
		{
			$model = new PackageModel();
		}
		elseif ($context == 'packagefile')
		{
			$model = new PackagefileModel();
		}

		$model->getState();
		$return = $model->checkin($id);

		if ($return === false)
		{
			// Checkin failed.
			$message = Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
			$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');

			return false;
		}
		else
		{
			// Checkin succeeded.
			$message = Text::plural($this->text_prefix . '_N_ITEMS_CHECKED_IN', count($ids));
			$this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message);

			return true;
		}
	}
}
