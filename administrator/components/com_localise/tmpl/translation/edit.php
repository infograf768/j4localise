<?php
/**
 * @package     Com_Localise
 * @subpackage  views
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Version;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('stylesheet', 'com_localise/localise.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('jquery.framework');

$parts = explode('-', $this->state->get('translation.reference'));
$src   = $parts[0];
$parts = explode('-', $this->state->get('translation.tag'));
$dest  = $parts[0];

// No use to filter if target language is also reference language
if ($this->state->get('translation.reference') != $this->state->get('translation.tag'))
{
	$istranslation = 1;
}
else
{
	$istranslation = 0;
}

$params            = ComponentHelper::getParams('com_localise');
$ref_tag           = $params->get('reference', 'en-GB');
$allow_develop     = $params->get('gh_allow_develop', 0);
$saved_ref         = $params->get('customisedref', 0);
$source_ref        = $saved_ref;
$istranslation     = $this->item->istranslation;
$installed_version = new Version;
$installed_version = $installed_version->getShortVersion();

	if ($saved_ref == 0)
	{
		$source_ref = $installed_version;
	}

	if ($saved_ref != 0 && $allow_develop == 1 && $ref_tag == 'en-GB' && $istranslation == 0)
	{
		Factory::getApplication()->enqueueMessage(
		Text::sprintf('COM_LOCALISE_NOTICE_EDIT_REFERENCE_HAS_LIMITED_USE', $source_ref),
		'notice');
	}

$input  = Factory::getApplication()->input;
$posted = $input->post->get('jform', array(), 'array');

$has_translatedkeys   = !empty($this->item->translatedkeys) ? 1 : 0;
$has_untranslatedkeys = !empty($this->item->untranslatedkeys) ? 1 : 0;
$has_unchangedkeys    = !empty($this->item->unchangedkeys) ? 1 : 0;
$has_textchangedkeys  = !empty($this->item->textchangedkeys) ? 1 : 0;

if (isset($posted['select']['keystatus'])
	&& !empty($posted['select']['keystatus'])
	&& $posted['select']['keystatus'] != 'allkeys'
	)
{
	$filter       = $posted['select']['keystatus'];
	$keystofilter = array ($this->item->$filter);
	$tabchoised   = 'strings';
}
elseif (empty($posted['select']['keystatus']))
{
	$filter       = 'allkeys';
	$keystofilter = array();
	$tabchoised   = 'default';
}
else
{
	$filter       = 'allkeys';
	$keystofilter = array();
	$tabchoised   = 'default';
}

$fieldSets = $this->form->getFieldsets();
$sections  = $this->form->getFieldsets('strings');
$ftpSets   = $this->formftp->getFieldsets();

if ($istranslation)
{
	// Only add the JS realted with the filters or others only showed at 'istranslation' case
	Factory::getDocument()->addScriptDeclaration("
		function returnAll()
		{
			$('.return').trigger('click');
		}

		(function($){
			$(document).ready(function() {
				var has_translatedkeys   = " . $has_translatedkeys . ";
				var has_untranslatedkeys = " . $has_untranslatedkeys . ";
				var has_unchangedkeys    = " . $has_unchangedkeys . ";
				var has_textchangedkeys  = " . $has_textchangedkeys . ";

				if (has_translatedkeys == '0')
				{
					var x = document.getElementById('jform_select_keystatus').options[2].disabled = true;
				}

				if (has_untranslatedkeys == '0')
				{
					var x = document.getElementById('jform_select_keystatus').options[3].disabled = true;
				}

				if (has_unchangedkeys == '0')
				{
					var x = document.getElementById('jform_select_keystatus').options[4].disabled = true;
				}

				if (has_textchangedkeys == '0')
				{
					var x = document.getElementById('jform_select_keystatus').options[5].disabled = true;
				}
			});
		})(jQuery);
	");
}
else
{
	Factory::getDocument()->addScriptDeclaration("
		function returnAll()
		{
			$('.return').trigger('click');
		}
	");
}
?>
<form action="" method="post" name="adminForm" id="localise-translation-form" class="form-validate">
	<div class="row">
		<!-- Begin Localise Translation -->
		<div class="col-md-12 form-horizontal">
				<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => $this->ftp ? 'ftp' : $tabchoised)); ?>
					<?php if ($this->ftp) : ?>
						<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'ftp', Text::_($ftpSets['ftp']->label, true)); ?>
							<?php if (!empty($ftpSets['ftp']->description)):?>
								<p class="tip"><?php echo Text::_($ftpSets['ftp']->description); ?></p>
							<?php endif;?>
							<?php if ($this->ftp instanceof Exception): ?>
								<p class="error"><?php echo Text::_($this->ftp->message); ?></p>
							<?php endif; ?>
							<?php foreach($this->formftp->getFieldset('ftp',false) as $field) : ?>
								<?php echo $field->renderField(); ?>
							<?php endforeach; ?>
						<?php echo HTMLHelper::_('uitab.endTab'); ?>
					<?php endif; ?>
					<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'default', Text::_($fieldSets['default']->label, true)); ?>
						<?php if (!empty($fieldSets['default']->description)) : ?>
							<p class="alert alert-info"><?php echo Text::_($fieldSets['default']->description); ?></p>
						<?php endif;?>
						<?php foreach($this->form->getFieldset('default') as $field) : ?>
							<?php echo $field->renderField(); ?>
						<?php endforeach; ?>
					<?php echo HTMLHelper::_('uitab.endTab'); ?>
					<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'strings', Text::_('COM_LOCALISE_FIELDSET_TRANSLATION_STRINGS')); ?>
						<div class="alert alert-info">
							<span class="fas fa-info-circle info-line" aria-hidden="true"</span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
							<?php echo Text::_('COM_LOCALISE_TRANSLATION_NOTICE'); ?>
						</div>
						<?php echo HTMLHelper::_('bootstrap.startAccordion', 'slide-legend', array('active' => '')); ?>
						<?php echo HTMLHelper::_('bootstrap.addSlide', 'slide-legend', Text::_($fieldSets['legend']->label), 'legend'); ?>
							<div>
								<p class="tip"><?php echo Text::_('COM_LOCALISE_LABEL_TRANSLATION_KEY'); ?></p>
								<ul class="adminformlist">
									<?php foreach($this->form->getFieldset('legend') as $field) : ?>
										<li>
											<?php echo $field->input; ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
						<?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>
						<div class="key">
							<div id="translationbar">
								<?php if ($istranslation) : ?>
									<div class="pull-left">
										<?php foreach($this->form->getFieldset('select') as $field): ?>
											<?php if ($field->type != "Spacer") : ?>
												<?php
													$field->value = $filter;
													echo Text::_('JSEARCH_FILTER_LABEL');
													echo $field->input;
												?>
											<?php else : ?>
												<?php echo $field->label; ?>
											<?php endif; ?>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
								<a href="javascript:void(0);" class="btn btn-small" onclick="returnAll();">
									<i class="icon-reset"></i> <?php echo Text::_('COM_LOCALISE_BUTTON_RESET_ALL');?>
								</a>
							</div>
							<?php
							if (count($sections) > 1) :
									echo '<div class="clearfix"></div>';
									echo HTMLHelper::_('bootstrap.startAccordion', 'localise-translation-sliders');
									$i = 0;
									foreach ($sections as $name => $fieldSet) :
										echo HTMLHelper::_('bootstrap.addSlide', 'localise-translation-sliders', Text::_($fieldSet->label), 'collapse' . $i++);
									?>
										<ul class="adminformlist">
											<?php foreach ($this->form->getFieldset($name) as $field) : ?>
												<?php
													$showkey = 0;

													if ($filter != 'allkeys' && !empty($keystofilter))
													{
														foreach ($keystofilter as $data => $ids)
														{
															foreach ($ids as $keytofilter)
															{
																$showkey = 0;
																$pregkey = preg_quote('<strong>'. $keytofilter .'</strong>', '/<>');

																if (preg_match("/$pregkey/", $field->label))
																{
																	$showkey = 1;
																		break;
																}
															}
														}

														if ($showkey == '1')
														{
												?>
															<li>
																<?php echo $field->label; ?>
																<?php echo $field->input; ?>
															</li>
														<?php
														}
														else
														{
														?>
															<div style="display:none;">
																<?php echo $field->label; ?>
																<?php echo $field->input; ?>
															</div>
														<?php
														}
													}
													elseif ($filter == 'allkeys')
													{
													?>
														<li>
															<?php echo $field->label; ?>
															<?php echo $field->input; ?>
														</li>
													<?php
													}
													?>
											<?php endforeach; ?>
										</ul>
									<?php
									echo HTMLHelper::_('bootstrap.endSlide');
									endforeach;
									echo HTMLHelper::_('bootstrap.endAccordion');
									?>
								<?php else : ?>
									<ul class="adminformlist">
										<?php $sections = array_keys($sections); ?>
										<?php foreach ($this->form->getFieldset($sections[0]) as $field) : ?>
										<?php
											$showkey = 0;

											if ($filter != 'allkeys' && !empty($keystofilter))
											{
												foreach ($keystofilter as $data  => $ids)
												{
													foreach ($ids as $keytofilter)
													{
														$showkey = 0;
														$pregkey = preg_quote('<strong>'.$keytofilter.'</strong>', '/<>');

														if (preg_match("/$pregkey/", $field->label))
														{
															$showkey = 1;
															break;
														}
													}
												}

												if ($showkey == '1')
												{
												?>
													<li>
														<?php echo $field->label; ?>
														<?php echo $field->input; ?>
													</li>
												<?php
												}
												else
												{
												?>
													<div style="display:none;">
														<?php echo $field->label; ?>
														<?php echo $field->input; ?>
													</div>
												<?php
												}
											}
											elseif ($filter == 'allkeys')
											{
											?>
												<li>
													<?php echo $field->label; ?>
													<?php echo $field->input; ?>
												</li>
											<?php
											}
											?>
										<?php endforeach; ?>
									</ul>
								<?php endif;?>
						</div>

					<?php echo HTMLHelper::_('uitab.endTab'); ?>
					<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'permissions', Text::_($fieldSets['permissions']->label, true)); ?>
						<?php if (!empty($fieldSets['permissions']->description)):?>
							<p class="tip"><?php echo Text::_($fieldSets['permissions']->description); ?></p>
						<?php endif;?>
						<?php foreach($this->form->getFieldset('permissions') as $field) : ?>
							<div class="control-group form-vertical">
								<div class="controls">
									<?php echo $field->input; ?>
								</div>
							</div>
						<?php endforeach; ?>
					<?php echo HTMLHelper::_('uitab.endTab'); ?>
				<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

		</div>
		<!-- End Localise Translation -->
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="notinref" value="" />
		<?php echo HTMLHelper::_('form.token'); ?>

	</div>
</form>
