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

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;


//jimport('joomla.html.html');

/**
 * Form Field Ini class.
 *
 * @package     Extensions.Components
 * @subpackage  Localise
 *
 * @since       1.0
 */
class IniField extends FormField
{
	/**
	 * The field type.
	 *
	 * @var    string
	 */
	protected $type = 'Ini';

	/**
	 * Base path for editor files
	 */
	protected $basePath = 'media/vendor/codemirror/';

	/**
	 * Method to get the field input.
	 *
	 * @return  string    The field input.
	 */
	protected function getInput()
	{
		\JHtml::_('behavior.framework');

		// Load Codemirror
		HTMLHelper::_('script', 'vendor/codemirror/lib/codemirror.js', array('version' => 'auto', 'relative' => true));
		HTMLHelper::_('stylesheet', 'vendor/codemirror/lib/codemirror.css', array('version' => 'auto', 'relative' => true));
		HTMLHelper::_('webcomponent', 'system/webcomponents/joomla-editor-codemirror.min.js', array('version' => 'auto', 'relative' => true));

		// Load Joomla language ini parser
		HTMLHelper::_('script', 'com_localise/parseini.js', array('version' => 'auto', 'relative' => true));
		HTMLHelper::_('stylesheet', 'com_localise/localise.css', array('version' => 'auto', 'relative' => true));

		$rows   = (string) $this->element['rows'];
		$cols   = (string) $this->element['cols'];
		$class  = (string) $this->class ? ' class="' . (string) $this->class . '"' : ' class="text_area"';

		$options = new \stdClass;

		$options->mode = 'text/parseini';
		$options->tabMode = 'default';
		$options->smartIndent = true;
		$options->lineNumbers = true;
		$options->lineWrapping = true;
		$options->autoCloseBrackets = true;
		$options->showTrailingSpace = true;
		$options->styleActiveLine = true;
		$options->gutters = array('CodeMirror-linenumbers', 'CodeMirror-foldgutter', 'breakpoints');

		$html = array();
		$html[] = '<textarea' . $class . ' name="' . $this->name . '" id="' . $this->id . '" cols="' . $cols . '" rows="' . $rows . '">'
				. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '</textarea>';
		$html[] = '<script type="text/javascript">';
		$html[] = '(function() {';
		$html[] = '		var editor = CodeMirror.fromTextArea(document.getElementById("' . $this->id . '"), ' . json_encode($options) . ');';
		$html[] = '		editor.setOption("extraKeys", {';
		$html[] = '			"Ctrl-Q": function(cm) {';
		$html[] = '				cm.setOption("fullScreen", !cm.getOption("fullScreen"));';
		$html[] = '			},';
		$html[] = '			"Esc": function(cm) {';
		$html[] = '				if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);';
		$html[] = '			}';
		$html[] = '		});';
		$html[] = '		editor.on("gutterClick", function(cm, n) {';
		$html[] = '			var info = cm.lineInfo(n)';
		$html[] = '			cm.setGutterMarker(n, "breakpoints", info.gutterMarkers ? null : makeMarker())';
		$html[] = '		})';
		$html[] = '		function makeMarker() {';
		$html[] = '			var marker = document.createElement("div")';
		$html[] = '			marker.style.color = "#822"';
		$html[] = '			marker.innerHTML = "●"';
		$html[] = '			return marker';
		$html[] = '		}';
		$html[] = '		Joomla.editors.instances[\'' . $this->id . '\'] = editor;';
		$html[] = '})()';
		$html[] = '</script>';

		return implode("\n", $html);
	}

	/**
	 * Get the save javascript code.
	 *
	 * @return  string
	 */
	public function save()
	{
		return "document.getElementById('" . $this->id . "').value = Joomla.editors.instances['" . $this->id . "'].getValue();\n";
	}
}
