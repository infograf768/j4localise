<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Localise\Administrator\View\Languages;

defined('_JEXEC') or die;

use Joomla\Component\Localise\Administrator\Helper\LocaliseHelper;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

/**
 * Languages View class for the Localise component
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

	protected $state;

	protected $form;
	
	public $filterForm;
	
	public $activeFilters;

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
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state      = $this->get('State');
		$this->form       = $this->get('Form');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		LocaliseHelper::addSubmenu('languages');
		
		unset($this->activeFilters['client']);
		unset($this->activeFilters['tag']);

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);

			return false;
		}

		// Set the toolbar
		$this->addToolbar();
		$this->sidebar = \JHtmlSidebar::render();

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
		$canDo = \JHelperContent::getActions('com_localise', 'component');

		\JToolbarHelper::title(\JText::sprintf('COM_LOCALISE_HEADER_MANAGER', \JText::_('COM_LOCALISE_HEADER_LANGUAGES')), 'comments-2 langmanager');

		if ($canDo->get('localise.create'))
		{
			\JToolbarHelper::addNew('language.add');
			\JToolbarHelper::custom('languages.purge', 'purge', 'purge', 'COM_LOCALISE_PURGE', false, false);
			\JToolbarHelper::divider();
		}

		if ($canDo->get('core.admin'))
		{
			\JToolbarHelper::preferences('com_localise');
			\JToolbarHelper::divider();
		}

		\JToolBarHelper::help('screen.languages', true);

		\JHtmlSidebar::setAction('index.php?option=com_localise&view=languages');
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
			'name'   => \JText::_('COM_LOCALISE_HEADING_LANGUAGES_NAME'),
			'tag'    => \JText::_('COM_LOCALISE_HEADING_LANGUAGES_TAG'),
			'client' => \JText::_('COM_LOCALISE_HEADING_LANGUAGES_CLIENT'),
		);
	}
}
