<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;

/** @var  array  $displayData */
$data = $displayData;

// Receive overridable options
$data['options'] = !empty($data['options']) ? $data['options'] : array();
$showSelector    = false;

if ($data['view'] instanceof \Joomla\Component\Localise\Administrator\View\Languages\HtmlView)
{
	// Client selector doesn't have to activate the filter bar.
	unset($data['view']->activeFilters['client']);

	// Menutype filter doesn't have to activate the filter bar
	unset($data['view']->activeFilters['tag']);
}

// Set some basic options
$customOptions = array(
	'filtersHidden'       => $data['options']['filtersHidden'] ?? empty($data['view']->activeFilters),
	'defaultLimit'        => $data['options']['defaultLimit'] ?? JFactory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#list_fullordering',
	'formSelector'        => !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#adminForm',
);

$data['options'] = array_merge($customOptions, $data['options']);

// Load search tools
HTMLHelper::_('searchtools.form', $data['options']['formSelector'], $data['options']);

$filtersClass = isset($data['view']->activeFilters) && $data['view']->activeFilters ? ' js-stools-container-filters-visible' : '';
?>
<div class="js-stools" role="search">
	<?php
		if ($data['view'] instanceof \Joomla\Component\Localise\Administrator\View\Languages\HtmlView)
	{
		$clientField = $data['view']->filterForm->getField('client');
		$tagField    = $data['view']->filterForm->getField('tag'); ?>

	<?php // Add the itemtype and language selectors before the form filters. ?>
	<?php if ($clientField) : ?>
		<div class="js-stools-container-selector-first">
			<div class="js-stools-field-selector js-stools-client">
				<?php echo $clientField->input; ?>
			</div>
		</div>
	<?php endif; ?>
	<?php if ($tagField) : ?>
		<div class="js-stools-container-selector">
			<div class="js-stools-field-selector js-stools-tag">
				<?php echo $tagField->input; ?>
			</div>
		</div>
	<?php endif; ?>
	<?php
		}
	?>
	<div class="js-stools-container-bar">
		<?php echo LayoutHelper::render('joomla.searchtools.default.bar', $data); ?>
	</div>

	<!-- Filters div -->
	<div class="js-stools-container-filters clearfix<?php echo $filtersClass; ?>">
		<?php echo LayoutHelper::render('joomla.searchtools.default.list', $data); ?>
		<?php echo LayoutHelper::render('joomla.searchtools.default.filters', $data); ?>
	</div>
</div>
