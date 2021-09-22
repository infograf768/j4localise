<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Localise\Administrator\View\Packagefile;

defined('_JEXEC') or die;

use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Package View class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class HtmlView extends BaseHtmlView
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get the data
		$this->state   = $this->get('State');
		$this->item    = $this->get('Item');
		$this->form    = $this->get('Form');
		$this->formftp = $this->get('FormFtp');
		$this->ftp     = ClientHelper::setCredentialsFromRequest('ftp');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new GenericDataException(implode("\n", $errors), 500);

			return false;
		}

		// Set the toolbar
		$this->addToolbar();

		// Prepare the document
		$this->prepareDocument();

		// Display the view
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user       = Factory::getUser();
		$userId     = $user->id;
		$isNew      = empty($this->item->id);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		ToolbarHelper::title(
			Text::sprintf(
				'COM_LOCALISE_HEADER_MANAGER',
				$isNew ? Text::_('COM_LOCALISE_HEADER_FILEPACKAGE_NEW') : Text::_('COM_LOCALISE_HEADER_FILEPACKAGE_EDIT')
			),
			'icon-comments-2 langmanager'
		);

		// If not checked out, can save the item.
		if (!$checkedOut)
		{
			ToolbarHelper::apply('packagefile.apply');
			ToolbarHelper::save('packagefile.save');
		}

		ToolbarHelper::custom('packagefile.download', 'out.png', 'out.png', 'COM_LOCALISE_TOOLBAR_PACKAGE_DOWNLOAD', false);

		ToolBarHelper::cancel("packagefile.cancel", $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
		// ToolBarHelper::divider();
		// ToolBarHelper::help('screen.packagefile', true);
	}

	/**
	 * Prepare Document
	 *
	 * @return  void
	 */
	protected function prepareDocument()
	{
		$document = Factory::getDocument();
		$document->setTitle(Text::sprintf('COM_LOCALISE_TITLE', Text::_('COM_LOCALISE_TITLE_PACKAGE')));
	}
}
