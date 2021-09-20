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
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Localise\Administrator\Helper\LocaliseHelper;

include_once JPATH_ADMINISTRATOR . '/components/com_localise/Helper/defines.php';

/**
 * Form Field ExtensionsName class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class ExtensionnameField extends FormField
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Extensionname';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 */
	protected function getInput()
	{
		$attributes = '';
		$params     = ComponentHelper::getParams('com_localise');
		$reftag	    = $params->get('reference', '');

		if (empty($reftag))
		{
			$reftag = 'en-GB';
		}

		// Form priority
		$formdata      = $this->form->getData();
		$langtag       = $formdata["language"];
		$extensionname = $formdata["extensionname"];

		// Ajax priority
		$ajaxlangtag       = (string) $this->element['langtag'];
		$ajaxextensionname = (string) $this->element['extensionname'];

		if (!empty($ajaxlangtag))
		{
			$langtag = $ajaxlangtag;
		}

		if (!empty($ajaxextensionname))
		{
			$extensionname = $ajaxextensionname;
		}

		if (empty($langtag))
		{
			$langtag = $reftag;
		}

		$istranslation  = $reftag != $langtag;

		$coreadminfiles = array();
		$coresitefiles  = array();
		$noncorefiles   = array();
		$allnames       = array();

		$requiredtags   = array($reftag);

		if ($istranslation)
		{
			$requiredtags[] = $langtag;
		}

		$requiredtypes  = array('_thirdparty');

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

			$path = constant('LOCALISEPATH_' . strtoupper($client)) . '/language';

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

						if ($client == 'Site')
						{
							$noncorefiles[$client] = array_diff($files, $coresitefiles);
						}
						elseif ($client == 'Administrator')
						{
							$noncorefiles[$client] = array_diff($files, $coreadminfiles);
						}

						foreach ($files as $file)
						{
							$extensionname = '';

							if (!in_array($file, $noncorefiles[$client]))
							{
								continue;
							}

							$extensionname = self::get_extension_name($file);

							if (!in_array($extensionname, $allnames))
							{
								$allnames[] = $extensionname;
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

							$extensionname = '';
							$file          = "$path$extension/language/$tag/$prefix$extension$suffix.ini";

							//Getting the $origin to avoid add, for example, overrides
							$origin = LocaliseHelper::getOrigin("$prefix$extension$suffix", strtolower($client));

							if (File::exists($file) && in_array($origin, $requiredtypes))
							{
								$extensionname = self::get_extension_name($extension);

								if (!in_array($extensionname, $allnames))
								{
									$allnames[] = $extensionname;
								}
							}
						}
					}
				}
			}
		}

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attributes .= ' disabled="disabled"';
		}

		if ($v = (string) $this->element['onchange'])
		{
			$attributes .= ' onchange="' . $v . '"';
		}

		$attributes .= ' class="custom-select"';

		$options = array();

		foreach ($this->element->children() as $option)
		{
			$options[] = HTMLHelper::_('select.option', $option->attributes('value'), Text::_(trim($option)), array('option.attr' => 'attributes', 'attr' => ''));
		}

		$options[] = HTMLHelper::_('select.option', '', Text::_('COM_LOCALISE_OPTION_EXTENSION_NAME_SELECT'));

		asort($allnames);

		foreach ($allnames as $name)
		{
			$options[] = HTMLHelper::_('select.option', $name, ucfirst($name), array('option.attr' => 'attributes', 'attr' => '')
						);
		}

		$return = array();

		if ((string) $this->element['readonly'] == 'true')
		{
			$return[] = HTMLHelper::_('select.genericlist', $options, '', array('id' => $this->id,
						'list.select' => $this->value, 'option.attr' => 'attributes', 'list.attr' => $attributes)
						);
			$return[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}
		else
		{
			$return[] = HTMLHelper::_('select.genericlist', $options, $this->name, array('id' => $this->id,
						'list.select' => $this->value, 'option.attr' => 'attributes', 'list.attr' => $attributes)
						);
		}

		return implode($return);
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

		// Add a suffix to the values without changing the keys.
		// For example when $suffix = '.ini', if the stored key in array is "com_localise", the assigned value for that key will be changed to "com_localise.ini".
		// Useful when we get the "$xml = simplexml_load_file(JPATH_ROOT . '/media/com_localise/packages/core.xml');"
		// as the files names in "core.xml" have no '.ini' suffix.
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

	/**
	 * Method to get the extension name from a filename.
	 *
	 * @param   string  $filename  The full file name.
	 *
	 * @return  string  The extension name without prefix or suffixes
	 */
	public static function get_extension_name($filename)
	{
		if (empty($filename))
		{
			return false;
		}

		$extensionname = $filename;
		$parts         = explode('.', $filename);

		if (!empty($parts[1]))
		{
			$extensionname = $parts[0];
		}

			switch (substr($filename, 0, 4))
			{
				case 'com_':
					$extensionname = str_replace('com_', '', $extensionname);

					break;

				case 'mod_':
					$extensionname = str_replace('mod_', '', $extensionname);

					break;

				case 'plg_':
					$extensionname = str_replace('plg_', '', $extensionname);

					break;

				case 'tpl_':
					$extensionname = str_replace('tpl_', '', $extensionname);

					break;

			}

		return $extensionname;
	}
}
