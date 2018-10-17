<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Localise\Administrator\View\Packages;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\Component\Localise\Administrator\Helper\LocaliseHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Packages View class for the Localise component
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class HtmlView extends BaseHtmlView
{
	protected $items;

	protected $pagination;

	protected $form;

	protected $state;

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
		$app				= Factory::getApplication();
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->form       = $this->get('Form');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->file			= $app->input->get('file');

		LocaliseHelper::addSubmenu('packages');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);

			return false;
		}

		// Set the toolbar
		$this->addToolbar();
		$this->sidebar = \JHtmlSidebar::render();

		// Prepare the document
		$this->prepareDocument();

		// Display the view
		parent::display($tpl);
	}

	/**
	 * Prepare Document
	 *
	 * @return  void
	 */
	protected function prepareDocument()
	{
		$document = Factory::getDocument();
		$document->setTitle(\JText::sprintf('COM_LOCALISE_TITLE', \JText::_('COM_LOCALISE_TITLE_PACKAGES')));
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
		$canDo = \JHelperContent::getActions('com_localise', 'component');

		ToolBarHelper::title(\JText::sprintf('COM_LOCALISE_HEADER_MANAGER', \JText::_('COM_LOCALISE_HEADER_PACKAGES')), 'comments-2 langmanager');

		if ($canDo->get('localise.create'))
		{
			ToolBarHelper::addNew('package.add', 'COM_LOCALISE_NEW_CORE_PACKAGE');
		}

		if ($canDo->get('localise.create'))
		{
			ToolBarHelper::addNew('packagefile.add', 'COM_LOCALISE_NEW_FILE_PACKAGE');
		}

		if ($canDo->get('localise.create') || $canDo->get('localise.edit'))
		{
			ToolBarHelper::divider();
		}

		if ($canDo->get('localise.create'))
		{
			ToolBarHelper::custom('packages.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
		}

		if ($canDo->get('localise.delete'))
		{
			ToolBarHelper::deleteList('COM_LOCALISE_MSG_PACKAGES_VALID_DELETE', 'packages.delete');
			ToolBarHelper::divider();
		}

		if ($canDo->get('localise.create'))
		{
			ToolBarHelper::modal('fileModal', 'icon-upload', 'COM_LOCALISE_BUTTON_IMPORT_XML');
			ToolBarHelper::divider();
			ToolBarHelper::custom('packages.export',  'out.png', 'out.png', 'COM_LOCALISE_BUTTON_EXPORT_XML', true, false);
			ToolBarHelper::divider();
		}

		if ($canDo->get('core.admin'))
		{
			ToolBarHelper::preferences('com_localise');
			ToolBarHelper::divider();
		}

		ToolBarHelper::help('screen.packages', true);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since 3.0
	 */
	protected function getSortFields()
	{
		return array(
			'title'    => \JText::_('COM_LOCALISE_HEADING_PACKAGES_TITLE'),
			'language' => \JText::_('COM_LOCALISE_LABEL_PACKAGE_LANGUAGE'),
			'version'  => \JText::_('COM_LOCALISE_LABEL_PACKAGE_VERSION'),
			'core'     => \JText::_('COM_LOCALISE_HEADING_PACKAGES_TYPE'),
		);
	}
}
