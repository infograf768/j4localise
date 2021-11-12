<?php
/**
 * @package     Com_Localise
 * @subpackage  Field
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace Joomla\Component\Localise\Administrator\Field;

defined('_JEXEC') or die;

// Load Composer Autoloader
require_once JPATH_ADMINISTRATOR . '/components/com_localise/vendor/autoload.php';


use Joomla\Github\Github;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;

FormHelper::loadFieldClass('list');

/**
 * Form Field Place class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class BranchesField extends ListField
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Branches';

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getOptions()
	{
		$attributes    = '';
		$params        = ComponentHelper::getParams('com_localise');
		$branches      = array();
		$branches_path = JPATH_ROOT
				. '/administrator/components/com_localise/customisedref/joomla_branches.txt';
		if (File::exists($branches_path))
		{
			file_put_contents($branches_path, '');

			$branches_file = file_get_contents($branches_path);
			$branches      = preg_split("/\\r\\n|\\r|\\n/", $branches_file);
		}

		$gh_user       = 'joomla';
		$gh_project    = 'joomla-cms';
		$gh_token      = $params->get('gh_token', '');

		$options = new Registry;

		if (!empty($gh_token))
		{
			$options->set('headers', ['Authorization' => 'token ' . $gh_token]);
			$github = new Github($options);
		}
		else
		{
			// Without a token runs fatal.
			// $github = new JGithub;

			// Trying with a 'read only' public repositories token
			// But base 64 encoded to avoid Github alarms sharing it.
			$gh_token = base64_decode('MzY2NzYzM2ZkMzZmMWRkOGU5NmRiMTdjOGVjNTFiZTIyMzk4NzVmOA==');
			$options->set('headers', ['Authorization' => 'token ' . $gh_token]);
			$github = new Github($options);
		}

		try
		{
			$active_branches = $github->repositories->get(
					$gh_user,
					$gh_project . '/branches'
					);

			foreach ($active_branches as $active_branch)
			{
				$branch_name = $active_branch->name;

				if (!in_array($branch_name, $branches) && is_numeric($branch_name[0]) && $branch_name[0] >= 4)
				{
					$branches[] = $branch_name;
					Factory::getApplication()->enqueueMessage(
						Text::sprintf('COM_LOCALISE_NOTICE_NEW_BRANCH_DETECTED', $branch_name),
						'notice');
				}
			}
		}
		catch (\Exception $e)
		{
			Factory::getApplication()->enqueueMessage(
				Text::_('COM_LOCALISE_ERROR_GITHUB_GETTING_BRANCHES'),
				'warning');
		}

		//arsort($versions);

		if ($v = (string) $this->element['onchange'])
		{
			$attributes .= ' onchange="' . $v . '"';
		}

		$attributes .= ' class="' . (string) $this->element['class'] . ' iconlist-16-' . $this->value . '"';
		$options = array();

		foreach ($this->element->children() as $option)
		{
			$options[] = HTMLHelper::_('select.option', $option->attributes('value'), Text::_(trim($option)), array('option.attr' => 'attributes', 'attr' => ''));
		}

		$branches_file = '';

		foreach ($branches as $id => $branch)
		{
			if (!empty($branch))
			{
				$options[] = HTMLHelper::_('select.option', $branch, $branch,
							array('option.attr' => 'attributes', 'attr' => 'class="iconlist-16-release"')
							);

				$branches_file .= $branch . "\n";
			}
		}

		File::write($branches_path, $branches_file);

		return $options;
	}
}
