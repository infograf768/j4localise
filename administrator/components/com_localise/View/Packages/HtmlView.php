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

use Joomla\Component\Localise\Administrator\Helper\LocaliseHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
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
		$app                 = Factory::getApplication();
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->state         = $this->get('State');
		$this->form          = $this->get('Form');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->file          = $app->input->get('file');

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
	 * Prepare Document
	 *
	 * @return  void
	 */
	protected function prepareDocument()
	{
		$document = Factory::getDocument();
		$document->setTitle(Text::sprintf('COM_LOCALISE_TITLE', Text::_('COM_LOCALISE_TITLE_PACKAGES')));
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
		$canDo = ContentHelper::getActions('com_localise', 'component');

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		ToolBarHelper::title(Text::sprintf('COM_LOCALISE_HEADER_MANAGER', Text::_('COM_LOCALISE_HEADER_PACKAGES')), 'comments-2 langmanager');

		if ($canDo->get('localise.create'))
		{
			$dropdown = $toolbar->dropdownButton('new')
				->text('JTOOLBAR_NEW')
				->toggleSplit(false)
				->icon('fa fa-plus')
				->buttonClass('btn btn-action');

			$childBar = $dropdown->getChildToolbar();

			$childBar->standardButton('save-new')
				->text('COM_LOCALISE_NEW_CORE_PACKAGE')
				->task('package.add');
			$childBar->standardButton('save-new')
				->text('COM_LOCALISE_NEW_FILE_PACKAGE')
				->task('packagefile.add');
		}

		if ($canDo->get('localise.create'))
		{
			ToolBarHelper::custom('packages.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
		}

		if ($canDo->get('localise.delete'))
		{
			ToolBarHelper::deleteList('COM_LOCALISE_MSG_PACKAGES_VALID_DELETE', 'packages.delete');
		}

		if ($canDo->get('localise.create'))
		{
			ToolBarHelper::modal('fileModal', 'icon-upload', 'COM_LOCALISE_BUTTON_IMPORT_XML');
			ToolBarHelper::custom('packages.export',  'out.png', 'out.png', 'COM_LOCALISE_BUTTON_EXPORT_XML', true, false);
		}

		if ($canDo->get('core.admin'))
		{
			$toolbar->preferences('com_localise');
		}

		//$toolbar->help('screen.packages', true);
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
			'title'    => Text::_('COM_LOCALISE_HEADING_PACKAGES_TITLE'),
			'language' => Text::_('COM_LOCALISE_LABEL_PACKAGE_LANGUAGE'),
			'version'  => Text::_('COM_LOCALISE_LABEL_PACKAGE_VERSION'),
			'core'     => Text::_('COM_LOCALISE_HEADING_PACKAGES_TYPE'),
		);
	}
}
