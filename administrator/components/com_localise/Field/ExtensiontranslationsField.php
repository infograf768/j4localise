<?php
/**
 * @package     Com_Localise
 * @subpackage  models
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Localise\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Form\Field\GroupedlistField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Localise\Administrator\Helper\LocaliseHelper;
use Joomla\Utilities\ArrayHelper;

/**
 * Form Field ExtensionTranslations class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class ExtensiontranslationsField extends GroupedlistField
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Extensiontranslations';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return  array    An array of JHtml options.
	 */
	protected function getGroups()
	{
		$params         = ComponentHelper::getParams('com_localise');
		$reftag	        = $params->get('reference', '');

		$formdata       = $this->form->getData();
		$langtag        = $formdata["language"];

		$istranslation  = $reftag != $langtag;

		$coreadminfiles = array();
		$coresitefiles  = array();
		$noncorefiles   = array();
		$extrafiles     = array();

		$requiredtags   = array($reftag);

		if ($istranslation)
		{
			$requiredtags[] = $langtag;
		}

		$requiredtypes  = array('_thirdparty');

		if (empty($reftag))
		{
			$reftag = 'en-GB';
		}

		// Remove '.ini' from values
		if (is_array($this->value))
		{
			foreach ($this->value as $key => $val)
			{
				$ext = File::getExt($val);

				if ($ext == "ini")
				{
					$this->value[$key] = substr($val, 0, -4);
				}
			}
		}

		$xml            = simplexml_load_file(JPATH_ROOT . '/media/com_localise/packages/core.xml');
		$coreadminfiles = (array) $xml->administrator->children();
		$coresitefiles  = (array) $xml->site->children();

		$coresitefiles  = $coresitefiles['filename'];
		$coreadminfiles = $coreadminfiles['filename'];

		$coreadminfiles = self::suffix_array_values($coreadminfiles, '.ini');
		$coresitefiles  = self::suffix_array_values($coresitefiles, '.ini');

		$package = (string) $this->element['package'];
		$groups  = array('Site' => array(), 'Administrator' => array());

		foreach (array('Site', 'Administrator') as $client)
		{
			$noncorefiles[$client] = array();
			$extrafiles[$client]   = array();

			$path                  = constant('LOCALISEPATH_' . strtoupper($client)) . '/language';

			if (Folder::exists($path))
			{
				$tags = Folder::folders($path, '.', false, false, array('overrides', '.svn', 'CVS', '.DS_Store', '__MACOSX'));

				if ($tags)
				{
					foreach ($tags as $tag)
					{
						if (!in_array($tag, $requiredtags))
						{
							continue;
						}

						$files = Folder::files("$path/$tag", ".ini$");

						if ($client == 'Site' && $tag == $reftag)
						{
							$noncorefiles[$client] = array_diff($files, $coresitefiles);
						}
						elseif ($client == 'Administrator' && $tag == $reftag)
						{
							$noncorefiles[$client] = array_diff($files, $coreadminfiles);
						}

						if ($istranslation && $client == 'Site' && $tag == $langtag)
						{
							$extrafiles[$client] = array_diff($files, $coresitefiles);
						}
						elseif ($istranslation && $client == 'Administrator' && $tag == $langtag)
						{
							$extrafiles[$client] = array_diff($files, $coreadminfiles);
						}

						foreach ($files as $file)
						{

							$class = '';

							if (in_array($file, $noncorefiles[$client]) && $tag == $reftag)
							{
								$class = 'reference-in-core-folder';
							}
							elseif (in_array($file, $extrafiles[$client]) && $tag == $langtag)
							{
								$class = 'translation-extra-in-core-folder';
							}
							else
							{
								continue;
							}

							if ($file == 'joomla.ini')
							{
								$key      = 'joomla';
								$value    = Text::_('COM_LOCALISE_TEXT_TRANSLATIONS_JOOMLA');
								$origin   = LocaliseHelper::getOrigin('', strtolower($client));
								$disabled = $origin != $package && $origin != '_thirdparty';
							}
							else
							{
								$key      = substr($file, 0, strlen($file) - 4);
								$value    = $key;
								$origin   = LocaliseHelper::getOrigin($key, strtolower($client));
								$disabled = $origin != $package && $origin != '_thirdparty';
							}

							if (!array_key_exists($key, $groups[$client]))
							{
								$groups[$client][$key] = HTMLHelper::_('select.option', strtolower($client) . '_' . $key, $value, 'value', 'text', false);
								$groups[$client][$key]->class = $class;
							}
						}
					}
				}
			}
		}

		$scans = LocaliseHelper::getScans();

		foreach ($scans as $scan)
		{
			$prefix     = $scan['prefix'];
			$suffix     = $scan['suffix'];
			$type       = $scan['type'];
			$client     = ucfirst($scan['client']);
			$path       = $scan['path'];
			$folder     = $scan['folder'];
			$extensions = Folder::folders($path);

			foreach ($extensions as $extension)
			{
				// Take off core extensions
				$file = "$prefix$extension$suffix.ini";

				if (($client == 'Site' && !in_array($file, $coresitefiles)) || ($client == 'Administrator' && !in_array($file, $coreadminfiles)))
				{
					if (Folder::exists("$path$extension/language"))
					{
						// Scan extensions folder
						$tags = Folder::folders("$path$extension/language");

						foreach ($tags as $tag)
						{
							if (!in_array($tag, $requiredtags))
							{
								continue;
							}

							$file   = "$path$extension/language/$tag/$prefix$extension$suffix.ini";

							//Getting the $origin to avoid add, for example, overrides
							$origin = LocaliseHelper::getOrigin("$prefix$extension$suffix", strtolower($client));

							if (File::exists($file) && in_array($origin, $requiredtypes))
							{
								if (!array_key_exists("$prefix$extension$suffix", $groups[$client]))
								{
									$groups[$client]["$prefix$extension$suffix"] = HTMLHelper::_(
											'select.option', strtolower($client) . '_' . "$prefix$extension$suffix", "$prefix$extension$suffix", 'value', 'text', false
									);

									$groups[$client]["$prefix$extension$suffix"]->class = "in-$type-folder";
								}
							}
						}
					}
				}
			}
		}

		foreach ($groups as $client => $extensions)
		{
			if (count($groups[$client]) == 0)
			{
				$groups[$client][] = HTMLHelper::_('select.option', '',  Text::_('COM_LOCALISE_NOTRANSLATION'), 'value', 'text', true);
			}
			else
			{
				ArrayHelper::sortObjects($groups[$client], 'text');
			}
		}

		// Merge any additional options in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);

		return $groups;
	}

	/**
	 * Method to add a suffix to an array.
	 *
	 * @param   array   $array   An array of core files.
	 * @param   string  $suffix  The suffix to add to each file.
	 *
	 * @return  array   The modified array
	 */
	public static function suffix_array_values($array, $suffix = '')
	{
		if (!is_array($array))
		{
			return false;
		}

		// Suffix the values and respect the keys
		foreach ($array as $key => $value)
		{
			if (!is_string($value))
			{
				continue;
			}

			$array[$key] = $value . $suffix;
		}

		return $array;
	}
}