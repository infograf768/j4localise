<?php
/**
 * @package     Com_Localise
 * @subpackage  model
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Localise\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Localise\Administrator\Helper\LocaliseHelper;

include_once JPATH_ADMINISTRATOR . '/components/com_localise/Helper/defines.php';

/**
 * Languages Model class for the Localise component
 *
 * @since  1.0
 */
class LanguagesModel extends ListModel
{
	// protected $filter_fields = array('tag', 'client', 'name');

	protected $context = 'com_localise.languages';

	protected $items;

	protected $languages;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   3.5
	 */
	public function __construct($config = array(), MVCFactoryInterface $factory = null)
	{
		// Reformat 3.x language packs to new format
		$this->reformat();

		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'tag',
				'client',
				'name',
			);
		}

		parent::__construct($config, $factory);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string'));
		$this->setState('client', $this->getUserStateFromRequest($this->context . '.client', 'client', '', 'string'));
		$this->setState('tag', $this->getUserStateFromRequest($this->context . '.tag', 'tag', '', 'string'));

		// Load the parameters.
		$params = ComponentHelper::getParams('com_localise');
		$this->setState('params', $params);

		// Call auto-populate parent method
		parent::populateState($ordering, $direction);
	}

	/**
	 * Get the items (according the filters and the pagination)
	 *
	 * @return  array  array of object items
	 */
	public function getItems()
	{
		if (!isset($this->items))
		{
			$languages = $this->getLanguages();
			$count     = count($languages);
			$start     = $this->getState('list.start');
			$limit     = $this->getState('list.limit');

			if ($start > $count)
			{
				$start = 0;
			}

			if ($limit == 0)
			{
				$start = 0;
				$limit = null;
			}

			$this->items = array_slice($languages, $start, $limit);
		}

		return $this->items;
	}

	/**
	 * Get total number of languages (according to filters)
	 *
	 * @return  int  number of languages
	 */
	public function getTotal()
	{
		return count($this->getLanguages());
	}

	/**
	 * Get all languages (according to filters)
	 *
	 * @return   array  array of object items
	 */
	protected function getLanguages()
	{
		if (!isset($this->languages))
		{
			$this->languages = array();
			$client          = $this->getState('client');
			$tag             = $this->getState('tag');
			$search          = $this->getState('filter.search');

			if (empty($client))
			{
				$clients = array('site', 'administrator');

				if (LocaliseHelper::hasInstallation())
				{
					$clients[] = 'installation';
				}
			}
			else
			{
				$clients = array($client);
			}

			foreach ($clients as $client)
			{
				if (empty($tag))
				{
					$folders = Folder::folders(
						constant('LOCALISEPATH_' . strtoupper($client)) . '/language',
							'.',
							false,
							false,
							array('.svn', 'CVS','.DS_Store','__MACOSX','pdf_fonts','overrides')
					);
				}
				else
				{
					$folders = Folder::folders(
						constant('LOCALISEPATH_' . strtoupper($client)) . '/language',
							'^' . $tag . '$',
							false,
							false,
							array('.svn','CVS','.DS_Store','__MACOSX','pdf_fonts','overrides')
						);
				}

				foreach ($folders as $folder)
				{
					$file = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$folder/langmetadata.xml";

					if (!is_file($file))
					{
						$file = constant('LOCALISEPATH_' . strtoupper($client)) . "/language/$folder/$folder.xml";
					}

					$id = LocaliseHelper::getFileId($file);

					// If no file is found.
					if ($id < 1)
					{
						continue;
					}

					//$model = \JModelLegacy::getInstance('Language', 'LocaliseModel', array('ignore_request' => true));
					$model = new LanguageModel();
					$model->getState();
					$model->setState('language.tag', $folder);
					$model->setState('language.client', $client);
					$model->setState('language.id', $id);

					$language = $model->getItem();

					if (empty($search) || preg_match("/$search/i", $language->name))
					{
						$this->languages[] = $language;
					}
				}
			}

			$ordering = $this->getState('list.ordering')
				? $this->getState('list.ordering')
				: 'name';
			$this->languages = ArrayHelper::sortObjects(
				$this->languages,
				$ordering, $this->getState('list.direction') == 'DESC' ? -1 : 1
			);
		}

		return $this->languages;
	}

	/**
	 * Cleans out _localise table.
	 *
	 * @return  bool True on success
	 *
	 * @throws	Exception
	 * @since   1.0
	 */
	public function purge()
	{
		// Get the localise data
		$query = $this->_db->getQuery(true);
		$query->select("l.id");
		$query->from("#__localise AS l");
		$query->join('LEFT', '#__assets AS ast ON ast.id = l.asset_id');
		$query->order('ast.rgt DESC');
		$this->_db->setQuery($query);

		try
		{
			$data = $this->_db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException($e->getMessage());
		}

		foreach ($data as $key => $value)
		{
			$id = $value->id;

			// Get the localise table
			$table = Table::getInstance('LocaliseTable', '\\Joomla\\Component\\Localise\\Administrator\\Table\\');

			// Load it before delete.
			$table->load($id);

			// Delete
			try
			{
				$table->delete($id);
			}
			catch (RuntimeException $e)
			{
				throw new RuntimeException($e->getMessage());
			}
		}

		Factory::getApplication()->enqueueMessage(Text::_('COM_LOCALISE_PURGE_SUCCESS'));

		return true;
	}

	/**
	 * Reformat j3 lang files.
	 *
	 * @return  bool True on success
	 *
	 * @since   5.0
	 */
	public function reformat()
	{
		$clients = array('site', 'administrator');

		if (LocaliseHelper::hasInstallation())
		{
			$clients[] = 'installation';
		}

		foreach ($clients as $client)
		{
			$folders = Folder::folders(
				constant('LOCALISEPATH_' . strtoupper($client)) . '/language',
					'.',
					false,
					false,
					array('.svn', 'CVS','.DS_Store','__MACOSX','pdf_fonts','overrides', 'en-GB')
			);

			foreach ($folders as $folder)
			{
				$file = constant('LOCALISEPATH_' . strtoupper($client)) . '/language/' . $folder . '/' . $folder . '.xml';
				$path = constant('LOCALISEPATH_' . strtoupper($client)) . '/language';

				if (File::exists($file))
				{
					$files   = Folder::files($path . '/' . $folder);
					$newPath = $path . '/' . $folder;

					foreach ($files as $file)
					{
						if ($file === $folder . '.xml')
						{
							rename($newPath . '/' . $file, $newPath . '/langmetadata.xml');
						}

						if ($file === $folder . '.localise.php')
						{
							rename($newPath . '/' . $file, $newPath . '/localise.php');
						}
					}

					$inifiles = Folder::files($path . '/' . $folder, '.ini$');

					foreach ($inifiles as $inifile)
					{
						if ($inifile === $folder . '.ini')
						{
							rename($newPath . '/' . $inifile,  $newPath . '/joomla.ini');
						}
						else
						{
							$newinifile = str_replace($folder . '.', '', $inifile);
							rename($newPath . '/' . $inifile,  $newPath . '/' . $newinifile);
						}
					}

					Factory::getApplication()->enqueueMessage(Text::sprintf('COM_LOCALISE_REFORMAT_SUCCESS', $folder, $client), 'message');

					$return = true;
				}
				else
				{
					Factory::getApplication()->enqueueMessage(Text::_('COM_LOCALISE_REFORMAT_NONE'));

					$return = false;
				}
			}
		}

		return $return;
	}
}
