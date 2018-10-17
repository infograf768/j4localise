<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Localise\Administrator\View\Package;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
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
		jimport('joomla.client.helper');

		// Get the data
		$app           = Factory::getApplication();
		$this->state   = $this->get('State');
		$this->item    = $this->get('Item');
		$this->form    = $this->get('Form');
		$this->formftp = $this->get('FormFtp');
		$this->ftp     = \JClientHelper::setCredentialsFromRequest('ftp');
		$this->file    = $app->input->get('file');
		$this->fileName = base64_decode($this->file);
		$this->location	= $app->input->get('location');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);

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

		$user		= Factory::getUser();
		$canDo		= \JHelperContent::getActions('com_localise', 'component');
		$isNew		= empty($this->item->id);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		ToolbarHelper::title(
			\JText::sprintf(
				'COM_LOCALISE_HEADER_MANAGER',
				$isNew ? \JText::_('COM_LOCALISE_HEADER_PACKAGE_NEW') : \JText::_('COM_LOCALISE_HEADER_PACKAGE_EDIT')
			),
			'comments-2 langmanager'
		);

		// If not checked out, can save the item.
		if (!$checkedOut)
		{
			ToolbarHelper::apply('package.apply');
			ToolbarHelper::save('package.save');
		}

		if (!$isNew && $canDo->get('localise.create'))
		{
			ToolBarHelper::divider();
			ToolbarHelper::modal('fileModal', 'icon-upload', 'COM_LOCALISE_BUTTON_IMPORT_FILE');
			ToolBarHelper::divider();
		}

		ToolbarHelper::custom('package.download', 'out.png', 'out.png', 'COM_LOCALISE_TOOLBAR_PACKAGE_DOWNLOAD', false);
		ToolBarHelper::divider();
		ToolBarHelper::cancel("package.cancel", $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
		ToolBarHelper::divider();
		ToolBarHelper::help('screen.package', true);
	}

	/**
	 * Prepare Document
	 *
	 * @return  void
	 */
	protected function prepareDocument()
	{
		$document = Factory::getDocument();
		$document->setTitle(\JText::sprintf('COM_LOCALISE_TITLE', \JText::_('COM_LOCALISE_TITLE_PACKAGE')));
	}
}
