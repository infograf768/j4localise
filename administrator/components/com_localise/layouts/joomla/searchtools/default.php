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

/** @var  array  $displayData */
$data = $displayData;

// Receive overridable options
$data['options']   = !empty($data['options']) ? $data['options'] : array();
$showSelector      = false;
$showFilterButton  = false;
$selectorFieldName = $data['options']['selectorFieldName'] ?? 'client';

if ($data['view'] instanceof \Joomla\Component\Localise\Administrator\View\Languages\HtmlView)
{
	// Client selector doesn't have to activate the filter bar.
	unset($data['view']->activeFilters['client']);

	// Menutype filter doesn't have to activate the filter bar
	unset($data['view']->activeFilters['tag']);
}

	// Checks if the filters button should exist.
	$filters = $data['view']->filterForm->getGroup('filter');
	$showFilterButton = isset($filters['filter_search']) && count($filters) === 1 ? false : true;

	// Checks if it should show the be hidden.
	$hideActiveFilters = empty($data['view']->activeFilters);

// Set some basic options
$customOptions = array(
	'filtersHidden'       => $data['options']['filtersHidden'] ?? empty($data['view']->activeFilters),
	'filterButton'        => isset($data['options']['filterButton']) && $data['options']['filterButton'] ? $data['options']['filterButton'] : $showFilterButton,
	'defaultLimit'        => $data['options']['defaultLimit'] ?? Factory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'selectorFieldName'   => $selectorFieldName,
	'orderFieldSelector'  => '#list_fullordering',
	'formSelector'        => !empty($data['options']['formSelector']) ? $data['options']['formSelector'] : '#adminForm',
);

$data['options'] = array_merge($customOptions, $data['options']);

// Add class to hide the active filters if needed.
$filtersActiveClass = $hideActiveFilters ? '' : ' js-stools-container-filters-visible';

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

	<?php // Add the client and language selectors before the form filters. ?>
	<?php if ($clientField) : ?>
		<div class="js-stools-container-selector">
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
	<div class="js-stools-container-bar ml-auto">
		<div class="btn-toolbar">
			<?php echo $this->sublayout('bar', $data); ?>
			<?php echo $this->sublayout('list', $data); ?>
		</div>
	</div>
		<!-- Filters div -->
	<div class="js-stools-container-filters clearfix<?php echo $filtersActiveClass; ?>">
		<?php if ($data['options']['filterButton']) : ?>
		<?php echo $this->sublayout('filters', $data); ?>
		<?php endif; ?>
	</div>
</div>
