<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Localise\Administrator\View\Language;

defined('_JEXEC') or die;

use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View to edit a language.
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
		//JLoader::import('joomla.client.helper');

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
		$canDo = ContentHelper::getActions('com_localise', 'component');

		$user       = Factory::getUser();
		$isNew      = empty($this->item->id);
		$client     = $this->item->client;
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		ToolbarHelper::title(
			Text::sprintf(
				'COM_LOCALISE_HEADER_MANAGER',
				$isNew ? Text::_('COM_LOCALISE_HEADER_LANGUAGE_NEW') : Text::_('COM_LOCALISE_HEADER_LANGUAGE_EDIT')
			),
			'icon-comments-2 langmanager'
		);

		// If not checked out, can save the item.
		if (!$checkedOut)
		{
			ToolbarHelper::apply('language.apply');
			ToolbarHelper::save('language.save');
		}

		ToolBarHelper::cancel('language.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
		ToolBarHelper::divider();

		if ($canDo->get('localise.create') && !$isNew && $client != 'installation')
		{
			ToolbarHelper::custom('language.copy', 'copy.png', 'copy.png', 'COM_LOCALISE_COPY_REF_TO_NEW_LANG', false);
			ToolBarHelper::divider();
		}

		ToolBarHelper::help('screen.language', true);
	}
}
