<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Localise\Administrator\View\Translation;

defined('_JEXEC') or die;

use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * Translation View class for the Localise component
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
			throw new \JViewGenericdataexception(implode("\n", $errors), 500);

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

		$user		= Factory::getUser();
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$complete   = (int) ComponentHelper::getParams('com_localise')->get('complete', 0);

		$toolbar = Toolbar::getInstance('toolbar');

		if ($this->state->get('translation.filename') == 'joomla')
		{
			$filename = $this->state->get('translation.tag') . '.ini';
		}
		else
		{
			$filename = $this->state->get('translation.tag') . '.' . $this->state->get('translation.filename') . '.ini';
		}

		ToolbarHelper::title(
			Text::sprintf(
				'COM_LOCALISE_HEADER_MANAGER',
				Text::sprintf($this->item->exists ? 'COM_LOCALISE_HEADER_TRANSLATION_EDIT' : 'COM_LOCALISE_HEADER_TRANSLATION_NEW', $filename)
			),
			'comments-2 langmanager'
		);

		if (!$checkedOut)
		{
			if ($complete === 1)
			{
				$toolbar->confirmButton('apply')
					->text('JAPPLY')
					->message('COM_LOCALISE_CONFIRM_TRANSLATION_SAVE')
					->task('translation.apply');

				$toolbar->confirmButton('save')
					->text('JSAVE')
					->message('COM_LOCALISE_CONFIRM_TRANSLATION_SAVE')
					->task('translation.save');
			}
			else
			{
				ToolbarHelper::apply('translation.apply');
				ToolbarHelper::save('translation.save');
			}
		}

		ToolbarHelper::cancel('translation.cancel');
		ToolBarHelper::divider();
		ToolBarHelper::help('screen.translation', true);
	}
}
